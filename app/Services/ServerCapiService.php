<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ServerCapiService
{
    /**
     * Track Purchase event simultaneously across Facebook CAPI and TikTok Events API (CAPI).
     *
     * @param Order|object $order
     * @param array $userData Customer data (email, phone, fbp, fbc, ttclid, etc.)
     * @param object|null $payment
     * @param string|null $sourceUrl
     */
    public static function trackPurchase($order, array $userData = [], $payment = null, ?string $sourceUrl = null): void
    {
        try {
            $orderDetails = $order->orderdetails ?? Order::with('orderdetails')->find($order->id)?->orderdetails ?? collect();
            $contentIds = $orderDetails->pluck('product_id')->map(fn ($id) => (string) $id)->values()->toArray();
            $contents = $orderDetails->map(fn ($i) => [
                'id'         => (string) $i->product_id,
                'name'       => (string) ($i->product_name ?? ''),
                'quantity'   => (int) $i->qty,
                'item_price' => (float) $i->sale_price,
            ])->values()->toArray();

            $invoiceId = (string) ($order->invoice_id ?? $order->id);
            $eventId = 'purchase_' . $invoiceId;
            $amount = (float) ($payment->amount ?? $order->amount ?? 0);
            $url = $sourceUrl ?: url('customer/order-success/' . $order->id);

            // 1. Meta (Facebook) CAPI
            try {
                app(FacebookCapiService::class)->sendEvent('Purchase', [
                    'currency'     => 'BDT',
                    'value'        => $amount,
                    'order_id'     => $invoiceId,
                    'content_ids'  => $contentIds,
                    'contents'     => $contents,
                    'num_items'    => count($contents),
                    'content_type' => 'product',
                ], $userData, [
                    'event_id'         => $eventId,
                    'event_source_url' => $url,
                ]);
            } catch (\Throwable $e) {
                Log::error('ServerCapi FB Purchase error for order ' . $order->id . ': ' . $e->getMessage());
            }

            // 2. TikTok CAPI (Events API)
            try {
                app(TikTokCapiService::class)->sendEvent('CompletePayment', [
                    'currency' => 'BDT',
                    'value'    => $amount,
                    'order_id' => $invoiceId,
                    'contents' => $contents,
                ], $userData, [
                    'event_id'         => $eventId,
                    'event_source_url' => $url,
                ]);
            } catch (\Throwable $e) {
                Log::error('ServerCapi TikTok Purchase error for order ' . $order->id . ': ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('ServerCapi trackPurchase exception for order ' . ($order->id ?? 'unknown') . ': ' . $e->getMessage());
        }
    }
}
