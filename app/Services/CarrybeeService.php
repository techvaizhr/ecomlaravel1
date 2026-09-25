<?php

namespace App\Services;

use App\Models\Courierapi;
use App\Models\CourierStore;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CarrybeeService
{
    /**
     * Get Carrybee config record
     */
    public static function getConfig(): ?Courierapi
    {
        return Courierapi::where('type', 'carrybee')->first();
    }

    /**
     * Resolve Base URL for Carrybee
     */
    public static function getBaseUrl(?Courierapi $config = null): string
    {
        $config = $config ?: self::getConfig();
        $url = $config ? trim((string) $config->url) : '';

        if (empty($url)) {
            return 'https://developers.carrybee.com';
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        return rtrim($url, '/');
    }

    /**
     * Prepare headers for Carrybee API request
     */
    public static function getHeaders(?Courierapi $config = null): array
    {
        $config = $config ?: self::getConfig();

        if (!$config) {
            return [];
        }

        $headers = [
            'Client-ID'      => (string) ($config->client_id ?? ''),
            'Client-Secret'  => (string) ($config->client_secret ?? ($config->secret_key ?? '')),
            'Client-Context' => (string) ($config->client_context ?? ''),
            'Content-Type'   => 'application/json',
            'Accept'         => 'application/json',
        ];

        return $headers;
    }

    /**
     * Check if Carrybee credentials are configured
     */
    public static function isConfigured(?Courierapi $config = null): bool
    {
        $config = $config ?: self::getConfig();
        if (!$config) return false;

        $clientId = trim((string) ($config->client_id ?? ''));
        $secret = trim((string) ($config->client_secret ?? ($config->secret_key ?? '')));
        $context = trim((string) ($config->client_context ?? ''));

        return !empty($clientId) && !empty($secret) && !empty($context);
    }

    /**
     * Fetch Stores from Carrybee API
     */
    public static function fetchStores(): array
    {
        $config = self::getConfig();
        if (!self::isConfigured($config)) {
            return [
                'success' => false,
                'message' => 'Carrybee API credentials (Client-ID, Client-Secret, Client-Context) not properly configured.',
                'stores'  => []
            ];
        }

        $baseUrl = self::getBaseUrl($config);
        $headers = self::getHeaders($config);

        try {
            $response = Http::withHeaders($headers)->timeout(15)->get($baseUrl . '/api/v2/stores');

            if ($response->successful()) {
                $body = $response->json();
                $stores = $body['data']['stores'] ?? [];

                return [
                    'success' => true,
                    'message' => $body['message'] ?? 'Stores fetched successfully',
                    'stores'  => $stores,
                    'raw'     => $body
                ];
            }

            $errorMsg = $response->json()['message'] ?? 'Failed to fetch stores from Carrybee (HTTP ' . $response->status() . ')';
            return [
                'success' => false,
                'message' => $errorMsg,
                'stores'  => [],
                'status'  => $response->status()
            ];
        } catch (\Throwable $e) {
            Log::error('Carrybee fetchStores Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'stores'  => []
            ];
        }
    }

    /**
     * Sync stores from Carrybee API to DB table `courier_stores`
     */
    public static function syncStoresToDatabase(): array
    {
        $result = self::fetchStores();

        if (!$result['success']) {
            return $result;
        }

        $stores = $result['stores'];
        $syncedCount = 0;

        $existingDefault = CourierStore::courier('carrybee')->default()->first();
        $config = self::getConfig();
        $defaultStoreId = $config ? $config->default_store_id : null;

        foreach ($stores as $s) {
            $storeId = (string) ($s['id'] ?? '');
            if (empty($storeId)) continue;

            $isApiDefault = !empty($s['is_default_pickup_store']);
            $shouldBeDefault = false;

            if ($defaultStoreId && $defaultStoreId === $storeId) {
                $shouldBeDefault = true;
            } elseif (!$existingDefault && $isApiDefault) {
                $shouldBeDefault = true;
            }

            CourierStore::updateOrCreate(
                [
                    'courier_type' => 'carrybee',
                    'store_id'     => $storeId,
                ],
                [
                    'store_name'                      => (string) ($s['name'] ?? 'Store #' . $storeId),
                    'contact_person_name'             => $s['contact_person_name'] ?? null,
                    'contact_person_number'           => $s['contact_person_number'] ?? null,
                    'contact_person_secondary_number' => $s['contact_person_secondary_number'] ?? null,
                    'address'                         => $s['address'] ?? null,
                    'city_id'                         => isset($s['city_id']) ? (int) $s['city_id'] : null,
                    'zone_id'                         => isset($s['zone_id']) ? (int) $s['zone_id'] : null,
                    'area_id'                         => isset($s['area_id']) ? (int) $s['area_id'] : null,
                    'is_active'                       => isset($s['is_active']) ? (bool) $s['is_active'] : true,
                    'is_default'                      => $shouldBeDefault,
                    'raw_data'                        => $s,
                ]
            );

            $syncedCount++;
        }

        // Return updated store list from DB
        $dbStores = CourierStore::courier('carrybee')->get();

        return [
            'success'      => true,
            'message'      => "মোট {$syncedCount} টি Carrybee স্টোর সফলভাবে ডাটাবেজে সিঙ্ক হয়েছে।",
            'synced_count' => $syncedCount,
            'stores'       => $dbStores
        ];
    }

    /**
     * Create single order on Carrybee
     *
     * @param Order $order
     * @param array $options [store_id, delivery_type, product_type, item_weight, notes, etc.]
     */
    public static function createOrder(Order $order, array $options = []): array
    {
        $config = self::getConfig();
        if (!self::isConfigured($config)) {
            return [
                'success' => false,
                'message' => 'Carrybee API credentials are not set.'
            ];
        }

        // Determine pickup store
        $storeId = $options['store_id'] ?? null;
        if (empty($storeId)) {
            $defaultStore = CourierStore::courier('carrybee')->default()->first()
                ?: CourierStore::courier('carrybee')->first();
            $storeId = $defaultStore ? $defaultStore->store_id : ($config->default_store_id ?? null);
        }

        if (empty($storeId)) {
            // Try fetching from API directly
            $storesRes = self::fetchStores();
            if (!empty($storesRes['stores'])) {
                $storeId = (string) $storesRes['stores'][0]['id'];
            }
        }

        if (empty($storeId)) {
            return [
                'success' => false,
                'message' => 'Carrybee পিকআপ স্টোর সিলেক্ট করুন অথবা সেটিংস থেকে স্টোর সিঙ্ক করে ডিফল্ট স্টোর সেট করুন।'
            ];
        }

        // Shipping details
        $shipping = $order->shipping;
        $recipientName = trim($shipping ? $shipping->name : ($order->customer->name ?? 'Customer'));
        if (strlen($recipientName) < 2) {
            $recipientName = 'Customer ' . $order->invoice_id;
        }

        $recipientPhone = trim($shipping ? $shipping->phone : ($order->customer->phone ?? ''));
        // Clean phone
        $recipientPhone = preg_replace('/[^0-9]/', '', $recipientPhone);
        if (str_starts_with($recipientPhone, '880')) {
            $recipientPhone = '0' . substr($recipientPhone, 3);
        }

        $recipientAddress = trim($shipping ? ($shipping->full_address ?: $shipping->address) : ($order->customer->address ?? ''));
        if (strlen($recipientAddress) < 10) {
            $recipientAddress = $recipientAddress . ' (Order #' . $order->invoice_id . ')';
        }
        if (strlen($recipientAddress) < 10) {
            $recipientAddress = 'House delivery address, Bangladesh';
        }
        if (strlen($recipientAddress) > 200) {
            $recipientAddress = substr($recipientAddress, 0, 200);
        }

        // COD Amount
        $codAmount = !empty($order->customer_payable_amount)
            ? round($order->customer_payable_amount)
            : round($order->amount);

        if (isset($options['collectable_amount']) && is_numeric($options['collectable_amount'])) {
            $codAmount = (int) $options['collectable_amount'];
        }

        // Weight in grams (1 to 25000), default 200g
        $weightGrams = isset($options['item_weight']) && (int) $options['item_weight'] > 0 ? (int) $options['item_weight'] : 200;
        if ($weightGrams < 1) $weightGrams = 200;
        if ($weightGrams > 25000) $weightGrams = 25000;

        // Delivery Type: 1 = Normal, 2 = Express
        $deliveryType = isset($options['delivery_type']) ? (int) $options['delivery_type'] : 1;

        // Product Type: 1 = Parcel, 2 = Book, 3 = Document
        $productType = isset($options['product_type']) ? (int) $options['product_type'] : 1;

        // Quantity
        $quantity = $order->orderdetails ? $order->orderdetails->sum('qty') : 1;
        if ($quantity < 1) $quantity = 1;
        if ($quantity > 200) $quantity = 200;

        // Description / instruction
        $instruction = $options['special_instruction'] ?? ($order->note ?? 'Please check product on delivery');
        if (strlen($instruction) > 255) $instruction = substr($instruction, 0, 255);

        $description = $options['product_description'] ?? 'E-commerce Order #' . $order->invoice_id;
        if (strlen($description) > 255) $description = substr($description, 0, 255);

        $payload = [
            'store_id'            => (string) $storeId,
            'merchant_order_id'   => (string) $order->invoice_id,
            'delivery_type'       => $deliveryType,
            'product_type'        => $productType,
            'recipient_phone'     => (string) $recipientPhone,
            'recipient_name'      => (string) $recipientName,
            'recipient_address'   => (string) $recipientAddress,
            'item_weight'         => $weightGrams,
            'item_quantity'       => $quantity,
            'collectable_amount'  => (int) $codAmount,
            'special_instruction' => $instruction,
            'product_description' => $description,
            'is_closed_box'       => !empty($options['is_closed_box']),
            'is_exchange'         => !empty($options['is_exchange']),
        ];

        if (!empty($options['city_id'])) $payload['city_id'] = (int) $options['city_id'];
        if (!empty($options['zone_id'])) $payload['zone_id'] = (int) $options['zone_id'];
        if (!empty($options['area_id'])) $payload['area_id'] = (int) $options['area_id'];

        $baseUrl = self::getBaseUrl($config);
        $headers = self::getHeaders($config);

        Log::info('Carrybee Order Create Request', [
            'order_id'   => $order->id,
            'invoice_id' => $order->invoice_id,
            'payload'    => $payload
        ]);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(20)
                ->post($baseUrl . '/api/v2/orders', $payload);

            $body = $response->json();

            Log::info('Carrybee Order Create Response', [
                'status' => $response->status(),
                'body'   => $body
            ]);

            if ($response->status() === 201 && isset($body['data']['order'])) {
                $orderData = $body['data']['order'];
                $consignmentId = (string) ($orderData['consignment_id'] ?? '');

                if (!empty($consignmentId)) {
                    $order->courier_type          = 'carrybee';
                    $order->courier_tracking_id   = $consignmentId;
                    $order->courier_tracking_code = $consignmentId;
                    $order->courier_sent_at       = now();
                    $order->consignment_id        = $consignmentId;
                    $order->order_status          = 5; // Shipped / In Courier
                    $order->save();

                    return [
                        'success'        => true,
                        'message'        => 'Carrybee পার্সেল সফলভাবে বুকিং হয়েছে!',
                        'consignment_id' => $consignmentId,
                        'order'          => $orderData,
                        'raw'            => $body
                    ];
                }
            }

            // Error handling
            $errorMsg = $body['message'] ?? 'Carrybee order creation failed.';
            if (!empty($body['causes']) && is_array($body['causes'])) {
                $causes = [];
                foreach ($body['causes'] as $fld => $errs) {
                    $causes[] = $fld . ': ' . json_encode($errs);
                }
                $errorMsg .= ' (' . implode(', ', $causes) . ')';
            }

            return [
                'success' => false,
                'message' => $errorMsg,
                'status'  => $response->status(),
                'raw'     => $body
            ];
        } catch (\Throwable $e) {
            Log::error('Carrybee Order Create Exception', [
                'order_id' => $order->id,
                'error'    => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Carrybee সার্ভার ত্রুটি: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get Order Details / Track from Carrybee API
     */
    public static function getOrderDetails(string $consignmentId): array
    {
        $config = self::getConfig();
        if (!self::isConfigured($config)) {
            return ['success' => false, 'message' => 'Credentials not configured'];
        }

        $baseUrl = self::getBaseUrl($config);
        $headers = self::getHeaders($config);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(12)
                ->get($baseUrl . '/api/v2/orders/' . rawurlencode($consignmentId) . '/details');

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'data'    => $body['data'] ?? [],
                    'raw'     => $body
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Order details not found',
                'status'  => $response->status()
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cancel Order on Carrybee
     */
    public static function cancelOrder(string $consignmentId, string $reason = 'Cancelled by merchant'): array
    {
        $config = self::getConfig();
        if (!self::isConfigured($config)) {
            return ['success' => false, 'message' => 'Credentials not configured'];
        }

        $baseUrl = self::getBaseUrl($config);
        $headers = self::getHeaders($config);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->post($baseUrl . '/api/v2/orders/' . rawurlencode($consignmentId) . '/cancel', [
                    'cancellation_reason' => substr($reason, 0, 190)
                ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Order cancelled on Carrybee'];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Cancellation failed',
                'status'  => $response->status()
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get Cities list
     */
    public static function getCities(): array
    {
        $config = self::getConfig();
        if (!self::isConfigured($config)) return [];

        $baseUrl = self::getBaseUrl($config);
        $headers = self::getHeaders($config);

        try {
            $response = Http::withHeaders($headers)->timeout(10)->get($baseUrl . '/api/v2/cities');
            if ($response->successful()) {
                return $response->json()['data']['cities'] ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('Carrybee getCities error', ['error' => $e->getMessage()]);
        }
        return [];
    }

    /**
     * Get Zones for City
     */
    public static function getZones(int $cityId): array
    {
        $config = self::getConfig();
        if (!self::isConfigured($config)) return [];

        $baseUrl = self::getBaseUrl($config);
        $headers = self::getHeaders($config);

        try {
            $response = Http::withHeaders($headers)->timeout(10)->get($baseUrl . "/api/v2/cities/{$cityId}/zones");
            if ($response->successful()) {
                return $response->json()['data']['zones'] ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('Carrybee getZones error', ['error' => $e->getMessage()]);
        }
        return [];
    }

    /**
     * Get Areas for Zone
     */
    public static function getAreas(int $cityId, int $zoneId): array
    {
        $config = self::getConfig();
        if (!self::isConfigured($config)) return [];

        $baseUrl = self::getBaseUrl($config);
        $headers = self::getHeaders($config);

        try {
            $response = Http::withHeaders($headers)->timeout(10)->get($baseUrl . "/api/v2/cities/{$cityId}/zones/{$zoneId}/areas");
            if ($response->successful()) {
                return $response->json()['data']['areas'] ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('Carrybee getAreas error', ['error' => $e->getMessage()]);
        }
        return [];
    }
}
