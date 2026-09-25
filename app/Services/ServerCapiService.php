<?php

namespace App\Services;

use App\Models\Order;
use App\Support\EcommerceTrackingUser;
use Illuminate\Support\Facades\Log;

class ServerCapiService
{
    /**
     * Track ViewContent event on Server-side (Meta CAPI + TikTok CAPI).
     */
    public static function trackViewContent(array $data, array $userData = [], ?string $eventId = null, ?string $sourceUrl = null, ?string $testEventCode = null): void
    {
        try {
            $eventId = $eventId ?: ('vc_' . ($data['content_ids'][0] ?? 'p') . '_' . time() . '_' . mt_rand(100, 999));
            $url = $sourceUrl ?: request()->fullUrl();
            $value = (float) ($data['value'] ?? 0);
            $contentIds = array_map('strval', (array) ($data['content_ids'] ?? []));
            $contents = $data['contents'] ?? [];

            if (empty($contents) && !empty($contentIds)) {
                $contents = [[
                    'id'         => (string) $contentIds[0],
                    'quantity'   => 1,
                    'item_price' => $value,
                ]];
            }

            $user = EcommerceTrackingUser::forCapi($userData);

            // 1. Meta CAPI
            try {
                app(FacebookCapiService::class)->sendEvent('ViewContent', [
                    'currency'         => 'BDT',
                    'value'            => $value,
                    'content_ids'      => $contentIds,
                    'content_name'     => (string) ($data['content_name'] ?? ''),
                    'content_category' => (string) ($data['content_category'] ?? ''),
                    'content_type'     => 'product',
                    'contents'         => $contents,
                ], $user, [
                    'event_id'         => $eventId,
                    'event_source_url' => $url,
                    'test_event_code'  => $testEventCode,
                ]);
            } catch (\Throwable $e) {
                Log::warning('ServerCapi FB ViewContent error: ' . $e->getMessage());
            }

            // 2. TikTok CAPI
            try {
                $ttContents = array_map(fn($c) => [
                    'id'         => (string) ($c['id'] ?? ''),
                    'name'       => (string) ($c['name'] ?? ($data['content_name'] ?? '')),
                    'quantity'   => (int) ($c['quantity'] ?? 1),
                    'item_price' => (float) ($c['item_price'] ?? $value),
                ], $contents);

                app(TikTokCapiService::class)->sendEvent('ViewContent', [
                    'currency'     => 'BDT',
                    'value'        => $value,
                    'content_type' => 'product',
                    'content_id'   => $contentIds[0] ?? '',
                    'contents'     => $ttContents,
                ], $user, [
                    'event_id'         => $eventId,
                    'event_source_url' => $url,
                    'test_event_code'  => $testEventCode,
                ]);
            } catch (\Throwable $e) {
                Log::warning('ServerCapi TikTok ViewContent error: ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('ServerCapi trackViewContent exception: ' . $e->getMessage());
        }
    }

    /**
     * Track AddToCart event on Server-side (Meta CAPI + TikTok CAPI).
     */
    public static function trackAddToCart(array $data, array $userData = [], ?string $eventId = null, ?string $sourceUrl = null, ?string $testEventCode = null): void
    {
        try {
            $eventId = $eventId ?: ('atc_' . time() . '_' . mt_rand(100, 999));
            $url = $sourceUrl ?: request()->fullUrl();
            $value = (float) ($data['value'] ?? 0);
            $contentIds = array_map('strval', (array) ($data['content_ids'] ?? []));
            $contents = $data['contents'] ?? [];

            $user = EcommerceTrackingUser::forCapi($userData);

            // 1. Meta CAPI
            try {
                app(FacebookCapiService::class)->sendEvent('AddToCart', [
                    'currency'     => 'BDT',
                    'value'        => $value,
                    'content_ids'  => $contentIds,
                    'content_name' => (string) ($data['content_name'] ?? ''),
                    'content_type' => 'product',
                    'contents'     => $contents,
                ], $user, [
                    'event_id'         => $eventId,
                    'event_source_url' => $url,
                    'test_event_code'  => $testEventCode,
                ]);
            } catch (\Throwable $e) {
                Log::warning('ServerCapi FB AddToCart error: ' . $e->getMessage());
            }

            // 2. TikTok CAPI
            try {
                $ttContents = array_map(fn($c) => [
                    'id'         => (string) ($c['id'] ?? ''),
                    'name'       => (string) ($c['name'] ?? ($data['content_name'] ?? '')),
                    'quantity'   => (int) ($c['quantity'] ?? 1),
                    'item_price' => (float) ($c['item_price'] ?? 0),
                ], $contents);

                app(TikTokCapiService::class)->sendEvent('AddToCart', [
                    'currency'     => 'BDT',
                    'value'        => $value,
                    'content_type' => 'product',
                    'contents'     => $ttContents,
                ], $user, [
                    'event_id'         => $eventId,
                    'event_source_url' => $url,
                    'test_event_code'  => $testEventCode,
                ]);
            } catch (\Throwable $e) {
                Log::warning('ServerCapi TikTok AddToCart error: ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('ServerCapi trackAddToCart exception: ' . $e->getMessage());
        }
    }

    /**
     * Track InitiateCheckout event on Server-side (Meta CAPI + TikTok CAPI).
     */
    public static function trackInitiateCheckout(array $data, array $userData = [], ?string $eventId = null, ?string $sourceUrl = null, ?string $testEventCode = null): void
    {
        try {
            $eventId = $eventId ?: ('ic_' . time() . '_' . mt_rand(100, 999));
            $url = $sourceUrl ?: request()->fullUrl();
            $value = (float) ($data['value'] ?? 0);
            $contentIds = array_map('strval', (array) ($data['content_ids'] ?? []));
            $contents = $data['contents'] ?? [];

            $user = EcommerceTrackingUser::forCapi($userData);

            // 1. Meta CAPI
            try {
                app(FacebookCapiService::class)->sendEvent('InitiateCheckout', [
                    'currency'     => 'BDT',
                    'value'        => $value,
                    'content_ids'  => $contentIds,
                    'num_items'    => count($contents) ?: count($contentIds),
                    'content_type' => 'product',
                    'contents'     => $contents,
                ], $user, [
                    'event_id'         => $eventId,
                    'event_source_url' => $url,
                    'test_event_code'  => $testEventCode,
                ]);
            } catch (\Throwable $e) {
                Log::warning('ServerCapi FB InitiateCheckout error: ' . $e->getMessage());
            }

            // 2. TikTok CAPI
            try {
                $ttContents = array_map(fn($c) => [
                    'id'         => (string) ($c['id'] ?? ''),
                    'name'       => (string) ($c['name'] ?? ''),
                    'quantity'   => (int) ($c['quantity'] ?? 1),
                    'item_price' => (float) ($c['item_price'] ?? 0),
                ], $contents);

                app(TikTokCapiService::class)->sendEvent('InitiateCheckout', [
                    'currency'     => 'BDT',
                    'value'        => $value,
                    'content_type' => 'product',
                    'contents'     => $ttContents,
                ], $user, [
                    'event_id'         => $eventId,
                    'event_source_url' => $url,
                    'test_event_code'  => $testEventCode,
                ]);
            } catch (\Throwable $e) {
                Log::warning('ServerCapi TikTok InitiateCheckout error: ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('ServerCapi trackInitiateCheckout exception: ' . $e->getMessage());
        }
    }

    /**
     * Track Purchase event simultaneously across Facebook CAPI and TikTok Events API (CAPI).
     */
    public static function trackPurchase($order, array $userData = [], $payment = null, ?string $sourceUrl = null): void
    {
        try {
            $orderDetails = ($order->relationLoaded('orderdetails') && $order->orderdetails && $order->orderdetails->isNotEmpty())
                ? $order->orderdetails
                : \App\Models\OrderDetails::where('order_id', $order->id)->get();

            $contentIds = $orderDetails->pluck('product_id')->map(fn ($id) => (string) $id)->values()->toArray();

            // Meta CAPI format: id, quantity, item_price
            $fbContents = $orderDetails->map(fn ($i) => [
                'id'         => (string) $i->product_id,
                'quantity'   => (int) $i->qty,
                'item_price' => (float) $i->sale_price,
            ])->values()->toArray();

            // TikTok format: id, name, quantity, item_price
            $ttContents = $orderDetails->map(fn ($i) => [
                'id'         => (string) $i->product_id,
                'name'       => (string) ($i->product_name ?? ''),
                'quantity'   => (int) $i->qty,
                'item_price' => (float) $i->sale_price,
            ])->values()->toArray();

            $invoiceId = (string) ($order->invoice_id ?? $order->id);
            $eventId = 'purchase_' . $invoiceId;
            $amount = (float) ($payment->amount ?? $order->amount ?? 0);
            $url = $sourceUrl ?: url('customer/order-success/' . $order->id);

            $user = !empty($userData) ? EcommerceTrackingUser::forCapi($userData) : EcommerceTrackingUser::fromOrder($order);

            // 1. Meta (Facebook) CAPI
            try {
                app(FacebookCapiService::class)->sendEvent('Purchase', [
                    'currency'     => 'BDT',
                    'value'        => $amount,
                    'order_id'     => $invoiceId,
                    'content_ids'  => $contentIds,
                    'contents'     => $fbContents,
                    'num_items'    => count($fbContents),
                    'content_type' => 'product',
                ], $user, [
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
                    'contents' => $ttContents,
                ], $user, [
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
