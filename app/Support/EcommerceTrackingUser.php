<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Order;

class EcommerceTrackingUser
{
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

        if (! empty($data['city'])) {
            $out['ct'] = strtolower(trim((string) $data['city']));
        }

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

        return array_filter([
            'email'        => $customer->email ?? null,
            'phone'        => $customer->phone ?? null,
            'name'         => $customer->name ?? null,
            'first_name'   => $parts['first'] ?: null,
            'last_name'    => $parts['last'] ?: null,
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
        }
        if ($shipping?->division_id) {
            $division = \App\Models\DeliveryDivision::find($shipping->division_id);
            $stateName = $division?->name;
        }

        // Fallback: if no district_id was saved, extract clean city from area / address
        if (! $cityName && $shipping?->area) {
            $rawArea = trim((string) $shipping->area);
            if (str_contains($rawArea, ',')) {
                $p = array_map('trim', explode(',', $rawArea));
                $cityName = $p[1] ?? $p[0];
                $stateName = $p[2] ?? $cityName;
            } elseif (stripos($rawArea, 'Dhaka') !== false || stripos($rawArea, 'ঢাকা') !== false) {
                $cityName = 'Dhaka';
                $stateName = 'Dhaka';
            } else {
                $cityName = $rawArea;
            }
        }

        return array_filter([
            'email'        => $order->customer?->email,
            'phone'        => $shipping?->phone,
            'name'         => $shipping?->name,
            'first_name'   => $parts['first'] ?: null,
            'last_name'    => $parts['last'] ?: null,
            'city'         => $cityName ?: 'Dhaka',
            'state'        => $stateName ?: ($cityName ?: 'Dhaka'),
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

        $user = array_filter([
            'email'             => $data['email'] ?? null,
            'phone'             => self::normalizePhone($data['phone'] ?? '') ?: null,
            'first_name'        => $parts['first'] ?: null,
            'last_name'         => $parts['last'] ?: null,
            'city'              => $data['city'] ?? null,
            'state'             => $data['state'] ?? null,
            'country_code'      => 'BD',
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
