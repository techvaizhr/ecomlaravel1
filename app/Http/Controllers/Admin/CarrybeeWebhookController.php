<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courierapi;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarrybeeWebhookController extends Controller
{
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
            $order = Order::where('invoice_id', $merchantOrderId)->first();
        }

        if (!$order && $consignmentId) {
            $order = Order::where('courier_tracking_id', $consignmentId)
                ->orWhere('consignment_id', $consignmentId)
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

        $oldStatus = $order->order_status;
        $statusChanged = false;

        switch ($event) {
            case 'order.delivered':
                $order->order_status = 6; // Delivered / Completed
                $statusChanged = true;
                if (!empty($payload['collected_amount'])) {
                    $order->payment_status = 'paid';
                }
                break;

            case 'order.partial-delivery':
                $order->order_status = 6; // Delivered
                $statusChanged = true;
                break;

            case 'order.returned':
            case 'order.paid-return':
            case 'order.returned-to-merchant':
            case 'order.returned-at-sorting':
            case 'order.returned-in-transit':
                $order->order_status = 7; // Returned
                $statusChanged = true;
                break;

            case 'order.pickup-cancelled':
            case 'order.delivery-failed':
                // Do not mark as delivered, status 5 or 7 depending on policy
                break;

            case 'order.picked':
            case 'order.in-transit':
            case 'order.assigned-for-delivery':
            case 'order.at-the-sorting-hub':
            case 'order.on-the-way-to-central-warehouse':
            case 'order.at-central-warehouse':
            case 'order.received-at-last-mile-hub':
            case 'order.pickup-requested':
            case 'order.assigned-for-pickup':
                if ($order->order_status != 6 && $order->order_status != 7) {
                    $order->order_status = 5; // In Courier
                    $statusChanged = true;
                }
                break;

            case 'order.paid':
                $order->payment_status = 'paid';
                break;
        }

        if (!empty($notes)) {
            $existingNote = $order->admin_note ?? '';
            $appendNote = implode(' | ', $notes) . ' [' . now()->format('d M h:i A') . ']';
            $order->admin_note = $existingNote ? $existingNote . "\n" . $appendNote : $appendNote;
        }

        $order->save();

        Log::info('Carrybee Webhook Processed for Order', [
            'order_id'       => $order->id,
            'invoice_id'     => $order->invoice_id,
            'event'          => $event,
            'old_status'     => $oldStatus,
            'new_status'     => $order->order_status,
            'status_changed' => $statusChanged,
        ]);
    }
}
