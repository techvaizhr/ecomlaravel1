<?php

namespace App\Services;

use App\Models\FacebookCapiSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FacebookCapiService
{
    protected $accessToken;
    protected $pixelId;
    protected $testEventCode;
    protected $initialized = false;

    /**
     * Lazy load credentials - only when needed
     */
    /**
     * Get all active Facebook CAPI configurations.
     */
    public function getActiveConfigs(): array
    {
        $configs = [];

        // 1. Find default access token and test code from FacebookCapiSetting, EcomPixel, or env
        $defaultToken = null;
        $defaultTestCode = null;

        try {
            $settings = Cache::remember('facebook_capi_active_settings', 1800, function () {
                return FacebookCapiSetting::all();
            });

            foreach ($settings as $setting) {
                $t = trim((string)($setting->access_token ?? ''));
                if ($t !== '' && $t !== '0' && $t !== 'your_long_lived_access_token') {
                    $defaultToken = $t;
                }
                $tc = trim((string)($setting->test_event_code ?? ''));
                if ($tc !== '') {
                    $defaultTestCode = $tc;
                }

                $pid = trim((string)($setting->pixel_id ?? ''));
                if ($pid !== '' && $pid !== '0' && $pid !== 'your_pixel_id' && $defaultToken && ($setting->status == 1 || is_null($setting->status))) {
                    $configs[$pid] = [
                        'pixel_id'        => $pid,
                        'access_token'    => $defaultToken,
                        'test_event_code' => $defaultTestCode,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('FacebookCapi getActiveConfigs FacebookCapiSetting error: ' . $e->getMessage());
        }

        if (!$defaultToken) {
            $envToken = config('services.facebook.access_token') ?: env('FACEBOOK_ACCESS_TOKEN');
            if ($envToken && $envToken !== 'your_long_lived_access_token') {
                $defaultToken = trim($envToken);
            }
        }
        if (!$defaultTestCode) {
            $defaultTestCode = config('services.facebook.test_event_code') ?: env('FACEBOOK_TEST_EVENT_CODE');
        }

        // 2. Also check all active EcomPixel records
        try {
            $ecomPixels = \App\Models\EcomPixel::where('status', 1)->get();
            foreach ($ecomPixels as $ep) {
                $code = trim((string)($ep->code ?? ''));
                if ($code === '' || $code === '0') {
                    continue;
                }
                $epToken = !empty($ep->access_token) && $ep->access_token !== '0' ? trim($ep->access_token) : $defaultToken;
                $epTestCode = !empty($ep->test_event_code) ? trim($ep->test_event_code) : $defaultTestCode;

                if ($epToken) {
                    $configs[$code] = [
                        'pixel_id'        => $code,
                        'access_token'    => $epToken,
                        'test_event_code' => $epTestCode,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('FacebookCapi getActiveConfigs EcomPixel error: ' . $e->getMessage());
        }

        // 3. Fallback to env/config
        if (empty($configs)) {
            $pixelId = config('services.facebook.pixel_id') ?: env('FACEBOOK_PIXEL_ID');
            $token = $defaultToken;
            if ($pixelId && $token && $pixelId !== 'your_pixel_id') {
                $configs[trim($pixelId)] = [
                    'pixel_id'        => trim($pixelId),
                    'access_token'    => trim($token),
                    'test_event_code' => $defaultTestCode,
                ];
            }
        }

        return array_values($configs);
    }

    /**
     * Send event to Facebook Conversion API using direct HTTP request across all active pixels
     * 
     * @param string $eventName Standard event name (Purchase, AddToCart, ViewContent, etc.)
     * @param array $data Event data (currency, value, content_ids, contents, etc.)
     * @param array $userData User data (email, phone, fbp, fbc, etc.)
     * @param array $options Additional options (event_id, event_time, action_source, etc.)
     * @return array|false
     */
    public function sendEvent($eventName, $data = [], $userData = [], $options = [])
    {
        $configs = $this->getActiveConfigs();

        if (empty($configs)) {
            Log::warning('Facebook CAPI: Missing access token or pixel ID');
            return false;
        }

        try {
            // Prepare user data (hash PII)
            $preparedUserData = $this->prepareUserData($userData);

            // Prepare custom data
            $preparedCustomData = $this->prepareCustomData($data, $eventName);

            // Build event payload
            $eventPayload = [
                'event_name' => $eventName,
                'event_time' => $options['event_time'] ?? time(),
                'action_source' => $options['action_source'] ?? 'website',
                'event_source_url' => $options['event_source_url'] ?? request()->fullUrl(),
                'user_data' => $preparedUserData,
                'custom_data' => $preparedCustomData,
            ];

            // Add event ID if provided (for deduplication)
            if (isset($options['event_id'])) {
                $eventPayload['event_id'] = $options['event_id'];
            } elseif (isset($data['event_id'])) {
                $eventPayload['event_id'] = $data['event_id'];
            }

            // Allow test_event_code from options, query param, cookie, or session
            $runtimeTestCode = $options['test_event_code']
                ?? request()->query('test_event_code')
                ?? $_COOKIE['fb_test_event_code']
                ?? $_COOKIE['test_event_code']
                ?? session('fb_test_event_code')
                ?? null;

            $lastSuccessResponse = null;

            foreach ($configs as $cfg) {
                $testCode = !empty($cfg['test_event_code']) ? $cfg['test_event_code'] : $runtimeTestCode;

                $requestPayload = [
                    'data' => [$eventPayload],
                    'access_token' => $cfg['access_token'],
                ];

                if (!empty($testCode)) {
                    $requestPayload['test_event_code'] = $testCode;
                }

                $url = "https://graph.facebook.com/v21.0/{$cfg['pixel_id']}/events";

                try {
                    $response = Http::timeout(5)->post($url, $requestPayload);

                    if ($response->successful()) {
                        $responseData = $response->json();
                        Log::info('Facebook CAPI: Event sent successfully', [
                            'event_name' => $eventName,
                            'pixel_id' => $cfg['pixel_id'],
                            'event_id' => $eventPayload['event_id'] ?? null,
                            'has_test_code' => !empty($testCode),
                            'response' => $responseData
                        ]);
                        $lastSuccessResponse = $responseData;
                    } else {
                        Log::error('Facebook CAPI: API request failed', [
                            'event_name' => $eventName,
                            'pixel_id' => $cfg['pixel_id'],
                            'status' => $response->status(),
                            'has_test_code' => !empty($testCode),
                            'response' => $response->body()
                        ]);
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::warning('Facebook CAPI: Request timeout/connection error (non-blocking)', [
                        'event_name' => $eventName,
                        'pixel_id' => $cfg['pixel_id'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($lastSuccessResponse !== null) {
                return [
                    'success' => true,
                    'event_name' => $eventName,
                    'response' => $lastSuccessResponse
                ];
            }

            return ['success' => false, 'error' => 'API request failed'];

        } catch (\Exception $e) {
            Log::error('Facebook CAPI: Error sending event', [
                'event_name' => $eventName,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }

    /**
     * Prepare user data (hash PII as required by Facebook)
     */
    protected function prepareUserData($userData)
    {
        $prepared = [];

        // Email (hashed)
        if (isset($userData['email'])) {
            $email = trim(strtolower($userData['email']));
            if (!empty($email) && strlen($email) < 64) {
                $prepared['em'] = hash('sha256', $email);
            }
        }

        // Phone (hashed) — Bangladesh: 88017xxxxxxxx
        if (isset($userData['phone'])) {
            $phone = \App\Support\EcommerceTrackingUser::normalizePhone($userData['phone']);
            if (! empty($phone) && strlen($phone) < 64) {
                $prepared['ph'] = hash('sha256', $phone);
            }
        }

        // First name (hashed)
        if (isset($userData['first_name'])) {
            $firstName = trim(strtolower($userData['first_name']));
            if (!empty($firstName) && strlen($firstName) < 64) {
                $prepared['fn'] = hash('sha256', $firstName);
            }
        }

        // Last name (hashed)
        if (isset($userData['last_name'])) {
            $lastName = trim(strtolower($userData['last_name']));
            if (!empty($lastName) && strlen($lastName) < 64) {
                $prepared['ln'] = hash('sha256', $lastName);
            }
        }

        // City (hashed)
        if (isset($userData['city'])) {
            $city = trim(strtolower($userData['city']));
            if (!empty($city) && strlen($city) < 64) {
                $prepared['ct'] = hash('sha256', $city);
            }
        }

        // State (hashed)
        if (isset($userData['state'])) {
            $state = trim(strtolower($userData['state']));
            if (!empty($state) && strlen($state) < 64) {
                $prepared['st'] = hash('sha256', $state);
            }
        }

        // Zip code (hashed)
        if (isset($userData['zip_code'])) {
            $zipCode = trim($userData['zip_code']);
            if (!empty($zipCode) && strlen($zipCode) < 64) {
                $prepared['zp'] = hash('sha256', $zipCode);
            }
        }

        // Country code (2-letter ISO code)
        if (isset($userData['country_code'])) {
            $prepared['country'] = strtoupper($userData['country_code']);
        }

        // External ID (hashed)
        if (isset($userData['external_id'])) {
            $externalId = (string) $userData['external_id'];
            if (!empty($externalId) && strlen($externalId) < 64) {
                $prepared['external_id'] = hash('sha256', $externalId);
            }
        }

        // Facebook Click ID (fbp) - from _fbp cookie
        if (isset($userData['fbp'])) {
            $prepared['fbp'] = $userData['fbp'];
        }

        // Facebook Browser ID (fbc) - from _fbc cookie
        if (isset($userData['fbc'])) {
            $prepared['fbc'] = $userData['fbc'];
        }

        // Client IP address
        if (isset($userData['client_ip_address'])) {
            $prepared['client_ip_address'] = $userData['client_ip_address'];
        } else {
            $prepared['client_ip_address'] = request()->ip();
        }

        // User agent
        if (isset($userData['client_user_agent'])) {
            $prepared['client_user_agent'] = $userData['client_user_agent'];
        } else {
            $prepared['client_user_agent'] = request()->userAgent();
        }

        return $prepared;
    }

    /**
     * Prepare custom data
     */
    protected function prepareCustomData($data, $eventName)
    {
        $prepared = [];

        // Currency (required for Purchase, AddToCart, InitiateCheckout)
        if (isset($data['currency'])) {
            $prepared['currency'] = strtoupper($data['currency']);
        } else {
            $prepared['currency'] = 'BDT'; // Default currency
        }

        // Value (required for Purchase, AddToCart, InitiateCheckout)
        if (isset($data['value'])) {
            $prepared['value'] = (float) $data['value'];
        }

        // Content IDs (array of product IDs)
        if (isset($data['content_ids'])) {
            $prepared['content_ids'] = is_array($data['content_ids']) ? $data['content_ids'] : [$data['content_ids']];
        }

        // Content type ('product' or 'product_group')
        $prepared['content_type'] = $data['content_type'] ?? 'product';

        // Contents (array of content objects: id, quantity, item_price)
        if (isset($data['contents']) && is_array($data['contents'])) {
            $prepared['contents'] = array_values(array_map(function ($item) {
                if (is_array($item)) {
                    $clean = [
                        'id'         => (string) ($item['id'] ?? ''),
                        'quantity'   => (int) ($item['quantity'] ?? 1),
                        'item_price' => (float) ($item['item_price'] ?? 0),
                    ];
                    if (!empty($item['delivery_category'])) {
                        $clean['delivery_category'] = (string) $item['delivery_category'];
                    }
                    return $clean;
                }
                return $item;
            }, $data['contents']));
        }

        // Content name
        if (isset($data['content_name'])) {
            $prepared['content_name'] = $data['content_name'];
        }

        // Content category
        if (isset($data['content_category'])) {
            $prepared['content_category'] = $data['content_category'];
        }

        // Number of items
        if (isset($data['num_items'])) {
            $prepared['num_items'] = (int) $data['num_items'];
        }

        // Order ID (for Purchase events)
        if (isset($data['order_id'])) {
            $prepared['order_id'] = (string) $data['order_id'];
        }

        // Search string (for Search events)
        if (isset($data['search_string'])) {
            $prepared['search_string'] = $data['search_string'];
        }

        // Status (for Purchase events)
        if (isset($data['status'])) {
            $prepared['status'] = $data['status'];
        }

        return $prepared;
    }

    /**
     * Send event with custom pixel ID and access token (e.g. for reseller landing pages)
     */
    public function sendEventWithCredentials(string $pixelId, string $accessToken, string $eventName, array $data = [], array $userData = [], array $options = [])
    {
        if (empty($pixelId) || empty($accessToken)) {
            return false;
        }
        $preparedUserData = $this->prepareUserData($userData);
        $preparedCustomData = $this->prepareCustomData($data, $eventName);
        $eventPayload = [
            'event_name' => $eventName,
            'event_time' => $options['event_time'] ?? time(),
            'action_source' => $options['action_source'] ?? 'website',
            'event_source_url' => $options['event_source_url'] ?? request()->fullUrl(),
            'user_data' => $preparedUserData,
            'custom_data' => $preparedCustomData,
        ];
        if (isset($options['event_id'])) {
            $eventPayload['event_id'] = $options['event_id'];
        } elseif (isset($data['event_id'])) {
            $eventPayload['event_id'] = $data['event_id'];
        }
        $requestPayload = [
            'data' => [$eventPayload],
            'access_token' => $accessToken,
        ];
        $url = "https://graph.facebook.com/v21.0/{$pixelId}/events";
        try {
            $response = Http::timeout(5)->post($url, $requestPayload);
            if ($response->successful()) {
                Log::info('Facebook CAPI (Landing): Event sent', ['event_name' => $eventName, 'pixel_id' => $pixelId]);
                return ['success' => true, 'response' => $response->json()];
            }
            Log::warning('Facebook CAPI (Landing): Failed', ['event_name' => $eventName, 'body' => $response->body()]);
            return ['success' => false];
        } catch (\Throwable $e) {
            Log::warning('Facebook CAPI (Landing): Error ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Send Purchase event
     */
    public function sendPurchase($data, $userData = [], $options = [])
    {
        return $this->sendEvent('Purchase', $data, $userData, $options);
    }

    /**
     * Send AddToCart event
     */
    public function sendAddToCart($data, $userData = [], $options = [])
    {
        return $this->sendEvent('AddToCart', $data, $userData, $options);
    }

    /**
     * Send ViewContent event
     */
    public function sendViewContent($data, $userData = [], $options = [])
    {
        return $this->sendEvent('ViewContent', $data, $userData, $options);
    }

    /**
     * Send InitiateCheckout event
     */
    public function sendInitiateCheckout($data, $userData = [], $options = [])
    {
        return $this->sendEvent('InitiateCheckout', $data, $userData, $options);
    }

    /**
     * Send AddPaymentInfo event
     */
    public function sendAddPaymentInfo($data, $userData = [], $options = [])
    {
        return $this->sendEvent('AddPaymentInfo', $data, $userData, $options);
    }

    /**
     * Send Search event
     */
    public function sendSearch($data, $userData = [], $options = [])
    {
        return $this->sendEvent('Search', $data, $userData, $options);
    }

    /**
     * Get user data from request cookies and session
     */
    public function getUserDataFromRequest()
    {
        $userData = [];

        // Get Facebook Pixel cookies
        if (isset($_COOKIE['_fbp'])) {
            $userData['fbp'] = $_COOKIE['_fbp'];
        }

        if (isset($_COOKIE['_fbc'])) {
            $userData['fbc'] = $_COOKIE['_fbc'];
        }

        // Get authenticated user data if available
        if (auth()->check()) {
            $user = auth()->user();
            
            if (isset($user->email)) {
                $userData['email'] = $user->email;
            }

            if (isset($user->phone)) {
                $userData['phone'] = $user->phone;
            }

            if (isset($user->name)) {
                $nameParts = explode(' ', $user->name, 2);
                if (count($nameParts) > 0) {
                    $userData['first_name'] = $nameParts[0];
                }
                if (count($nameParts) > 1) {
                    $userData['last_name'] = $nameParts[1];
                }
            }

            if (isset($user->id)) {
                $userData['external_id'] = (string) $user->id;
            }
        }

        return $userData;
    }
}
