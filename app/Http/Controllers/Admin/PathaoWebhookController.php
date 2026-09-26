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

        // Status mapping: 6 = Delivered/Completed, 11 = Cancelled, 7 = Returned, 5 = In Courier
        switch ($event) {
            case 'order.delivered':
                $this->webhookOrders->applyStatusChange($order, 6, 'Pathao');
                if (!empty($payload['delivery_fee'])) {
                    $order->delivery_charge = $payload['delivery_fee'];
                }
                break;

            case 'order.partial_delivery':
            case 'order.partial-delivery':
                $this->webhookOrders->applyStatusChange($order, 6, 'Pathao');
                break;

            case 'order.return':
            case 'order.returned':
            case 'order.paid_return':
            case 'order.returned_to_merchant':
            case 'returned_to_merchant':
            case 'return.in_transit':
            case 'order.delivery_failed':
            case 'order.pickup_cancelled':
                $this->webhookOrders->applyStatusChange($order, 11, 'Pathao');
                break;

            case 'order.pickup':
            case 'order.in_transit':
            case 'order.assigned_for_delivery':
            case 'order.at_the_sorting_hub':
            case 'order.received_at_last_mile_hub':
            case 'order.assigned_for_pickup':
            case 'order.pickup_requested':
            case 'order.created':
            case 'order.updated':
                // Courier in transit status if not already completed/cancelled
                if ($order->order_status != 6 && $order->order_status != 11 && $order->order_status != 7) {
                    $this->webhookOrders->applyStatusChange($order, 5, 'Pathao');
                }
                break;
        }

        $order->save();

        Log::info('Pathao Webhook Processed for Order', [
            'order_id'       => $order->id,
            'invoice_id'     => $order->invoice_id,
            'event'          => $event,
            'current_status' => $order->order_status,
        ]);
    }
}
