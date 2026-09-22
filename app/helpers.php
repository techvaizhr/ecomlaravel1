<?php

use App\Models\ResellerLandingPage;

if (!function_exists('landing_url')) {
    /**
     * Generate URL for reseller landing - uses custom domain when applicable.
     */
    function landing_url(string $slug, string $path = ''): string
    {
        $landing = ResellerLandingPage::where('slug', $slug)->first();
        $host = strtolower(request()->getHost());

        if ($landing && $landing->custom_domain && strtolower($landing->custom_domain) === $host) {
            return $path === '' ? url('/') : url($path);
        }

        $base = '/r/' . $slug;
        return $path === '' ? url($base) : url(rtrim($base . '/' . ltrim($path, '/'), '/'));
    }
}

if (!function_exists('admin_per_page')) {
    /**
     * Get or set current per-page count for admin tables.
     * Supports query string `per_page` and falls back to session or default.
     * When 'all' is selected, returns 5000 to display all records.
     */
    function admin_per_page(int $default = 10, string $sessionKey = 'admin_order_per_page'): int
    {
        $allowed = [10, 20, 25, 50, 100, 200, 500];
        $val = request()->input('per_page');

        if ($val !== null) {
            if ($val === 'all') {
                session([$sessionKey => 'all', 'admin_per_page' => 'all']);
                return 5000;
            }
            if (is_numeric($val)) {
                $val = (int) $val;
                if (in_array($val, $allowed, true) || ($val > 0 && $val <= 5000)) {
                    session([$sessionKey => $val, 'admin_per_page' => $val]);
                    return $val;
                }
            }
        }

        $sessionVal = session($sessionKey, session('admin_per_page', $default));
        if ($sessionVal === 'all' || (is_numeric($sessionVal) && (int) $sessionVal >= 5000)) {
            return 5000;
        }

        return (int) $sessionVal;
    }
}

