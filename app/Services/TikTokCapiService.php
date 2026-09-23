<?php

namespace App\Services;

use App\Models\TiktokPixel;
use App\Models\AdsAnalyticsSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TikTokCapiService
{
    private const API_URL = 'https://business-api.tiktok.com/open_api/v1.3/event/track/';

    /**
     * Get active configurations: array of ['pixel_id' => ..., 'access_token' => ..., 'test_event_code' => ...]
     */
    public function getActiveConfigs(): array
    {
        $configs = [];

        // 1. Try from tiktok_pixels table
        try {
            $pixels = TiktokPixel::where('status', 1)->get();
            $globalToken = null;

            foreach ($pixels as $pixel) {
                $token = $pixel->access_token;
                if (!$token) {
                    if ($globalToken === null) {
                        $globalToken = AdsAnalyticsSetting::where('platform', 'tiktok')
                            ->where('is_active', 1)
                            ->value('access_token')
                            ?: (config('services.tiktok.access_token') ?: env('TIKTOK_ACCESS_TOKEN'));
                    }
                    $token = $globalToken;
                }

                if ($pixel->code && $token) {
                    $configs[] = [
                        'pixel_id'        => trim($pixel->code),
                        'access_token'    => trim($token),
                        'test_event_code' => $pixel->test_event_code ?: env('TIKTOK_TEST_EVENT_CODE'),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TikTok CAPI: Error reading pixels: ' . $e->getMessage());
        }

        // 2. Fallback to env/settings if no configs from table
        if (empty($configs)) {
            $pixelId = config('services.tiktok.pixel_id') ?: env('TIKTOK_PIXEL_ID');
            $token = config('services.tiktok.access_token') ?: env('TIKTOK_ACCESS_TOKEN');
            if (!$token) {
                try {
                    $token = AdsAnalyticsSetting::where('platform', 'tiktok')
                        ->where('is_active', 1)
                        ->value('access_token');
                } catch (\Throwable $e) {}
            }

            if ($pixelId && $token) {
                $configs[] = [
                    'pixel_id'        => trim($pixelId),
                    'access_token'    => trim($token),
                    'test_event_code' => env('TIKTOK_TEST_EVENT_CODE'),
                ];
            }
        }

        return $configs;
    }

    /**
     * Normalize and hash phone to E.164 with '+' for TikTok Events API
     */
    protected function hashPhone(?string $phone): ?string
    {
        if (!$phone) return null;
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (!$digits) return null;

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $e164 = '+880' . substr($digits, 1);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            $e164 = '+880' . $digits;
        } elseif (str_starts_with($digits, '880')) {
            $e164 = '+' . $digits;
        } else {
            $e164 = '+' . $digits;
        }

        return hash('sha256', $e164);
    }

    /**
     * Send event to TikTok Events API
     */
    public function sendEvent(string $eventName, array $properties = [], array $userData = [], array $options = []): array
    {
        $configs = $this->getActiveConfigs();
        if (empty($configs)) {
            return ['success' => false, 'message' => 'No active TikTok CAPI configurations found'];
        }

        $results = [];

        // Build user object
        $userObj = [];
        if (!empty($userData['phone'])) {
            $hashedPhone = $this->hashPhone($userData['phone']);
            if ($hashedPhone) $userObj['phone_sha256'] = $hashedPhone;
        }
        if (!empty($userData['email'])) {
            $userObj['email_sha256'] = hash('sha256', strtolower(trim($userData['email'])));
        }
        if (!empty($userData['external_id'])) {
            $userObj['external_id_sha256'] = hash('sha256', (string) $userData['external_id']);
        }

        $ttclid = $userData['ttclid'] ?? $_COOKIE['ttclid'] ?? request()->query('ttclid');
        if ($ttclid) {
            $userObj['ttclid'] = (string) $ttclid;
        }

        $userObj['ip'] = $userData['client_ip_address'] ?? request()->ip();
        $userObj['user_agent'] = $userData['client_user_agent'] ?? request()->userAgent();

        // Standard TikTok contents format
        $contents = [];
        if (!empty($properties['contents']) && is_array($properties['contents'])) {
            foreach ($properties['contents'] as $it) {
                $contents[] = [
                    'content_id'   => (string) ($it['id'] ?? $it['content_id'] ?? ''),
                    'content_type' => 'product',
                    'content_name' => (string) ($it['name'] ?? $it['content_name'] ?? ''),
                    'quantity'     => (int) ($it['quantity'] ?? $it['qty'] ?? 1),
                    'price'        => (float) ($it['item_price'] ?? $it['price'] ?? 0),
                ];
            }
        }

        $propObj = [
            'currency' => $properties['currency'] ?? 'BDT',
            'value'    => (float) ($properties['value'] ?? 0),
        ];
        if (!empty($properties['order_id'])) {
            $propObj['order_id'] = (string) $properties['order_id'];
        }
        if (!empty($contents)) {
            $propObj['contents'] = $contents;
        }

        $eventPayload = [
            'event'      => $eventName,
            'event_time' => (int) ($options['event_time'] ?? time()),
            'event_id'   => (string) ($options['event_id'] ?? $properties['event_id'] ?? uniqid('tt_', true)),
            'user'       => $userObj,
            'properties' => $propObj,
        ];

        // Send to each configured TikTok Pixel
        foreach ($configs as $cfg) {
            $reqBody = [
                'event_source'    => 'web',
                'event_source_id' => $cfg['pixel_id'],
                'data'            => [$eventPayload],
            ];

            if (!empty($cfg['test_event_code'])) {
                $reqBody['test_event_code'] = $cfg['test_event_code'];
            }

            try {
                $response = Http::timeout(5)
                    ->withHeaders([
                        'Access-Token' => $cfg['access_token'],
                        'Content-Type' => 'application/json',
                    ])
                    ->post(self::API_URL, $reqBody);

                if ($response->successful()) {
                    $json = $response->json();
                    Log::info('TikTok CAPI: Event sent successfully', [
                        'pixel_id'   => $cfg['pixel_id'],
                        'event_name' => $eventName,
                        'event_id'   => $eventPayload['event_id'],
                        'response'   => $json,
                    ]);
                    $results[$cfg['pixel_id']] = ['success' => true, 'response' => $json];
                } else {
                    Log::error('TikTok CAPI: API request failed', [
                        'pixel_id' => $cfg['pixel_id'],
                        'status'   => $response->status(),
                        'response' => $response->body(),
                    ]);
                    $results[$cfg['pixel_id']] = ['success' => false, 'error' => $response->body()];
                }
            } catch (\Throwable $e) {
                Log::warning('TikTok CAPI: Connection error or timeout (non-blocking)', [
                    'pixel_id' => $cfg['pixel_id'],
                    'error'    => $e->getMessage(),
                ]);
                $results[$cfg['pixel_id']] = ['success' => false, 'error' => $e->getMessage()];
            }
        }

        return ['success' => true, 'results' => $results];
    }
}
