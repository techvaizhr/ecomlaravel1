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

if (!function_exists('apply_date_filter')) {
    /**
     * Apply standard smart date filtering to an Eloquent Query Builder.
     * Supports:
     * - `date_preset`: today, yesterday, this_week, last_week, last_30_days, this_month, last_month, this_year, last_year
     * - `start_date` / `end_date`, `date_from` / `date_to`, `from_date` / `to_date`
     * - `date_range` format: 'YYYY-MM-DD to YYYY-MM-DD' or 'YYYY-MM-DD - YYYY-MM-DD'
     */
    function apply_date_filter($query, $request = null, string $column = 'created_at')
    {
        $request = $request ?? request();
        $preset = $request->input('date_preset');
        $startDate = $request->input('start_date') ?: ($request->input('date_from') ?: $request->input('from_date'));
        $endDate = $request->input('end_date') ?: ($request->input('date_to') ?: $request->input('to_date'));
        $dateRange = $request->input('date_range');

        if (!empty($dateRange)) {
            $parts = preg_split('/\s+(?:to|-)\s+/', trim($dateRange));
            if (count($parts) >= 2) {
                $startDate = trim($parts[0]);
                $endDate = trim($parts[1]);
            } elseif (count($parts) === 1) {
                $startDate = trim($parts[0]);
                $endDate = trim($parts[0]);
            }
        }

        if (!empty($preset)) {
            $now = \Carbon\Carbon::now();
            switch ($preset) {
                case 'today':
                    return $query->whereDate($column, \Carbon\Carbon::today());
                case 'yesterday':
                    return $query->whereDate($column, \Carbon\Carbon::yesterday());
                case 'this_week':
                    return $query->whereBetween($column, [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                case 'last_week':
                    $lastWeek = $now->copy()->subWeek();
                    return $query->whereBetween($column, [$lastWeek->startOfWeek(), $lastWeek->endOfWeek()]);
                case 'last_30_days':
                    return $query->whereBetween($column, [$now->copy()->subDays(30)->startOfDay(), $now->copy()->endOfDay()]);
                case 'this_month':
                    return $query->whereBetween($column, [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                case 'last_month':
                    $lastMonth = $now->copy()->subMonth();
                    return $query->whereBetween($column, [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()]);
                case 'this_year':
                    return $query->whereBetween($column, [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
                case 'last_year':
                    $lastYear = $now->copy()->subYear();
                    return $query->whereBetween($column, [$lastYear->startOfYear(), $lastYear->endOfYear()]);
            }
        }

        if (!empty($startDate) && !empty($endDate)) {
            return $query->whereBetween($column, [
                \Carbon\Carbon::parse($startDate)->startOfDay(),
                \Carbon\Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif (!empty($startDate)) {
            return $query->whereDate($column, '>=', $startDate);
        } elseif (!empty($endDate)) {
            return $query->whereDate($column, '<=', $endDate);
        }

        return $query;
    }
}

