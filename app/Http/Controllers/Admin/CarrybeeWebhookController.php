<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courierapi;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarrybeeWebhookController extends Controller
{
    public function __construct(
        private readonly \App\Services\CourierWebhookOrderService $webhookOrders
    ) {}

    /**
     * Handle incoming webhook requests from Carrybee
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $event = $request->input('event') ?? ($payload['event'] ?? null);

        // Header containing the integration secret
        $integrationHeader = $request->header('X-CB-Webhook-Integration-Header') 
            ?? $request->header('x-cb-webhook-integration-header');

        $queryToken = $request->query('token');

        Log::info('Carrybee Webhook Received', [
            'event'   => $event,
            'headers' => [
                'x-cb-webhook-integration-header' => $integrationHeader,
            ],
            'query'   => $request->query(),
            'payload' => $payload,
        ]);

        $carrybee = Courierapi::where('type', 'carrybee')->first();
        $configuredSecret = $carrybee ? ($carrybee->webhook_secret ?? $carrybee->token) : null;

        // 1. Integration Verification Event
        if ($event === 'webhook.integration' || empty($event)) {
            $secretEcho = $integrationHeader ?: ($configuredSecret ?: $queryToken ?: '40489fe0-9386-4fc9-8e92-2b2fcb9d451c');
            
            // Save webhook secret if not saved yet
            if ($carrybee && !empty($integrationHeader) && empty($carrybee->webhook_secret)) {
                $carrybee->webhook_secret = $integrationHeader;
                $carrybee->save();
            }

            return response()->json([
                'error'   => false,
                'message' => 'Webhook integrated successfully',
                'event'   => 'webhook.integration'
            ], 202)->header('X-CB-Webhook-Integration-Header', $secretEcho);
        }

        // 2. Secret Verification (if configured)
        if ($configuredSecret) {
            $incomingSecret = $integrationHeader ?: $queryToken;
            if ($incomingSecret && $incomingSecret !== $configuredSecret) {
                Log::warning('Carrybee Webhook: Invalid Secret', [
                    'expected' => $configuredSecret,
                    'received' => $incomingSecret
                ]);
                return response()->json(['error' => true, 'message' => 'Unauthorized webhook secret'], 401);
            }
        }

        // 3. Process Order Event
        $this->processOrderEvent($payload, $event);

        $responseSecret = $integrationHeader ?: ($configuredSecret ?: $queryToken ?: '');
        return response()->json([
            'error'   => false,
            'message' => 'Event processed successfully',
            'event'   => $event
        ], 200)->header('X-CB-Webhook-Integration-Header', $responseSecret);
    }

    /**
     * Map and process status updates
     */
    protected function processOrderEvent(array $payload, ?string $event)
    {
        $merchantOrderId = $payload['merchant_order_id'] ?? null;
        $consignmentId   = $payload['consignment_id'] ?? null;

        if (!$merchantOrderId && !$consignmentId) {
            Log::warning('Carrybee Webhook: No order identifier found in payload', ['payload' => $payload]);
            return;
        }

        // Find Order
        $order = null;
        if ($merchantOrderId) {
            $order = Order::where('invoice_id', (string) $merchantOrderId)->orWhere('id', (string) $merchantOrderId)->first();
        }

        if (!$order && $consignmentId) {
            $order = Order::where('courier_tracking_id', (string) $consignmentId)
                ->orWhere('consignment_id', (string) $consignmentId)
                ->first();
        }

        if (!$order) {
            Log::warning('Carrybee Webhook: Order not found in database', [
                'merchant_order_id' => $merchantOrderId,
                'consignment_id'   => $consignmentId,
            ]);
            return;
        }

        // Ensure courier type and tracking ID are set
        $order->courier_type = 'carrybee';
        if ($consignmentId && empty($order->courier_tracking_id)) {
            $order->courier_tracking_id = (string) $consignmentId;
            $order->consignment_id = (string) $consignmentId;
            $order->save();
        }

        $notes = [];
        if (!empty($payload['reason'])) {
            $notes[] = 'কারণ: ' . $payload['reason'];
        }
        if (!empty($payload['remarks'])) {
            $notes[] = 'মন্তব্য: ' . $payload['remarks'];
        }
        if (!empty($payload['agent_name'])) {
            $notes[] = 'ডেলিভারি রাইডার: ' . $payload['agent_name'] . (!empty($payload['agent_phone']) ? ' (' . $payload['agent_phone'] . ')' : '');
        }

        if (!empty($notes)) {
            $this->webhookOrders->appendCourierNote($order, implode(' | ', $notes), 'Carrybee');
            $order->refresh();
        }

        // Status mapping using CourierStatusMapping:
        // 7 = Delivered, 8 = Pending Partial, 12 = Pending Return, 6 = In Courier
        $newStatusId = \App\Support\CourierStatusMapping::map('carrybee', $event);

        if ($newStatusId !== null) {
            $this->webhookOrders->applyStatusChange($order, $newStatusId, 'Carrybee');
        }

        Log::info('Carrybee Webhook Processed for Order', [
            'order_id'       => $order->id,
            'invoice_id'     => $order->invoice_id,
            'event'          => $event,
            'mapped_status'  => $newStatusId,
            'current_status' => $order->order_status,
        ]);
    }
}
