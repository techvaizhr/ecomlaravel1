<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DeliverySetting extends Model
{
    protected $table = 'delivery_settings';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'flat_rate_amount'        => 'float',
            'default_inside_charge'   => 'float',
            'default_outside_charge'  => 'float',
            'weight_base_cost'        => 'float',
            'weight_base_kg'          => 'float',
            'weight_extra_per_kg'     => 'float',
            'free_delivery_min_order' => 'float',
            'weight_tiers_json'       => 'array',
        ];
    }

    public static function instance(): self
    {
        return Cache::remember('delivery_setting_instance', 300, function () {
            $setting = self::first();
            if (!$setting) {
                $setting = self::create([
                    'active_method'          => 'area_based',
                    'flat_rate_amount'       => 80.00,
                    'default_inside_charge'  => 60.00,
                    'default_outside_charge' => 120.00,
                    'weight_base_cost'       => 60.00,
                    'weight_base_kg'         => 1.00,
                    'weight_extra_per_kg'    => 20.00,
                ]);
            }
            return $setting;
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('delivery_setting_instance');
        Cache::forget('delivery_divisions_active');
        Cache::forget('delivery_districts_all');
        Cache::forget('shipping_charges_active');
    }
}
