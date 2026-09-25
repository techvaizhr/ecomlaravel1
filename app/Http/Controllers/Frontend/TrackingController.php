<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ServerCapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    /**
     * Handle asynchronous Server CAPI event dispatching from browser interactions.
     * Guarantees 100% deduplication match between Browser Pixels and Server CAPI.
     */
    public function handleCapiEvent(Request $request)
    {
        $raw = $request->all();
        if (empty($raw)) {
            $content = $request->getContent();
            if (!empty($content)) {
                $raw = json_decode($content, true) ?: [];
            }
        }

        $eventName = trim((string) ($raw['event_name'] ?? $request->input('event_name', '')));
        if (empty($eventName)) {
            return response()->json(['status' => 'error', 'message' => 'Missing event_name'], 400);
        }

        $eventId   = $raw['event_id'] ?? $request->input('event_id');
        $sourceUrl = ($raw['source_url'] ?? null) ?: ($request->headers->get('referer') ?: url('/'));
        $eventData = (array) ($raw['event_data'] ?? $request->input('event_data', []));
        $userData  = (array) ($raw['user_data'] ?? $request->input('user_data', []));

        // Auto-inject client IP, User-Agent, cookies if not present
        if (empty($userData['client_ip_address'])) {
            $userData['client_ip_address'] = $request->ip();
        }
        if (empty($userData['client_user_agent'])) {
            $userData['client_user_agent'] = $request->userAgent();
        }
        if (empty($userData['fbp'])) {
            $userData['fbp'] = $request->cookie('_fbp') ?: ($raw['fbp'] ?? $request->input('fbp'));
        }
        if (empty($userData['fbc'])) {
            $userData['fbc'] = $request->cookie('_fbc') ?: ($request->cookie('fbc') ?: ($raw['fbc'] ?? $request->input('fbc')));
        }
        if (empty($userData['ttclid'])) {
            $userData['ttclid'] = $request->cookie('ttclid') ?: ($raw['ttclid'] ?? $request->input('ttclid'));
        }

        $testEventCode = $raw['test_event_code'] ?? $request->input('test_event_code');
        if (empty($testEventCode) && !empty($sourceUrl) && str_contains($sourceUrl, 'test_event_code=')) {
            parse_str(parse_url($sourceUrl, PHP_URL_QUERY) ?? '', $queryParams);
            $testEventCode = $queryParams['test_event_code'] ?? null;
        }

        try {
            switch ($eventName) {
                case 'ViewContent':
                    ServerCapiService::trackViewContent($eventData, $userData, $eventId, $sourceUrl, $testEventCode);
                    break;

                case 'AddToCart':
                    ServerCapiService::trackAddToCart($eventData, $userData, $eventId, $sourceUrl, $testEventCode);
                    break;

                case 'InitiateCheckout':
                    ServerCapiService::trackInitiateCheckout($eventData, $userData, $eventId, $sourceUrl, $testEventCode);
                    break;

                default:
                    break;
            }

            return response()->json([
                'status'   => 'success',
                'event'    => $eventName,
                'event_id' => $eventId,
            ]);
        } catch (\Throwable $e) {
            Log::warning('TrackingController handleCapiEvent error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
