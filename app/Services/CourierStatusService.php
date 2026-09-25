<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Courierapi;
use App\Support\SteadfastWebhookStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourierStatusService
{
    /**
     * Get public tracking URL for an order based on courier type and tracking ID.
     */
    public static function getTrackingUrl(Order $order): ?string
    {
        $trackingId = $order->courier_tracking_id ?: ($order->consignment_id ?: null);
        if (!$trackingId) {
            return null;
        }

        $courierType = strtolower((string) ($order->courier_type ?: 'steadfast'));

        if ($courierType === 'pathao') {
            return 'https://merchant.pathao.com/public-tracking?consignment_id=' . urlencode((string) $trackingId);
        }

        if ($courierType === 'steadfast') {
            $code = trim((string) ($order->courier_tracking_code ?: $trackingId));
            if ($code === '') {
                return null;
            }

            // If it is already a full URL
            if (str_starts_with($code, 'http://') || str_starts_with($code, 'https://')) {
                return $code;
            }

            // If it starts with tl/ or t/
            if (str_starts_with($code, 'tl/') || str_starts_with($code, 't/')) {
                return 'https://steadfast.com.bd/' . $code;
            }

            // 32-character mixed-case token (e.g. WNuuS5Zkqlp5O9NpvwqNJeAECjaYIJMb)
            if (strlen($code) >= 28 && preg_match('/[a-z]/', $code) && preg_match('/[A-Z]/', $code)) {
                return 'https://steadfast.com.bd/tl/' . urlencode($code);
            }

            // If it's a numeric consignment ID only, or standard code
            if (ctype_digit($code)) {
                return 'https://steadfast.com.bd/t/' . urlencode($code);
            }

            // Standard /tl/ token format for Steadfast public tracking
            if (str_starts_with(strtoupper($code), 'SFR') || str_starts_with(strtoupper($code), 'SF')) {
                return 'https://steadfast.com.bd/t/' . urlencode($code);
            }

            return 'https://steadfast.com.bd/tl/' . urlencode($code);
        }

        if ($courierType === 'redx') {
            return 'https://redx.com.bd/track/' . urlencode((string) $trackingId);
        }

        if ($courierType === 'paperfly') {
            return 'https://paperfly.com.bd/track?track_id=' . urlencode((string) $trackingId);
        }

        return null;
    }

    /**
     * Get human-readable courier display name.
     */
    public static function getCourierName(Order $order): string
    {
        $type = strtolower((string) ($order->courier_type ?: 'steadfast'));
        return match ($type) {
            'steadfast' => 'Steadfast',
            'pathao'    => 'Pathao',
            'redx'      => 'RedX',
            'paperfly'  => 'Paperfly',
            default     => ucfirst($type),
        };
    }

    /**
     * Check live courier status for an order, update order_status in DB if needed, and return status details.
     */
    public static function syncOrderStatus(Order $order): array
    {
        $courierType = strtolower((string) ($order->courier_type ?: 'steadfast'));
        $trackingId  = $order->courier_tracking_id ?: ($order->consignment_id ?: null);

        if (!$trackingId) {
            return [
                'success' => false,
                'message' => 'অর্ডারে কোনো কুরিয়ার ট্র্যাকিং বা কনসাইনমেন্ট আইডি পাওয়া যায়নি।',
            ];
        }

        try {
            if ($courierType === 'pathao') {
                return self::syncPathaoStatus($order);
            } elseif ($courierType === 'steadfast') {
                return self::syncSteadfastStatus($order);
            } elseif ($courierType === 'redx') {
                return self::syncRedXStatus($order);
            }

            return [
                'success' => false,
                'message' => "অসমর্থিত কুরিয়ার টাইপ: {$courierType}",
            ];
        } catch (\Throwable $e) {
            Log::error('Courier status sync error', [
                'order_id'     => $order->id,
                'invoice_id'   => $order->invoice_id,
                'courier_type' => $courierType,
                'error'        => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'কুরিয়ার সার্ভার থেকে তথ্য আনার সময় ত্রুটি হয়েছে: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sync Steadfast Status
     */
    protected static function syncSteadfastStatus(Order $order): array
    {
        $cfg = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first()
            ?: Courierapi::where('type', 'steadfast')->first();

        if (!$cfg || empty($cfg->api_key) || empty($cfg->secret_key)) {
            return [
                'success' => false,
                'message' => 'Steadfast API কনফিগারেশন পাওয়া যায়নি।',
            ];
        }

        $baseUrl = rtrim($cfg->url ?: 'https://portal.packzy.com/api/v1', '/');
        $baseUrl = preg_replace('#/create_order/?$#i', '', $baseUrl);

        $cidRaw  = trim((string) ($order->courier_tracking_id ?: ($order->consignment_id ?: '')));
        $codeRaw = trim((string) ($order->courier_tracking_code ?: ''));
        $inv     = trim((string) ($order->invoice_id ?: ''));

        $tries = [];
        if ($cidRaw !== '' && ctype_digit($cidRaw)) {
            $tries[] = '/status_by_cid/' . rawurlencode($cidRaw);
        }
        if ($codeRaw !== '') {
            $tries[] = '/status_by_trackingcode/' . rawurlencode($codeRaw);
        }
        if ($cidRaw !== '' && !ctype_digit($cidRaw)) {
            $tries[] = '/status_by_trackingcode/' . rawurlencode($cidRaw);
        }
        if ($inv !== '') {
            $tries[] = '/status_by_invoice/' . rawurlencode($inv);
        }

        $headers = [
            'Api-Key'      => $cfg->api_key,
            'Secret-Key'   => $cfg->secret_key,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];

        $data = null;
        foreach ($tries as $path) {
            $resp = Http::withHeaders($headers)->timeout(12)->get($baseUrl . $path);
            if ($resp->successful()) {
                $json = $resp->json();
                if (is_array($json) && isset($json['delivery_status'])) {
                    $data = $json;
                    break;
                }
            }
        }

        if (!$data || !isset($data['delivery_status'])) {
            return [
                'success' => false,
                'message' => 'Steadfast থেকে কোনো স্ট্যাটাস রেসপন্স পাওয়া যায়নি।',
            ];
        }

        $rawStatus = (string) $data['delivery_status'];
        $newStatusId = SteadfastWebhookStatus::toOrderStatusId($rawStatus);

        $updated = false;
        $oldStatusId = $order->order_status;
        if ($newStatusId !== null && (int) $newStatusId !== (int) $oldStatusId) {
            $order->order_status = $newStatusId;
            $order->save();
            $updated = true;

            if ($newStatusId == 11) {
                \App\Helpers\ResellerOrderHelper::deductDeliveryChargeOnCancel($order);
            }
        }

        $order->load('status');
        $statusDisplayName = $order->status ? $order->status->name : ($newStatusId ?: $oldStatusId);

        return [
            'success'            => true,
            'courier_name'       => 'Steadfast',
            'tracking_id'        => $cidRaw ?: $codeRaw,
            'raw_courier_status' => $rawStatus,
            'courier_status'     => ucwords(str_replace('_', ' ', $rawStatus)),
            'order_status_id'    => $order->order_status,
            'order_status_name'  => $statusDisplayName,
            'status_updated'     => $updated,
            'message'            => "Steadfast স্ট্যাটাস: " . ucwords(str_replace('_', ' ', $rawStatus)) . ($updated ? " (অর্ডার স্ট্যাটাস আপডেট হয়েছে)" : ""),
        ];
    }

    /**
     * Sync Pathao Status
     */
    protected static function syncPathaoStatus(Order $order): array
    {
        $cfg = Courierapi::where(['status' => 1, 'type' => 'pathao'])->first()
            ?: Courierapi::where('type', 'pathao')->first();

        if (!$cfg || empty($cfg->token)) {
            return [
                'success' => false,
                'message' => 'Pathao API কনফিগারেশন বা টোকেন পাওয়া যায়নি।',
            ];
        }

        $consignmentId = $order->courier_tracking_id ?: ($order->consignment_id ?: null);
        $baseUrl = rtrim($cfg->url ?: 'https://api-hermes.pathao.com', '/');
        $baseUrl = preg_replace('#/aladdin/?$#', '', $baseUrl);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $cfg->token,
            'Accept'        => 'application/json',
        ])->timeout(12)->get($baseUrl . '/aladdin/api/v1/orders/' . $consignmentId . '/info');

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => 'Pathao থেকে স্ট্যাটাস আনা যায়নি (HTTP ' . $response->status() . ')',
            ];
        }

        $json = $response->json();
        $orderData = $json['data'] ?? $json;
        $rawStatus = (string) ($orderData['order_status'] ?? $orderData['order_status_slug'] ?? 'Unknown');

        // Pathao status mapping
        $newStatusId = null;
        $lowerStatus = strtolower($rawStatus);
        if (in_array($lowerStatus, ['delivered', 'partial_delivered', 'payment_invoice_issued', 'paid'])) {
            $newStatusId = 6; // Completed
        } elseif (in_array($lowerStatus, ['cancelled', 'returned', 'return'])) {
            $newStatusId = 11; // Cancelled
        }

        $updated = false;
        $oldStatusId = $order->order_status;
        if ($newStatusId !== null && (int) $newStatusId !== (int) $oldStatusId) {
            $order->order_status = $newStatusId;
            $order->save();
            $updated = true;

            if ($newStatusId == 11) {
                \App\Helpers\ResellerOrderHelper::deductDeliveryChargeOnCancel($order);
            }
        }

        $order->load('status');
        $statusDisplayName = $order->status ? $order->status->name : ($newStatusId ?: $oldStatusId);

        return [
            'success'            => true,
            'courier_name'       => 'Pathao',
            'tracking_id'        => $consignmentId,
            'raw_courier_status' => $rawStatus,
            'courier_status'     => ucwords(str_replace('_', ' ', $rawStatus)),
            'order_status_id'    => $order->order_status,
            'order_status_name'  => $statusDisplayName,
            'status_updated'     => $updated,
            'message'            => "Pathao স্ট্যাটাস: " . ucwords(str_replace('_', ' ', $rawStatus)) . ($updated ? " (অর্ডার স্ট্যাটাস আপডেট হয়েছে)" : ""),
        ];
    }

    /**
     * Sync RedX Status
     */
    protected static function syncRedXStatus(Order $order): array
    {
        $cfg = Courierapi::where(['status' => 1, 'type' => 'redx'])->first()
            ?: Courierapi::where('type', 'redx')->first();

        if (!$cfg || empty($cfg->token)) {
            return [
                'success' => false,
                'message' => 'RedX API কনফিগারেশন বা টোকেন পাওয়া যায়নি।',
            ];
        }

        $trackingId = $order->courier_tracking_id ?: ($order->consignment_id ?: null);
        $redxService = new RedXService();
        $parcelDetails = $redxService->getParcelDetails($trackingId);

        if (!$parcelDetails || !isset($parcelDetails['parcel']['status'])) {
            return [
                'success' => false,
                'message' => 'RedX থেকে পার্সেল তথ্য পাওয়া যায়নি।',
            ];
        }

        $rawStatus = (string) $parcelDetails['parcel']['status'];
        $newStatusId = $redxService->mapStatusToOrderStatus(strtolower($rawStatus));

        $updated = false;
        $oldStatusId = $order->order_status;
        if ($newStatusId !== null && (int) $newStatusId !== (int) $oldStatusId) {
            $order->order_status = $newStatusId;
            $order->save();
            $updated = true;

            if ($newStatusId == 11) {
                \App\Helpers\ResellerOrderHelper::deductDeliveryChargeOnCancel($order);
            }
        }

        $order->load('status');
        $statusDisplayName = $order->status ? $order->status->name : ($newStatusId ?: $oldStatusId);

        return [
            'success'            => true,
            'courier_name'       => 'RedX',
            'tracking_id'        => $trackingId,
            'raw_courier_status' => $rawStatus,
            'courier_status'     => ucwords(str_replace('_', ' ', $rawStatus)),
            'order_status_id'    => $order->order_status,
            'order_status_name'  => $statusDisplayName,
            'status_updated'     => $updated,
            'message'            => "RedX স্ট্যাটাস: " . ucwords(str_replace('_', ' ', $rawStatus)) . ($updated ? " (অর্ডার স্ট্যাটাস আপডেট হয়েছে)" : ""),
        ];
    }
}
