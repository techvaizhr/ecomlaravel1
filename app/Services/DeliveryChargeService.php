<?php

namespace App\Services;

use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use App\Models\DeliverySetting;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Collection;

class DeliveryChargeService
{
    /**
     * Calculate delivery charge for cart or item collection based on Delivery Settings.
     *
     * @param Collection|array|null $items Items in cart (or default to Cart::instance('shopping')->content())
     * @param int|null $divisionId
     * @param int|null $districtId
     * @param int|null $upazilaId
     * @return array ['charge' => float, 'active_method' => string, 'is_free' => bool, 'total_weight' => float, 'details' => array]
     */
    public static function calculate(
        $items = null,
        ?int $divisionId = null,
        ?int $districtId = null,
        ?int $upazilaId = null
    ): array {
        $settings = DeliverySetting::instance();
        $globalMethod = $settings->active_method ?: 'area_based';

        if ($items === null) {
            $items = Cart::instance('shopping')->content();
        }

        if ($items instanceof Collection) {
            $itemsList = $items->values()->all();
        } else {
            $itemsList = is_array($items) ? $items : [];
        }

        // Empty items check
        if (empty($itemsList)) {
            return [
                'charge'        => 0.0,
                'active_method' => $globalMethod,
                'is_free'       => ($globalMethod === 'free_delivery'),
                'total_weight'  => 0.0,
                'details'       => [],
            ];
        }

        // 1. Global Free Delivery Mode
        if ($globalMethod === 'free_delivery') {
            return [
                'charge'        => 0.0,
                'active_method' => 'free_delivery',
                'is_free'       => true,
                'total_weight'  => 0.0,
                'details'       => ['reason' => 'Global Free Delivery is Active'],
            ];
        }

        // Fetch products in 1 query
        $productIds = collect($itemsList)->map(function ($it) {
            return is_object($it) ? ($it->id ?? null) : ($it['id'] ?? null);
        })->filter()->unique()->all();

        $productsMap = !empty($productIds)
            ? Product::whereIn('id', $productIds)->get()->keyBy('id')
            : collect();

        // Check if all items are digital
        $allDigital = true;
        $allPhysicalFree = true;
        $totalWeight = 0.0;

        foreach ($itemsList as $it) {
            $pid = is_object($it) ? ($it->id ?? null) : ($it['id'] ?? null);
            $qty = is_object($it) ? (int) ($it->qty ?? 1) : (int) ($it['qty'] ?? 1);
            $prod = $productsMap->get($pid);

            $isDigital = (int) ($it->options->is_digital ?? $prod?->is_digital ?? 0) === 1;
            if (!$isDigital) {
                $allDigital = false;
                $pWeight = (float) ($prod?->weight ?? 0);
                $totalWeight += ($pWeight * $qty);

                if ((int) ($prod?->free_delivery ?? 0) !== 1) {
                    $allPhysicalFree = false;
                }
            }
        }

        if ($allDigital) {
            return [
                'charge'        => 0.0,
                'active_method' => 'digital_free',
                'is_free'       => true,
                'total_weight'  => 0.0,
                'details'       => ['reason' => 'All items are digital downloads'],
            ];
        }

        if ($allPhysicalFree) {
            return [
                'charge'        => 0.0,
                'active_method' => 'free_delivery',
                'is_free'       => true,
                'total_weight'  => $totalWeight,
                'details'       => ['reason' => 'All items have Free Delivery promotion'],
            ];
        }

        // Calculate delivery charge according to active mode in Settings
        $finalCharge = 0.0;

        if ($globalMethod === 'flat_rate') {
            $finalCharge = (float) $settings->flat_rate_amount;
        } elseif ($globalMethod === 'weight_based') {
            $finalCharge = self::calculateWeightCost($totalWeight, $settings);
        } else {
            // Default: area_based (Division & District hierarchy)
            $finalCharge = self::resolveAreaCharge($divisionId, $districtId, $settings);
        }

        return [
            'charge'        => (float) $finalCharge,
            'active_method' => $globalMethod,
            'is_free'       => ($finalCharge <= 0),
            'total_weight'  => $totalWeight,
            'details'       => [
                'method'       => $globalMethod,
                'charge'       => $finalCharge,
                'total_weight' => $totalWeight,
            ],
        ];
    }

    /**
     * Resolve area charge for division & district hierarchy without any legacy hardcoded checks.
     */
    public static function resolveAreaCharge(?int $divisionId, ?int $districtId, ?DeliverySetting $settings = null): float
    {
        $settings = $settings ?: DeliverySetting::instance();

        // 1. Check district specific override
        if ($districtId) {
            $dist = DeliveryDistrict::find($districtId);
            if ($dist) {
                if ((float) $dist->delivery_charge > 0) {
                    return (float) $dist->delivery_charge;
                }
                if (!$divisionId && $dist->division_id) {
                    $divisionId = $dist->division_id;
                }
                if ($dist->division && (float) $dist->division->delivery_charge > 0) {
                    return (float) $dist->division->delivery_charge;
                }
            }
        }

        // 2. Check division rate
        if ($divisionId) {
            $div = DeliveryDivision::find($divisionId);
            if ($div && (float) $div->delivery_charge > 0) {
                return (float) $div->delivery_charge;
            }
        }

        // 3. Fallback to default area charge (e.g. 100 Tk)
        return (float) ($settings->default_area_charge ?: 100.00);
    }

    /**
     * Calculate cost based on total weight.
     */
    public static function calculateWeightCost(float $weight, ?DeliverySetting $settings = null): float
    {
        $settings = $settings ?: DeliverySetting::instance();
        $weight = max(0.1, $weight);

        $baseKg    = (float) ($settings->weight_base_kg ?: 1.0);
        $baseCost  = (float) ($settings->weight_base_cost ?: 60.0);
        $extraRate = (float) ($settings->weight_extra_per_kg ?: 20.0);

        if ($weight <= $baseKg) {
            return $baseCost;
        }

        $extraKg = ceil($weight - $baseKg);
        return $baseCost + ($extraKg * $extraRate);
    }
}
