<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courierapi;
use App\Models\Order;
use App\Services\CourierWebhookOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PathaoWebhookController extends Controller
{
    public function __construct(
        private readonly CourierWebhookOrderService $webhookOrders
    ) {}

    /**
     * Handle incoming webhook requests from Pathao Courier
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event   = strtolower(trim((string) ($request->input('event') ?? ($payload['event'] ?? ''))));

        $pathaoConfig     = Courierapi::where('type', 'pathao')->first();
        $configuredSecret = $pathaoConfig ? ($pathaoConfig->webhook_secret ?? $pathaoConfig->token) : null;

        // Pathao sends secret in X-PATHAO-Signature or X-Pathao-Signature
        $incomingSecret = $request->header('X-PATHAO-Signature')
            ?? $request->header('x-pathao-signature')
            ?? $request->header('X-Pathao-Merchant-Webhook-Integration-Secret')
            ?? $request->query('token')
            ?? $request->query('secret');

        Log::info('Pathao Webhook Received', [
            'event'   => $event,
            'ip'      => $request->ip(),
            'headers' => [
                'x-pathao-signature' => $incomingSecret,
            ],
            'payload' => $payload,
        ]);

        $echoSecret = $incomingSecret ?: ($configuredSecret ?: 'f3992ecc-59da-4cbe-a049-a13da2018d51');

        // Auto-save incoming secret if missing in DB
        if ($pathaoConfig && !empty($incomingSecret) && empty($pathaoConfig->webhook_secret)) {
            $pathaoConfig->webhook_secret = $incomingSecret;
            $pathaoConfig->save();
        }

        // Test handshake / integration ping
        if ($request->isMethod('get') || empty($event) || $event === 'test' || $event === 'webhook.integration' || $event === 'ping') {
            return response()->json([
                'status'  => 'success',
                'message' => 'Pathao webhook endpoint is reachable and active.',
                'event'   => $event ?: 'webhook.integration',
            ], 200)
            ->header('X-Pathao-Merchant-Webhook-Integration-Secret', $echoSecret)
            ->header('X-PATHAO-Signature', $echoSecret);
        }

        // Process order status update
        $this->processOrderEvent($payload, $event);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pathao event processed successfully.',
            'event'   => $event,
        ], 200)
        ->header('X-Pathao-Merchant-Webhook-Integration-Secret', $echoSecret)
        ->header('X-PATHAO-Signature', $echoSecret);
    }

    /**
     * Map and process Pathao status updates
     */
    protected function processOrderEvent(array $payload, string $event): void
    {
        $merchantOrderId = $payload['merchant_order_id'] ?? null;
        $consignmentId   = $payload['consignment_id'] ?? null;

        if (!$merchantOrderId && !$consignmentId) {
            Log::warning('Pathao Webhook: No order identifier found in payload', ['payload' => $payload]);
            return;
        }

        // Find Order
        $order = null;
        if ($merchantOrderId) {
            $order = Order::where('invoice_id', (string) $merchantOrderId)
                ->orWhere('id', (string) $merchantOrderId)
                ->first();
        }

        if (!$order && $consignmentId) {
            $order = Order::where('courier_tracking_id', (string) $consignmentId)
                ->orWhere('consignment_id', (string) $consignmentId)
                ->orWhere('courier_tracking_code', (string) $consignmentId)
                ->first();
        }

        if (!$order) {
            Log::warning('Pathao Webhook: Order not found in database', [
                'merchant_order_id' => $merchantOrderId,
                'consignment_id'   => $consignmentId,
            ]);
            return;
        }

        // Ensure courier type & tracking ID
        $order->courier_type = 'pathao';
        if ($consignmentId && empty($order->courier_tracking_id)) {
            $order->courier_tracking_id = (string) $consignmentId;
            $order->consignment_id      = (string) $consignmentId;
        }

        // Append note for event
        $note = "Pathao: " . str_replace(['order.', '_', '.'], [' ', ' ', ' '], $event);
        if (!empty($payload['updated_at'])) {
            $note .= " (" . $payload['updated_at'] . ")";
        }
        $this->webhookOrders->appendCourierNote($order, $note);
        $order->refresh();

        // Status mapping using CourierStatusMapping:
        // 7 = Delivered, 8 = Pending Partial, 12 = Pending Return, 6 = In Courier
        $newStatusId = \App\Support\CourierStatusMapping::map('pathao', $event);

        if (!empty($payload['delivery_fee'])) {
            $order->delivery_charge = $payload['delivery_fee'];
            $order->save();
        }

        if ($newStatusId !== null) {
            $this->webhookOrders->applyStatusChange($order, $newStatusId, 'Pathao');
        }

        Log::info('Pathao Webhook Processed for Order', [
            'order_id'       => $order->id,
            'invoice_id'     => $order->invoice_id,
            'event'          => $event,
            'mapped_status'  => $newStatusId,
            'current_status' => $order->order_status,
        ]);
    }
}
