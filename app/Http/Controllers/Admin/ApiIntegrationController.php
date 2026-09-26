<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\Courierapi;
use App\Helpers\SmsHelper;
use Toastr;
use File;
use Str;
use Image;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\CourierStore;
use App\Services\BdCourierService;
use App\Services\CarrybeeService;

class ApiIntegrationController extends Controller
{
    
     
    public function pay_manage ()
    {
        $bkash = PaymentGateway::where('type','=','bkash')->first();
        $shurjopay = PaymentGateway::where('type','=','shurjopay')->first();
        $uddoktapay = PaymentGateway::where('type', 'uddoktapay')->first();
        $aamarpay = PaymentGateway::where('type', 'aamarpay')->first();
        return view('backEnd.apiintegration.pay_manage', compact('bkash', 'shurjopay', 'uddoktapay', 'aamarpay'));

    }
    
   public function pay_update(Request $request)
{
    $update_data = \App\Models\PaymentGateway::find($request->id);
    $input = $request->all();
    $input['status'] = $request->status ? 1 : 0;
    $update_data->update($input);

    // ✅ যদি গেটওয়ে টাইপ হয় UddoktaPay
    if ($update_data->type === 'uddoktapay') {
        $this->updateEnvFile('UDDOKTAPAY_API_KEY', $request->app_key);
        $this->updateEnvFile('UDDOKTAPAY_API_URL', $request->base_url);
    }

    \Toastr::success('Success', ucfirst($update_data->type) . ' settings updated successfully');
    return redirect()->back();
}

/**
 * 🔧 Helper function: Update or add key in .env file
 */
private function updateEnvFile($key, $value)
{
    $path = base_path('.env');

    if (file_exists($path)) {
        $oldValue = env($key);

        if (strpos(file_get_contents($path), $key) !== false) {
            // Replace old value
            file_put_contents($path, str_replace(
                $key . '=' . $oldValue,
                $key . '=' . $value,
                file_get_contents($path)
            ));
        } else {
            // Add new line if not exists
            file_put_contents($path, PHP_EOL . $key . '=' . $value, FILE_APPEND);
        }
    }
}

    
    public function sms_manage ()
    {  
        $sms = SmsGateway::first();
        return view('backEnd.apiintegration.sms_manage',compact('sms'));
    }
    
public function sms_update(Request $request)
{
    $sms = SmsGateway::find($request->id) ?? SmsGateway::first();

    if (!$sms) {
        Toastr::error('SMS Gateway record not found!', 'Error');
        return redirect()->back();
    }

    $sms->update([
        // BulkSMSBD fixed config
        'gateway_name'   => 'BulkSMSBD',
        'url'            => 'http://bulksmsbd.net/api/smsapi',
        'method'         => 'GET',
        'param_api_key'  => 'api_key',
        'param_phone'    => 'number',
        'param_message'  => 'message',
        'param_senderid' => 'senderid',
        'extra_params'   => '{"type":"text"}',
        'success_check'  => '202',
        'auth_type'      => 'none',
        // User-configurable fields
        'api_key'        => $request->api_key,
        'senderid'       => $request->senderid ?? '',
        'serderid'       => $request->senderid ?? '',
        'admin_phone'    => $request->admin_phone_list ?? '',
    ]);

    Toastr::success('BulkSMSBD settings saved!', 'Success');
    return redirect()->back();
}

public function sms_balance()
{
    $sms = SmsGateway::first();

    if (!$sms || empty($sms->api_key)) {
        return response()->json(['success' => false, 'message' => 'API Key সেট করা নেই।']);
    }

    $url = 'http://bulksmsbd.net/api/getBalanceApi?api_key=' . urlencode($sms->api_key);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
    ]);
    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return response()->json(['success' => false, 'message' => 'Connection error: ' . $err]);
    }

    $data = json_decode($response, true);

    // BulkSMSBD returns: {"response_code":202,"success_message":"Your current balance is 100.00 tk"}
    if (isset($data['response_code']) && $data['response_code'] == 202) {
        $balance = $data['balance'] ?? $data['success_message'] ?? 'N/A';
        return response()->json(['success' => true, 'balance' => $balance, 'raw' => $data]);
    }

    $errMsg = $data['error_message'] ?? $data['message'] ?? 'Invalid API Key বা সমস্যা হয়েছে।';
    return response()->json(['success' => false, 'message' => $errMsg, 'raw' => $data]);
}

/**
 * অফিসিয়াল my-plan `data` রুটে `next_due_date` ও `expires_at` থাকে; নেস্টেড অবজেক্টও স্ক্যান।
 *
 * @param  array<string, mixed>  $d
 */
protected function resolveBdCourierNextDueRaw(array $d): ?string
{
    $keys = [
        'next_due_date', 'nextDueDate',
        'expires_at', 'expire_at',
        'next_billing_date', 'nextBillingDate',
        'next_billing_at', 'nextBillingAt',
        'due_date', 'dueDate',
        'subscription_ends_at', 'subscriptionEndsAt',
        'renewal_date', 'renewalDate',
        'valid_until', 'validUntil',
        'end_date', 'endDate',
        'billing_period_end', 'current_period_end',
        'next_payment_date', 'nextPaymentDate',
        'period_end', 'periodEnd',
    ];

    $nestedKeys = ['subscription', 'plan', 'billing', 'current_plan', 'invoice'];

    $pick = function (array $row) use ($keys): ?string {
        foreach ($keys as $k) {
            if (! array_key_exists($k, $row)) {
                continue;
            }
            $v = $row[$k];
            if ($v === null || $v === '') {
                continue;
            }
            if (is_string($v) || is_int($v) || is_float($v)) {
                return (string) $v;
            }
        }

        return null;
    };

    if ($hit = $pick($d)) {
        return $hit;
    }

    foreach ($nestedKeys as $nest) {
        if (! empty($d[$nest]) && is_array($d[$nest])) {
            if ($hit = $pick($d[$nest])) {
                return $hit;
            }
        }
    }

    return null;
}

protected function formatBdCourierNextDue(?string $raw): ?string
{
    if ($raw === null || $raw === '') {
        return null;
    }

    if (is_numeric($raw)) {
        $n = (int) $raw;
        if ($n > 1000000000 && $n < 4000000000) {
            try {
                return \Carbon\Carbon::createFromTimestamp($n)
                    ->timezone(config('app.timezone'))
                    ->format('d M Y, h:i A');
            } catch (\Throwable $e) {
                return $raw;
            }
        }
    }

    try {
        return \Carbon\Carbon::parse($raw)
            ->timezone(config('app.timezone'))
            ->format('d M Y, h:i A');
    } catch (\Throwable $e) {
        return $raw;
    }
}

/**
 * BD Courier — My Plan (subscription, limits, usage).
 *
 * @see https://api.bdcourier.com/my-plan — success payload uses root keys:
 *      has_subscription, plan_name, next_due_date, expires_at, days_remaining, api_calls, …
 */
public function bdcourier_my_plan()
{
    $apiKey = BdCourierService::resolveApiKey();
    if (! $apiKey) {
        return response()->json([
            'success' => false,
            'message' => 'BD Courier API কী নেই। ফ্রড সেটিংসে কী দিন অথবা .env এ BDCOURIER_API_KEY যোগ করুন।',
        ]);
    }

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept'        => 'application/json',
        ])->timeout(12)->get('https://api.bdcourier.com/my-plan');

        $body = $response->json();
        if (! is_array($body)) {
            $body = json_decode($response->body(), true) ?? [];
        }

        $statusOk = strtolower((string) ($body['status'] ?? '')) === 'success';
        if ($statusOk && array_key_exists('data', $body)) {
            $data = $body['data'];
            if (is_string($data)) {
                $decoded = json_decode($data, true);
                $data = is_array($decoded) ? $decoded : [];
            } elseif (! is_array($data)) {
                $data = [];
            }

            $rawDue = $this->resolveBdCourierNextDueRaw($data);
            $data['next_due_display_raw'] = $rawDue;
            $data['next_due_display'] = $this->formatBdCourierNextDue($rawDue);

            if ($data['next_due_display'] === null) {
                $days = $data['days_remaining'] ?? data_get($data, 'subscription.days_remaining');
                if (is_numeric($days)) {
                    $data['next_due_display'] = 'আরও '.$days.' দিন পর (প্ল্যান অনুযায়ী)';
                }
            }

            return response()->json(['success' => true, 'data' => $data]);
        }

        return response()->json([
            'success' => false,
            'message' => $body['message'] ?? 'প্ল্যান তথ্য পাওয়া যায়নি।',
            'raw'     => $body,
        ]);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * অ্যাডমিন ড্যাশবোর্ডে স্টেডফাস্ট: ব্যালান্স (get_balance) + ইন রিভিউ পার্সেল (নিকটতম অর্ডার স্ট্যাটাস চেক)।
 */
public function steadfast_dashboard_widget()
{
    $cfg = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();

    if (! $cfg || empty($cfg->api_key) || empty($cfg->secret_key)) {
        return response()->json([
            'success' => false,
            'message' => 'স্টেডফাস্ট API কনফিগার করা নেই বা নিষ্ক্রিয়।',
        ]);
    }

    $stored = trim(str_replace(' ', '', $cfg->url ?? ''));
    $baseUrl = $stored !== '' ? rtrim($stored, '/') : 'https://portal.packzy.com/api/v1';
    $baseUrl = rtrim(preg_replace('#/create_order/?$#i', '', $baseUrl), '/');

    $headers = [
        'Api-Key'       => $cfg->api_key,
        'Secret-Key'    => $cfg->secret_key,
        'Content-Type'  => 'application/json',
        'Accept'        => 'application/json',
    ];

    $balanceRaw       = null;
    $balanceFormatted = null;

    try {
        $br = Http::withHeaders($headers)->timeout(10)->get($baseUrl.'/get_balance');
        if ($br->successful()) {
            $bj = $br->json();
            if ((int) ($bj['status'] ?? 0) === 200 && array_key_exists('current_balance', $bj)) {
                $balanceRaw = $bj['current_balance'];
                $balanceFormatted = '৳'.number_format((float) $balanceRaw, 2);
            }
        }
    } catch (\Throwable $e) {
        \Log::warning('Steadfast get_balance dashboard', ['error' => $e->getMessage()]);
    }

    $courierPendingTotal = Order::where('courier_type', 'steadfast')
        ->where('order_status', 5)
        ->count();

    $sample = Order::where('courier_type', 'steadfast')
        ->where('order_status', 5)
        ->whereNotNull('courier_tracking_id')
        ->orderByDesc('id')
        ->limit(15)
        ->get(['id', 'courier_tracking_id', 'courier_tracking_code', 'invoice_id']);

    $inReviewCount = 0;
    $sampleChecked = 0;

    $pendingRequests = [];

    foreach ($sample as $o) {
        $cid   = trim((string) ($o->courier_tracking_id ?? ''));
        $code  = trim((string) ($o->courier_tracking_code ?? ''));
        $inv   = trim((string) ($o->invoice_id ?? ''));
        $url   = null;

        if ($cid !== '' && ctype_digit($cid)) {
            $url = $baseUrl.'/status_by_cid/'.rawurlencode($cid);
        } elseif ($code !== '') {
            $url = $baseUrl.'/status_by_trackingcode/'.rawurlencode($code);
        } elseif ($cid !== '') {
            $url = $baseUrl.'/status_by_trackingcode/'.rawurlencode($cid);
        } elseif ($inv !== '') {
            $url = $baseUrl.'/status_by_invoice/'.rawurlencode($inv);
        }

        if ($url) {
            $pendingRequests[(string) $o->id] = $url;
        }
    }

    if ($pendingRequests !== []) {
        try {
            $responses = Http::pool(function ($pool) use ($pendingRequests, $headers) {
                $batch = [];
                foreach ($pendingRequests as $id => $url) {
                    $batch[] = $pool->as($id)->withHeaders($headers)->timeout(10)->get($url);
                }

                return $batch;
            });

            foreach ($pendingRequests as $id => $_url) {
                $resp = $responses[$id] ?? null;

                if ($resp instanceof \Throwable) {
                    continue;
                }

                if (! $resp || ! $resp->successful()) {
                    continue;
                }

                $sampleChecked++;

                $json = $resp->json();

                if (is_array($json)
                    && strtolower((string) ($json['delivery_status'] ?? '')) === 'in_review') {
                    $inReviewCount++;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Steadfast dashboard status pool', ['error' => $e->getMessage()]);
        }
    }

    return response()->json([
        'success'                => true,
        'balance'                => $balanceRaw,
        'balance_display'        => $balanceFormatted,
        'in_review_count'        => $inReviewCount,
        'in_review_checked'      => $sampleChecked,
        'in_review_sample_cap'   => $sample->count(),
        'courier_pending_orders' => $courierPendingTotal,
    ]);
}

public function sms_toggle_field(Request $request)
{
    $allowed = ['status', 'order', 'forget_pass', 'password_g'];

    if (!in_array($request->field, $allowed)) {
        return response()->json(['success' => false, 'message' => 'Invalid field'], 422);
    }

    $sms = SmsGateway::first();
    if (!$sms) {
        return response()->json(['success' => false, 'message' => 'Gateway not found'], 404);
    }

    $newValue = $request->value ? 1 : 0;
    $sms->update([$request->field => $newValue]);

    return response()->json([
        'success' => true,
        'field'   => $request->field,
        'value'   => $newValue,
    ]);
}

    
    public function courier_manage()
    {
        $steadfast = Courierapi::where('type', '=', 'steadfast')->first();
        $pathao    = Courierapi::where('type', '=', 'pathao')->first();
        $redx      = Courierapi::where('type', '=', 'redx')->first();
        $carrybee  = Courierapi::where('type', '=', 'carrybee')->first();

        // Create RedX entry if not exists
        if (!$redx) {
            $redx = Courierapi::create([
                'type'   => 'redx',
                'url'    => 'https://sandbox.redx.com.bd/v1.0.0-beta',
                'status' => 0,
            ]);
        }

        // Create Carrybee entry if not exists
        if (!$carrybee) {
            $carrybee = Courierapi::create([
                'type'           => 'carrybee',
                'url'            => 'https://developers.carrybee.com',
                'status'         => 0,
                'webhook_url'    => config('app.url') . '/webhooks/carrybee?token=40489fe0-9386-4fc9-8e92-2b2fcb9d451c',
                'webhook_secret' => '40489fe0-9386-4fc9-8e92-2b2fcb9d451c',
            ]);
        }

        // Load stores for all couriers
        $carrybee_stores = Schema::hasTable('courier_stores') ? CourierStore::courier('carrybee')->get() : collect([]);
        $pathao_stores   = Schema::hasTable('courier_stores') ? CourierStore::courier('pathao')->get() : collect([]);
        $redx_stores     = Schema::hasTable('courier_stores') ? CourierStore::courier('redx')->get() : collect([]);
        $steadfast_stores= Schema::hasTable('courier_stores') ? CourierStore::courier('steadfast')->get() : collect([]);

        return view('backEnd.apiintegration.courier_manage', compact(
            'steadfast',
            'pathao',
            'redx',
            'carrybee',
            'carrybee_stores',
            'pathao_stores',
            'redx_stores',
            'steadfast_stores'
        ));
    }

    public function courier_update(Request $request)
    {
        $update_data = Courierapi::find($request->id);
        if (!$update_data && $request->type) {
            $update_data = Courierapi::where('type', $request->type)->first();
        }

        if (!$update_data) {
            Toastr::error('Courier configuration not found', 'Error');
            return redirect()->back();
        }

        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;

        // Only include webhook_url and other optional cols if schema has them
        if (!Schema::hasColumn('courierapis', 'webhook_url')) {
            unset($input['webhook_url']);
        }
        if (!Schema::hasColumn('courierapis', 'webhook_secret')) {
            unset($input['webhook_secret']);
        }
        if (!Schema::hasColumn('courierapis', 'client_context')) {
            unset($input['client_context']);
        }
        if (!Schema::hasColumn('courierapis', 'default_store_id')) {
            unset($input['default_store_id']);
        }

        // Carrybee configuration
        if ($update_data->type === 'carrybee' || $request->type === 'carrybee') {
            if (!empty($input['url'])) {
                $url = trim($input['url']);
                if (!preg_match('#^https?://#i', $url)) {
                    $url = 'https://' . $url;
                }
                $input['url'] = rtrim($url, '/');
            }

            if (!empty($input['webhook_secret'])) {
                $input['webhook_secret'] = trim($input['webhook_secret']);
            }
        }

        // Pathao এর জন্য token handle (manual entry + auto generation)
        if ($update_data->type === 'pathao' || $request->type === 'pathao') {
            if (!empty($input['token'])) {
                $input['token'] = trim(preg_replace('/^Bearer\s+/i', '', (string)$input['token']));
            } elseif (!empty($input['client_id']) && !empty($input['client_secret']) && !empty($input['username']) && !empty($input['password'])) {
                try {
                    $apiUrl = $input['url'] ?? 'https://api-hermes.pathao.com';
                    $apiUrl = rtrim($apiUrl, '/');
                    $apiUrl = preg_replace('#/aladdin/?$#', '', $apiUrl);

                    $username = $input['username'] ?? null;
                    $password = $input['password'] ?? null;

                    $tokenResponse = $this->generatePathaoToken(
                        $input['client_id'],
                        $input['client_secret'],
                        $apiUrl,
                        $username,
                        $password
                    );
                    if ($tokenResponse && isset($tokenResponse['access_token'])) {
                        $input['token'] = $tokenResponse['access_token'];
                    }
                } catch (\Exception $e) {
                    Toastr::warning('Token generation failed: ' . $e->getMessage());
                }
            }

            if (!empty($input['webhook_secret'])) {
                $input['webhook_secret'] = trim($input['webhook_secret']);
            }
        }

        // Steadfast — Webhook URL + Bearer token
        if ($update_data->type === 'steadfast') {
            if (!empty($input['url'])) {
                $url = trim($input['url']);
                $url = preg_replace('/^https?:\/\//', '', $url);
                $input['url'] = 'https://' . rtrim($url, '/');
            }

            if (isset($input['token']) && $input['token'] !== null) {
                $input['token'] = preg_replace('/^Bearer\s+/i', '', trim((string) $input['token']));
                if ($input['token'] === '') {
                    $input['token'] = null;
                }
            }

            if (isset($input['webhook_url'])) {
                $webhookUrl = trim((string) $input['webhook_url']);
                if ($webhookUrl !== '') {
                    if (!preg_match('/^https?:\/\//', $webhookUrl)) {
                        $baseUrl = rtrim(config('app.url'), '/');
                        $input['webhook_url'] = $baseUrl . '/' . ltrim($webhookUrl, '/');
                    }
                } else {
                    $input['webhook_url'] = null;
                }
            }
        }

        // RedX এর জন্য URL format ঠিক করা
        if ($update_data->type == 'redx') {
            if (!empty($input['url'])) {
                $url = trim($input['url']);
                $url = preg_replace('/^https?:\/\//', '', $url);
                $url = rtrim($url, '/');
                $input['url'] = 'https://' . $url;
            }

            if (!empty($input['token'])) {
                $token = trim($input['token']);
                $token = preg_replace('/^Bearer\s+/i', '', $token);
                $input['token'] = $token;
            }

            if (isset($input['webhook_url']) && !empty(trim($input['webhook_url']))) {
                $webhookUrl = trim($input['webhook_url']);
                if (!preg_match('/^https?:\/\//', $webhookUrl)) {
                    $baseUrl = rtrim(config('app.url'), '/');
                    $input['webhook_url'] = $baseUrl . '/' . ltrim($webhookUrl, '/');
                }
            } else {
                $input['webhook_url'] = null;
            }
        }

        $update_data->update($input);

        Toastr::success('সফল', ucfirst($update_data->type) . ' সেটিংস সফলভাবে আপডেট হয়েছে');
        return redirect()->back();
    }

    /**
     * AJAX: Sync stores from courier API to DB
     */
    public function sync_courier_stores($type)
    {
        $type = strtolower($type);

        if (!Schema::hasTable('courier_stores')) {
            return response()->json(['success' => false, 'message' => 'courier_stores table not found. Please run migrations.']);
        }

        try {
            if ($type === 'carrybee') {
                $res = CarrybeeService::syncStoresToDatabase();
                return response()->json($res);
            }

            if ($type === 'pathao') {
                $pathao = Courierapi::where(['type' => 'pathao'])->first();
                if (!$pathao || empty($pathao->token)) {
                    return response()->json(['success' => false, 'message' => 'Pathao টোকেন কনফিগার করা নেই।']);
                }

                $baseUrl = rtrim($pathao->url ?? 'https://api-hermes.pathao.com', '/');
                $baseUrl = preg_replace('#/aladdin/?$#', '', $baseUrl);
                if (!preg_match('#^https?://#i', $baseUrl)) $baseUrl = 'https://' . $baseUrl;

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $pathao->token,
                    'Accept'        => 'application/json',
                ])->timeout(15)->get($baseUrl . '/aladdin/api/v1/stores');

                if ($response->successful()) {
                    $data = $response->json();
                    $stores = $data['data']['data'] ?? ($data['data'] ?? []);
                    $syncedCount = 0;

                    $existingDefault = CourierStore::courier('pathao')->default()->first();

                    foreach ($stores as $s) {
                        $storeId = (string) ($s['store_id'] ?? $s['id'] ?? '');
                        if (empty($storeId)) continue;

                        $shouldBeDefault = false;
                        if ($pathao->default_store_id && $pathao->default_store_id === $storeId) {
                            $shouldBeDefault = true;
                        } elseif (!$existingDefault && $syncedCount === 0) {
                            $shouldBeDefault = true;
                        }

                        CourierStore::updateOrCreate(
                            ['courier_type' => 'pathao', 'store_id' => $storeId],
                            [
                                'store_name'            => (string) ($s['store_name'] ?? 'Store #' . $storeId),
                                'address'               => $s['store_address'] ?? $s['address'] ?? null,
                                'city_id'               => $s['city_id'] ?? null,
                                'zone_id'               => $s['zone_id'] ?? null,
                                'area_id'               => $s['area_id'] ?? null,
                                'contact_person_number' => $s['store_phone'] ?? $s['phone'] ?? null,
                                'is_active'             => true,
                                'is_default'            => $shouldBeDefault,
                                'raw_data'              => $s,
                            ]
                        );
                        $syncedCount++;
                    }

                    $dbStores = CourierStore::courier('pathao')->get();
                    return response()->json([
                        'success'      => true,
                        'message'      => "মোট {$syncedCount} টি Pathao স্টোর সফলভাবে সিঙ্ক হয়েছে।",
                        'synced_count' => $syncedCount,
                        'stores'       => $dbStores,
                    ]);
                }

                return response()->json(['success' => false, 'message' => 'Pathao থেকে স্টোর আনা সম্ভব হয়নি।']);
            }

            if ($type === 'redx') {
                $redx = Courierapi::where(['type' => 'redx'])->first();
                if (!$redx || empty($redx->token)) {
                    return response()->json(['success' => false, 'message' => 'RedX টোকেন কনফিগার করা নেই।']);
                }

                $redxService = new \App\Services\RedXService();
                $res = $redxService->getPickupStores();
                $stores = $res['pickup_stores'] ?? [];
                $syncedCount = 0;

                $existingDefault = CourierStore::courier('redx')->default()->first();

                foreach ($stores as $s) {
                    $storeId = (string) ($s['id'] ?? '');
                    if (empty($storeId)) continue;

                    $shouldBeDefault = false;
                    if ($redx->default_store_id && $redx->default_store_id === $storeId) {
                        $shouldBeDefault = true;
                    } elseif (!$existingDefault && $syncedCount === 0) {
                        $shouldBeDefault = true;
                    }

                    CourierStore::updateOrCreate(
                        ['courier_type' => 'redx', 'store_id' => $storeId],
                        [
                            'store_name'            => (string) ($s['name'] ?? 'Store #' . $storeId),
                            'address'               => $s['address'] ?? null,
                            'contact_person_number' => $s['phone'] ?? null,
                            'is_active'             => true,
                            'is_default'            => $shouldBeDefault,
                            'raw_data'              => $s,
                        ]
                    );
                    $syncedCount++;
                }

                $dbStores = CourierStore::courier('redx')->get();
                return response()->json([
                    'success'      => true,
                    'message'      => "মোট {$syncedCount} টি RedX স্টোর সফলভাবে সিঙ্ক হয়েছে।",
                    'synced_count' => $syncedCount,
                    'stores'       => $dbStores,
                ]);
            }

            if ($type === 'steadfast') {
                $steadfast = Courierapi::where(['type' => 'steadfast'])->first();
                if (!$steadfast || empty($steadfast->api_key) || empty($steadfast->secret_key)) {
                    return response()->json(['success' => false, 'message' => 'Steadfast API Key বা Secret Key কনফিগার করা নেই।']);
                }

                $apiUrl = rtrim($steadfast->url ?? 'https://portal.packzy.com/api/v1', '/');
                if (!preg_match('#^https?://#i', $apiUrl)) $apiUrl = 'https://' . $apiUrl;
                if (!str_contains($apiUrl, '/api/v1')) $apiUrl .= '/api/v1';

                $syncedCount = 0;
                $balanceInfo = null;

                // 1. Check credentials & balance
                try {
                    $balRes = Http::withHeaders([
                        'Api-Key'      => $steadfast->api_key,
                        'Secret-Key'   => $steadfast->secret_key,
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ])->timeout(12)->get($apiUrl . '/get_balance');

                    if ($balRes->successful()) {
                        $balanceInfo = $balRes->json();
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Steadfast balance check in store sync', ['error' => $e->getMessage()]);
                }

                // 2. Try fetching pickup addresses/stores from API if endpoint available
                $fetchedStores = [];
                $endpointsToTry = ['/get_pickup_addresses', '/pickup_addresses', '/stores', '/get_profile'];
                foreach ($endpointsToTry as $ep) {
                    try {
                        $addrRes = Http::withHeaders([
                            'Api-Key'      => $steadfast->api_key,
                            'Secret-Key'   => $steadfast->secret_key,
                            'Content-Type' => 'application/json',
                            'Accept'       => 'application/json',
                        ])->timeout(8)->get($apiUrl . $ep);

                        if ($addrRes->successful()) {
                            $addrData = $addrRes->json();
                            $items = $addrData['data'] ?? ($addrData['pickup_addresses'] ?? ($addrData['stores'] ?? []));
                            if (is_array($items) && !empty($items)) {
                                $fetchedStores = $items;
                                break;
                            }
                        }
                    } catch (\Throwable $e) {}
                }

                $existingDefault = CourierStore::courier('steadfast')->default()->first();

                if (!empty($fetchedStores)) {
                    foreach ($fetchedStores as $idx => $s) {
                        $storeId = (string) ($s['id'] ?? $s['store_id'] ?? $s['address_id'] ?? ($idx + 1));
                        $storeName = (string) ($s['name'] ?? $s['store_name'] ?? $s['title'] ?? ('Steadfast Store #' . $storeId));
                        $addr = $s['address'] ?? $s['pickup_address'] ?? null;
                        $phone = $s['phone'] ?? $s['contact_number'] ?? null;

                        $shouldBeDefault = false;
                        if ($steadfast->default_store_id && $steadfast->default_store_id === $storeId) {
                            $shouldBeDefault = true;
                        } elseif (!$existingDefault && $syncedCount === 0) {
                            $shouldBeDefault = true;
                        }

                        CourierStore::updateOrCreate(
                            ['courier_type' => 'steadfast', 'store_id' => $storeId],
                            [
                                'store_name'            => $storeName,
                                'address'               => $addr,
                                'contact_person_number' => $phone,
                                'is_active'             => true,
                                'is_default'            => $shouldBeDefault,
                                'raw_data'              => $s,
                            ]
                        );
                        $syncedCount++;
                    }
                } else {
                    // Ensure Primary Business Store exists
                    $setting = \App\Models\GeneralSetting::first();
                    $shopName = $setting->name ?? 'Main Business Store';
                    $shopAddr = $setting->address ?? 'Dhaka, Bangladesh';
                    $shopPhone = $setting->phone ?? null;

                    $mainStore = CourierStore::courier('steadfast')->where('store_id', '1')->first();
                    if (!$mainStore && CourierStore::courier('steadfast')->count() === 0) {
                        CourierStore::create([
                            'courier_type'          => 'steadfast',
                            'store_id'              => '1',
                            'store_name'            => $shopName,
                            'address'               => $shopAddr,
                            'contact_person_number' => $shopPhone,
                            'is_active'             => true,
                            'is_default'            => true,
                            'raw_data'              => ['balance' => $balanceInfo['current_balance'] ?? null],
                        ]);
                    }
                }

                $dbStores = CourierStore::courier('steadfast')->get();
                $msg = "Steadfast সংযোগ সফল!";
                if ($balanceInfo && isset($balanceInfo['current_balance'])) {
                    $msg .= " (বর্তমান ব্যালান্স: ৳" . number_format($balanceInfo['current_balance'], 2) . ")";
                }
                $msg .= " মোট {$dbStores->count()} টি স্টোর সিঙ্ক হয়েছে।";

                return response()->json([
                    'success'      => true,
                    'message'      => $msg,
                    'synced_count' => $dbStores->count(),
                    'stores'       => $dbStores,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'অজ্ঞাত কুরিয়ার টাইপ']);
        } catch (\Throwable $e) {
            \Log::error('sync_courier_stores error', ['type' => $type, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Set default store for a courier
     */
    public function set_default_store(Request $request)
    {
        $request->validate([
            'courier_type' => 'required|string',
            'store_id'     => 'required',
        ]);

        $courierType = strtolower($request->courier_type);
        $storeId     = (string) $request->store_id;

        // Reset default for this courier
        CourierStore::where('courier_type', $courierType)->update(['is_default' => false]);

        // Set target store as default
        $store = CourierStore::where('courier_type', $courierType)
            ->where(function($q) use ($storeId) {
                $q->where('store_id', $storeId)->orWhere('id', $storeId);
            })->first();

        if ($store) {
            $store->is_default = true;
            $store->save();
        }

        // Update courierapis table
        $courier = Courierapi::where('type', $courierType)->first();
        if ($courier) {
            $courier->default_store_id = $storeId;
            $courier->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'ডিফল্ট স্টোর সফলভাবে সেট করা হয়েছে!',
            'store_id' => $storeId,
            'courier_type' => $courierType
        ]);
    }

    /**
     * AJAX/POST: Save manual store (e.g. for Steadfast or offline pickup points)
     */
    public function save_courier_store(Request $request)
    {
        $request->validate([
            'courier_type'          => 'required|string',
            'store_name'            => 'required|string|max:191',
            'contact_person_number' => 'nullable|string|max:50',
            'address'               => 'nullable|string|max:300',
        ]);

        $storeId = $request->store_id ?: ('store_' . time() . '_' . rand(100, 999));

        $store = CourierStore::updateOrCreate(
            [
                'courier_type' => strtolower($request->courier_type),
                'store_id'     => $storeId,
            ],
            [
                'store_name'            => $request->store_name,
                'contact_person_name'   => $request->contact_person_name,
                'contact_person_number' => $request->contact_person_number,
                'address'               => $request->address,
                'city_name'             => $request->city_name,
                'zone_name'             => $request->zone_name,
                'is_active'             => true,
                'is_default'            => $request->is_default ? true : false,
            ]
        );

        if ($request->is_default) {
            $store->makeDefault();
        }

        Toastr::success('স্টোর সফলভাবে সংরক্ষণ হয়েছে', 'সফল');
        return redirect()->back();
    }

    /**
     * AJAX/POST: Delete store from DB
     */
    public function delete_courier_store(Request $request)
    {
        $store = CourierStore::find($request->id);
        if ($store) {
            $store->delete();
            return response()->json(['success' => true, 'message' => 'স্টোর ডিলিট হয়েছে।']);
        }
        return response()->json(['success' => false, 'message' => 'স্টোর পাওয়া যায়নি।']);
    }

    /**
     * AJAX: Get stores for modal
     */
    public function get_courier_stores($type)
    {
        $stores = CourierStore::courier($type)->where('is_active', true)->get();
        $defaultStore = CourierStore::courier($type)->default()->first();

        return response()->json([
            'success'       => true,
            'stores'        => $stores,
            'default_store' => $defaultStore ? $defaultStore->store_id : null,
        ]);
    }
    
    /**
     * Generate Pathao Access Token
     * According to Pathao API Documentation: https://developer.pathao.com/
     * Uses OAuth 2.0 with grant_type: password
     */
    private function generatePathaoToken($clientId, $clientSecret, $baseUrl = 'https://api-hermes.pathao.com', $username = null, $password = null)
    {
        try {
            // Clean up URL - remove trailing slashes and /aladdin if present
            $baseUrl = rtrim($baseUrl, '/');
            $baseUrl = preg_replace('#/aladdin/?$#', '', $baseUrl);
            
            // Ensure we have the correct base URL
            if (!preg_match('#^https?://#', $baseUrl)) {
                $baseUrl = 'https://' . $baseUrl;
            }
            
            // Check if this is sandbox/test environment
            $isSandbox = (strpos($baseUrl, 'sandbox') !== false || strpos($baseUrl, 'courier-api-sandbox') !== false);
            
            // For sandbox, use test credentials if username/password not provided
            if ($isSandbox && empty($username)) {
                $username = 'test@pathao.com';
                $password = 'lovePathao';
            }
            
            // Validate required fields
            if (empty($username) || empty($password)) {
                throw new \Exception('Username and Password are required for Pathao token generation. For Sandbox: test@pathao.com / lovePathao');
            }
            
            \Log::info('Attempting Pathao token generation', [
                'base_url' => $baseUrl,
                'endpoint' => $baseUrl . '/aladdin/api/v1/issue-token',
                'has_client_id' => !empty($clientId),
                'has_client_secret' => !empty($clientSecret),
                'has_username' => !empty($username),
                'is_sandbox' => $isSandbox
            ]);
            
            // Pathao API requires JSON format with grant_type: password
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($baseUrl . '/aladdin/api/v1/issue-token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password
            ]);
            
            \Log::info('Pathao token API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => substr($response->body(), 0, 500)
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['access_token'])) {
                    \Log::info('Pathao token generated successfully', [
                        'token_type' => $data['token_type'] ?? 'N/A',
                        'expires_in' => $data['expires_in'] ?? 'N/A'
                    ]);
                    return $data;
                } else {
                    throw new \Exception('Access token not found in response: ' . json_encode($data));
                }
            } else {
                $errorBody = $response->json();
                $errorMessage = $errorBody['message'] ?? 'Token generation failed';
                
                \Log::error('Pathao token generation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'error_message' => $errorMessage
                ]);
                
                throw new \Exception('Token generation failed: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            \Log::error('Pathao token generation exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Generate Pathao Token via AJAX
     */
    public function pathao_generate_token(Request $request)
    {
        try {
            \Log::info('Pathao token generation request received');
            
            $pathao = Courierapi::where('type', 'pathao')->first();
            
            $clientId     = trim((string) ($request->input('client_id') ?? $pathao?->client_id));
            $clientSecret = trim((string) ($request->input('client_secret') ?? $pathao?->client_secret));
            $username     = trim((string) ($request->input('username') ?? $pathao?->username));
            $password     = trim((string) ($request->input('password') ?? $pathao?->password));
            $apiUrl       = trim((string) ($request->input('url') ?? $pathao?->url ?? 'https://api-hermes.pathao.com'));

            if (empty($clientId) || empty($clientSecret)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Client ID এবং Client Secret প্রদান করুন।',
                ], 400);
            }

            if (empty($username) || empty($password)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Username এবং Password প্রদান করুন।',
                ], 400);
            }
            
            $apiUrl = rtrim($apiUrl, '/');
            $apiUrl = preg_replace('#/aladdin/?$#', '', $apiUrl);
            
            $tokenResponse = $this->generatePathaoToken(
                $clientId, 
                $clientSecret, 
                $apiUrl,
                $username,
                $password
            );
            
            if ($tokenResponse && isset($tokenResponse['access_token'])) {
                if (!$pathao) {
                    $pathao = new Courierapi(['type' => 'pathao']);
                }
                $pathao->client_id     = $clientId;
                $pathao->client_secret = $clientSecret;
                $pathao->username      = $username;
                $pathao->password      = $password;
                $pathao->url           = $apiUrl;
                $pathao->token         = $tokenResponse['access_token'];
                $pathao->save();
                
                // Calculate and save expiry time if expires_in is provided
                if(isset($tokenResponse['expires_in'])){
                    $expiresIn = (int) $tokenResponse['expires_in']; // seconds
                    $expiresAt = now()->addSeconds($expiresIn);
                    // Note: If you have token_expires_at column, uncomment below:
                    // $pathao->token_expires_at = $expiresAt;
                }
                
                $pathao->save();
                
                // Calculate expiry info for response
                $expiryInfo = '';
                if(isset($tokenResponse['expires_in'])){
                    $expiresIn = (int) $tokenResponse['expires_in'];
                    $days = floor($expiresIn / 86400);
                    $hours = floor(($expiresIn % 86400) / 3600);
                    $minutes = floor(($expiresIn % 3600) / 60);
                    
                    if($days > 0){
                        $expiryInfo = $days . ' দিন';
                    } elseif($hours > 0){
                        $expiryInfo = $hours . ' ঘন্টা';
                    } else {
                        $expiryInfo = $minutes . ' মিনিট';
                    }
                }
                
                \Log::info('Pathao token generated successfully', [
                    'expires_in' => $tokenResponse['expires_in'] ?? 'N/A',
                    'expiry_info' => $expiryInfo
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Token generated successfully' . ($expiryInfo ? ' (Valid for ' . $expiryInfo . ')' : ''),
                    'token' => $tokenResponse['access_token'],
                    'expires_in' => $tokenResponse['expires_in'] ?? null,
                    'expiry_info' => $expiryInfo,
                    'expires_at' => isset($expiresAt) ? $expiresAt->format('Y-m-d H:i:s') : null
                ]);
            } else {
                \Log::error('Pathao token generation failed', ['response' => $tokenResponse]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate token. Please check your Client ID and Secret. Response: ' . json_encode($tokenResponse)
                ], 400);
            }
        } catch (\Exception $e) {
            $errorDetails = [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method(),
                'request_data' => request()->all()
            ];
            
            \Log::error('Pathao token generation exception', $errorDetails);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Token generation failed: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $errorDetails : null
            ], 500);
        }
    }
    public function sms_custom_send_page()
{
    return view('backEnd.apiintegration.sms_custom_send');
}

public function sms_custom_send(Request $request)
{
    $request->validate([
        'phone'   => 'required|string',
        'message' => 'required|string|max:500',
    ]);

    $sent = SmsHelper::send($request->phone, $request->message);

    if ($sent) {
        Toastr::success('SMS sent successfully!', 'Success');
    } else {
        Toastr::error('SMS sending failed. Check API key and gateway status.', 'Failed');
    }

    return back();
}

}