<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Order;

class EcommerceTrackingUser
{
    /**
     * Complete District to Division & English Romanization Map for 64 Districts of Bangladesh.
     */
    protected static array $districtMap = [
        // Dhaka Division
        'ঢাকা' => ['city' => 'Dhaka', 'state' => 'Dhaka'],
        'dhaka' => ['city' => 'Dhaka', 'state' => 'Dhaka'],
        'gazipur' => ['city' => 'Gazipur', 'state' => 'Dhaka'],
        'গাজীপুর' => ['city' => 'Gazipur', 'state' => 'Dhaka'],
        'narayanganj' => ['city' => 'Narayanganj', 'state' => 'Dhaka'],
        'নারায়ণগঞ্জ' => ['city' => 'Narayanganj', 'state' => 'Dhaka'],
        'narshingdi' => ['city' => 'Narsingdi', 'state' => 'Dhaka'],
        'narsingdi' => ['city' => 'Narsingdi', 'state' => 'Dhaka'],
        'নরসিংদী' => ['city' => 'Narsingdi', 'state' => 'Dhaka'],
        'tangail' => ['city' => 'Tangail', 'state' => 'Dhaka'],
        'টাঙ্গাইল' => ['city' => 'Tangail', 'state' => 'Dhaka'],
        'kishoreganj' => ['city' => 'Kishoreganj', 'state' => 'Dhaka'],
        'কিশোরগঞ্জ' => ['city' => 'Kishoreganj', 'state' => 'Dhaka'],
        'manikganj' => ['city' => 'Manikganj', 'state' => 'Dhaka'],
        'মানিকগঞ্জ' => ['city' => 'Manikganj', 'state' => 'Dhaka'],
        'munshiganj' => ['city' => 'Munshiganj', 'state' => 'Dhaka'],
        'মুন্সীগঞ্জ' => ['city' => 'Munshiganj', 'state' => 'Dhaka'],
        'rajbari' => ['city' => 'Rajbari', 'state' => 'Dhaka'],
        'রাজবাড়ী' => ['city' => 'Rajbari', 'state' => 'Dhaka'],
        'faridpur' => ['city' => 'Faridpur', 'state' => 'Dhaka'],
        'ফরিদপুর' => ['city' => 'Faridpur', 'state' => 'Dhaka'],
        'gopalganj' => ['city' => 'Gopalganj', 'state' => 'Dhaka'],
        'গোপালগঞ্জ' => ['city' => 'Gopalganj', 'state' => 'Dhaka'],
        'madaripur' => ['city' => 'Madaripur', 'state' => 'Dhaka'],
        'মাদারীপুর' => ['city' => 'Madaripur', 'state' => 'Dhaka'],
        'shariatpur' => ['city' => 'Shariatpur', 'state' => 'Dhaka'],
        'শরীয়তপুর' => ['city' => 'Shariatpur', 'state' => 'Dhaka'],

        // Chattogram Division
        'chattogram' => ['city' => 'Chattogram', 'state' => 'Chattogram'],
        'chittagong' => ['city' => 'Chattogram', 'state' => 'Chattogram'],
        'চট্টগ্রাম' => ['city' => 'Chattogram', 'state' => 'Chattogram'],
        'coxsbazar' => ['city' => "Cox's Bazar", 'state' => 'Chattogram'],
        'cox\'s bazar' => ['city' => "Cox's Bazar", 'state' => 'Chattogram'],
        'কক্সবাজার' => ['city' => "Cox's Bazar", 'state' => 'Chattogram'],
        'cumilla' => ['city' => 'Cumilla', 'state' => 'Chattogram'],
        'comilla' => ['city' => 'Cumilla', 'state' => 'Chattogram'],
        'কুমিল্লা' => ['city' => 'Cumilla', 'state' => 'Chattogram'],
        'feni' => ['city' => 'Feni', 'state' => 'Chattogram'],
        'ফেনী' => ['city' => 'Feni', 'state' => 'Chattogram'],
        'brahmanbaria' => ['city' => 'Brahmanbaria', 'state' => 'Chattogram'],
        'ব্রাহ্মণবাড়িয়া' => ['city' => 'Brahmanbaria', 'state' => 'Chattogram'],
        'chandpur' => ['city' => 'Chandpur', 'state' => 'Chattogram'],
        'চাঁদপুর' => ['city' => 'Chandpur', 'state' => 'Chattogram'],
        'noakhali' => ['city' => 'Noakhali', 'state' => 'Chattogram'],
        'নোয়াখালী' => ['city' => 'Noakhali', 'state' => 'Chattogram'],
        'lakshmipur' => ['city' => 'Lakshmipur', 'state' => 'Chattogram'],
        'লক্ষ্মীপুর' => ['city' => 'Lakshmipur', 'state' => 'Chattogram'],
        'rangamati' => ['city' => 'Rangamati', 'state' => 'Chattogram'],
        'রাঙ্গামাটি' => ['city' => 'Rangamati', 'state' => 'Chattogram'],
        'bandarban' => ['city' => 'Bandarban', 'state' => 'Chattogram'],
        'বান্দরবান' => ['city' => 'Bandarban', 'state' => 'Chattogram'],
        'khagrachhari' => ['city' => 'Khagrachhari', 'state' => 'Chattogram'],
        'খাগড়াছড়ি' => ['city' => 'Khagrachhari', 'state' => 'Chattogram'],

        // Rajshahi Division
        'rajshahi' => ['city' => 'Rajshahi', 'state' => 'Rajshahi'],
        'রাজশাহী' => ['city' => 'Rajshahi', 'state' => 'Rajshahi'],
        'bogura' => ['city' => 'Bogura', 'state' => 'Rajshahi'],
        'bogra' => ['city' => 'Bogura', 'state' => 'Rajshahi'],
        'বগুড়া' => ['city' => 'Bogura', 'state' => 'Rajshahi'],
        'বগুড়া' => ['city' => 'Bogura', 'state' => 'Rajshahi'],
        'pabna' => ['city' => 'Pabna', 'state' => 'Rajshahi'],
        'পাবনা' => ['city' => 'Pabna', 'state' => 'Rajshahi'],
        'sirajganj' => ['city' => 'Sirajganj', 'state' => 'Rajshahi'],
        'সিরাজগঞ্জ' => ['city' => 'Sirajganj', 'state' => 'Rajshahi'],
        'naogaon' => ['city' => 'Naogaon', 'state' => 'Rajshahi'],
        'নওগাঁ' => ['city' => 'Naogaon', 'state' => 'Rajshahi'],
        'natore' => ['city' => 'Natore', 'state' => 'Rajshahi'],
        'নাটোর' => ['city' => 'Natore', 'state' => 'Rajshahi'],
        'chapainawabganj' => ['city' => 'Chapainawabganj', 'state' => 'Rajshahi'],
        'চাঁপাইনবাবগঞ্জ' => ['city' => 'Chapainawabganj', 'state' => 'Rajshahi'],
        'joypurhat' => ['city' => 'Joypurhat', 'state' => 'Rajshahi'],
        'জয়পুরহাট' => ['city' => 'Joypurhat', 'state' => 'Rajshahi'],

        // Khulna Division
        'khulna' => ['city' => 'Khulna', 'state' => 'Khulna'],
        'খুলনা' => ['city' => 'Khulna', 'state' => 'Khulna'],
        'jashore' => ['city' => 'Jashore', 'state' => 'Khulna'],
        'jessore' => ['city' => 'Jashore', 'state' => 'Khulna'],
        'যশোর' => ['city' => 'Jashore', 'state' => 'Khulna'],
        'satkhira' => ['city' => 'Satkhira', 'state' => 'Khulna'],
        'সাতক্ষীরা' => ['city' => 'Satkhira', 'state' => 'Khulna'],
        'bagerhat' => ['city' => 'Bagerhat', 'state' => 'Khulna'],
        'বাগেরহাট' => ['city' => 'Bagerhat', 'state' => 'Khulna'],
        'kushtia' => ['city' => 'Kushtia', 'state' => 'Khulna'],
        'কুষ্টিয়া' => ['city' => 'Kushtia', 'state' => 'Khulna'],
        'jhenaidah' => ['city' => 'Jhenaidah', 'state' => 'Khulna'],
        'ঝিনাইদহ' => ['city' => 'Jhenaidah', 'state' => 'Khulna'],
        'chuadanga' => ['city' => 'Chuadanga', 'state' => 'Khulna'],
        'চুয়াডাঙ্গা' => ['city' => 'Chuadanga', 'state' => 'Khulna'],
        'meherpur' => ['city' => 'Meherpur', 'state' => 'Khulna'],
        'মেহেরপুর' => ['city' => 'Meherpur', 'state' => 'Khulna'],
        'magura' => ['city' => 'Magura', 'state' => 'Khulna'],
        'মাগুরা' => ['city' => 'Magura', 'state' => 'Khulna'],
        'narail' => ['city' => 'Narail', 'state' => 'Khulna'],
        'নড়াইল' => ['city' => 'Narail', 'state' => 'Khulna'],

        // Sylhet Division
        'sylhet' => ['city' => 'Sylhet', 'state' => 'Sylhet'],
        'সিলেট' => ['city' => 'Sylhet', 'state' => 'Sylhet'],
        'moulvibazar' => ['city' => 'Moulvibazar', 'state' => 'Sylhet'],
        'মৌলভীবাজার' => ['city' => 'Moulvibazar', 'state' => 'Sylhet'],
        'habiganj' => ['city' => 'Habiganj', 'state' => 'Sylhet'],
        'হবিগঞ্জ' => ['city' => 'Habiganj', 'state' => 'Sylhet'],
        'sunamganj' => ['city' => 'Sunamganj', 'state' => 'Sylhet'],
        'সুনামগঞ্জ' => ['city' => 'Sunamganj', 'state' => 'Sylhet'],

        // Barishal Division
        'barishal' => ['city' => 'Barishal', 'state' => 'Barishal'],
        'barisal' => ['city' => 'Barishal', 'state' => 'Barishal'],
        'বরিশাল' => ['city' => 'Barishal', 'state' => 'Barishal'],
        'bhola' => ['city' => 'Bhola', 'state' => 'Barishal'],
        'ভোলা' => ['city' => 'Bhola', 'state' => 'Barishal'],
        'patuakhali' => ['city' => 'Patuakhali', 'state' => 'Barishal'],
        'পটুয়াখালী' => ['city' => 'Patuakhali', 'state' => 'Barishal'],
        'pirojpur' => ['city' => 'Pirojpur', 'state' => 'Barishal'],
        'পিরোজপুর' => ['city' => 'Pirojpur', 'state' => 'Barishal'],
        'barguna' => ['city' => 'Barguna', 'state' => 'Barishal'],
        'বরগুনা' => ['city' => 'Barguna', 'state' => 'Barishal'],
        'jhalokati' => ['city' => 'Jhalokati', 'state' => 'Barishal'],
        'ঝালকাঠি' => ['city' => 'Jhalokati', 'state' => 'Barishal'],

        // Rangpur Division
        'rangpur' => ['city' => 'Rangpur', 'state' => 'Rangpur'],
        'রংপুর' => ['city' => 'Rangpur', 'state' => 'Rangpur'],
        'dinajpur' => ['city' => 'Dinajpur', 'state' => 'Rangpur'],
        'দিনাজপুর' => ['city' => 'Dinajpur', 'state' => 'Rangpur'],
        'gaibandha' => ['city' => 'Gaibandha', 'state' => 'Rangpur'],
        'গাইবান্ধা' => ['city' => 'Gaibandha', 'state' => 'Rangpur'],
        'kurigram' => ['city' => 'Kurigram', 'state' => 'Rangpur'],
        'কুড়িগ্রাম' => ['city' => 'Kurigram', 'state' => 'Rangpur'],
        'nilphamari' => ['city' => 'Nilphamari', 'state' => 'Rangpur'],
        'নীলফামারী' => ['city' => 'Nilphamari', 'state' => 'Rangpur'],
        'lalmonirhat' => ['city' => 'Lalmonirhat', 'state' => 'Rangpur'],
        'লালমনিরহাট' => ['city' => 'Lalmonirhat', 'state' => 'Rangpur'],
        'panchagarh' => ['city' => 'Panchagarh', 'state' => 'Rangpur'],
        'পঞ্চগড়' => ['city' => 'Panchagarh', 'state' => 'Rangpur'],
        'thakurgaon' => ['city' => 'Thakurgaon', 'state' => 'Rangpur'],
        'ঠাকুরগাঁও' => ['city' => 'Thakurgaon', 'state' => 'Rangpur'],

        // Mymensingh Division
        'mymensingh' => ['city' => 'Mymensingh', 'state' => 'Mymensingh'],
        'ময়মনসিংহ' => ['city' => 'Mymensingh', 'state' => 'Mymensingh'],
        'jamalpur' => ['city' => 'Jamalpur', 'state' => 'Mymensingh'],
        'জামালপুর' => ['city' => 'Jamalpur', 'state' => 'Mymensingh'],
        'netrokona' => ['city' => 'Netrokona', 'state' => 'Mymensingh'],
        'নেত্রকোণা' => ['city' => 'Netrokona', 'state' => 'Mymensingh'],
        'sherpur' => ['city' => 'Sherpur', 'state' => 'Mymensingh'],
        'শেরপুর' => ['city' => 'Sherpur', 'state' => 'Mymensingh'],
    ];

    /**
     * Resolve and normalize location (Bangla/English District/Division) into standard English for Meta/Google/TikTok.
     *
     * @return array{city: string, state: string}
     */
    public static function resolveLocation(?string $cityOrDistrict, ?string $stateOrDivision = null): array
    {
        $raw = strtolower(trim((string) $cityOrDistrict));
        $raw = preg_replace('/(জেলা|বিভাগ|city|district|division|\(৳[^)]*\))/iu', '', $raw);
        $raw = trim($raw);

        if (isset(self::$districtMap[$raw])) {
            return self::$districtMap[$raw];
        }

        // Match partial
        foreach (self::$districtMap as $key => $val) {
            if (mb_stripos($raw, $key) !== false || mb_stripos($key, $raw) !== false) {
                return $val;
            }
        }

        // Check state/division
        $rawState = strtolower(trim((string) $stateOrDivision));
        $rawState = preg_replace('/(জেলা|বিভাগ|city|district|division)/iu', '', $rawState);
        $rawState = trim($rawState);

        if (isset(self::$districtMap[$rawState])) {
            return [
                'city'  => $val['city'] ?? ($raw ?: 'Dhaka'),
                'state' => self::$districtMap[$rawState]['state']
            ];
        }

        return [
            'city'  => $raw ? ucfirst($raw) : 'Dhaka',
            'state' => $rawState ? ucfirst($rawState) : 'Dhaka'
        ];
    }

    /**
     * @return array{first: string, last: string}
     */
    public static function splitName(?string $name): array
    {
        $name = trim((string) $name);
        if ($name === '') {
            return ['first' => '', 'last' => ''];
        }

        $parts = preg_split('/\s+/u', $name, 2);

        return [
            'first' => $parts[0] ?? '',
            'last'  => $parts[1] ?? '',
        ];
    }

    /**
     * Meta / TikTok — E.164 without + (e.g. 88017xxxxxxxx).
     */
    public static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return '';
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return '880'.substr($digits, 1);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '880'.$digits;
        }

        return $digits;
    }

    /**
     * Browser Pixel advanced matching (lowercase plain text — Meta hashes client-side).
     *
     * @return array<string, string>
     */
    public static function forBrowserPixel(array $data): array
    {
        $out = [];
        $name = self::splitName($data['name'] ?? ($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));

        if (! empty($data['email'])) {
            $out['em'] = strtolower(trim((string) $data['email']));
        }

        $phone = self::normalizePhone($data['phone'] ?? '');
        if ($phone !== '') {
            $out['ph'] = $phone;
        }

        if ($name['first'] !== '') {
            $out['fn'] = strtolower($name['first']);
        }
        if ($name['last'] !== '') {
            $out['ln'] = strtolower($name['last']);
        }

        $loc = self::resolveLocation($data['city'] ?? ($data['district'] ?? ($data['area'] ?? null)), $data['state'] ?? ($data['division'] ?? null));
        $out['ct'] = strtolower($loc['city']);
        $out['st'] = strtolower($loc['state']);
        $out['country'] = strtolower($data['country_code'] ?? 'bd');

        if (! empty($data['external_id'])) {
            $out['external_id'] = (string) $data['external_id'];
        }

        return array_filter($out, fn ($v) => $v !== '' && $v !== null);
    }

    /**
     * @return array<string, mixed>
     */
    public static function fromCustomer(?Customer $customer): array
    {
        if (! $customer) {
            return [];
        }

        $parts = self::splitName($customer->name);
        $loc = self::resolveLocation($customer->district ?? $customer->city ?? null, $customer->division ?? null);

        return array_filter([
            'email'        => $customer->email ?? null,
            'phone'        => $customer->phone ?? null,
            'name'         => $customer->name ?? null,
            'first_name'   => $parts['first'] ?: null,
            'last_name'    => $parts['last'] ?: null,
            'city'         => $loc['city'],
            'state'        => $loc['state'],
            'address'      => $customer->address ?? null,
            'external_id'  => (string) $customer->id,
            'country_code' => 'BD',
        ], fn ($v) => $v !== null && $v !== '');
    }

    /**
     * @return array<string, mixed>
     */
    public static function fromOrder(Order $order): array
    {
        $order->loadMissing(['shipping', 'customer']);
        $shipping = $order->shipping;
        $parts    = self::splitName($shipping?->name);

        $cityName = null;
        $stateName = null;

        if ($shipping?->district_id) {
            $district = \App\Models\DeliveryDistrict::find($shipping->district_id);
            $cityName = $district?->name;
            if ($district?->division_id) {
                $div = \App\Models\DeliveryDivision::find($district->division_id);
                $stateName = $div?->name;
            }
        }
        if ($shipping?->division_id && !$stateName) {
            $division = \App\Models\DeliveryDivision::find($shipping->division_id);
            $stateName = $division?->name;
        }

        // Fallback: extract from area string if needed
        if (! $cityName && $shipping?->area) {
            $rawArea = trim((string) $shipping->area);
            if (str_contains($rawArea, ',')) {
                $p = array_map('trim', explode(',', $rawArea));
                $cityName = $p[1] ?? $p[0];
                $stateName = $p[2] ?? $cityName;
            } else {
                $cityName = $rawArea;
            }
        }

        $loc = self::resolveLocation($cityName, $stateName);

        return array_filter([
            'email'        => $order->customer?->email,
            'phone'        => $shipping?->phone,
            'name'         => $shipping?->name,
            'first_name'   => $parts['first'] ?: null,
            'last_name'    => $parts['last'] ?: null,
            'city'         => $loc['city'],
            'state'        => $loc['state'],
            'address'      => $shipping?->address,
            'external_id'  => $order->customer_id ? (string) $order->customer_id : null,
            'country_code' => 'BD',
        ], fn ($v) => $v !== null && $v !== '');
    }

    /**
     * Facebook CAPI user_data (plain — FacebookCapiService hashes).
     *
     * @return array<string, mixed>
     */
    public static function forCapi(array $data, ?string $fbp = null, ?string $fbc = null): array
    {
        $parts = self::splitName($data['name'] ?? '');
        if (empty($parts['first']) && ! empty($data['first_name'])) {
            $parts['first'] = $data['first_name'];
            $parts['last']  = $data['last_name'] ?? '';
        }

        $req = request();

        if (empty($fbp)) {
            $fbp = $data['fbp'] ?? ($req ? ($req->cookie('_fbp') ?: ($req->cookie('fbp') ?: ($_COOKIE['_fbp'] ?? ($_COOKIE['fbp'] ?? null)))) : ($_COOKIE['_fbp'] ?? null));
        }

        if (empty($fbc)) {
            $fbc = $data['fbc'] ?? ($req ? ($req->cookie('_fbc') ?: ($req->cookie('fbc') ?: ($_COOKIE['_fbc'] ?? ($_COOKIE['fbc'] ?? null)))) : ($_COOKIE['_fbc'] ?? null));
            if (empty($fbc)) {
                $fbclid = ($req ? ($req->query('fbclid') ?: $req->input('fbclid')) : null) ?: session('fbclid');
                if ($fbclid) {
                    $fbc = 'fb.1.' . time() . '.' . $fbclid;
                }
            }
        }

        $ttclid = $data['ttclid'] ?? ($req ? ($req->cookie('ttclid') ?: ($req->query('ttclid') ?: $req->input('ttclid'))) : ($_COOKIE['ttclid'] ?? null)) ?: session('ttclid');
        $loc = self::resolveLocation($data['city'] ?? ($data['district'] ?? ($data['area'] ?? null)), $data['state'] ?? ($data['division'] ?? null));

        $user = array_filter([
            'email'             => $data['email'] ?? null,
            'phone'             => self::normalizePhone($data['phone'] ?? '') ?: null,
            'first_name'        => $parts['first'] ?: null,
            'last_name'         => $parts['last'] ?: null,
            'city'              => $loc['city'],
            'state'             => $loc['state'],
            'address'           => $data['address'] ?? null,
            'country_code'      => 'bd',
            'external_id'       => $data['external_id'] ?? null,
            'fbp'               => $fbp,
            'fbc'               => $fbc,
            'ttclid'            => $ttclid,
            'client_ip_address' => $data['client_ip_address'] ?? ($req ? $req->ip() : null),
            'client_user_agent' => $data['client_user_agent'] ?? ($req ? $req->userAgent() : null),
        ], fn ($v) => $v !== null && $v !== '');

        return $user;
    }
}
