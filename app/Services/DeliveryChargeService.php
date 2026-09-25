<?php

namespace App\Services;

use App\Models\CustomDeliveryCharge;
use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use App\Models\DeliverySetting;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DeliveryChargeService
{
    /**
     * Calculate delivery charge for cart or item collection and given destination.
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

        // 1. Global Free Delivery Check
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
            ? Product::with('customDeliveryCharge')->whereIn('id', $productIds)->get()->keyBy('id')
            : collect();

        // Check if all items are digital
        $allDigital = true;
        foreach ($itemsList as $it) {
            $pid = is_object($it) ? ($it->id ?? null) : ($it['id'] ?? null);
            $prod = $productsMap->get($pid);
            $isDigital = (int) ($it->options->is_digital ?? $prod->is_digital ?? 0) === 1;
            if (!$isDigital) {
                $allDigital = false;
                break;
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

        // Check physical items & calculate individual charges
        $allPhysicalFree = true;
        $totalWeight = 0.0;
        $productCalculatedCharges = [];

        foreach ($itemsList as $it) {
            $pid = is_object($it) ? ($it->id ?? null) : ($it['id'] ?? null);
            $qty = is_object($it) ? (int) ($it->qty ?? 1) : (int) ($it['qty'] ?? 1);
            $prod = $productsMap->get($pid);
            if (!$prod) continue;

            $isDigital = (int) ($it->options->is_digital ?? $prod->is_digital ?? 0) === 1;
            if ($isDigital) continue;

            $pWeight = (float) ($prod->weight ?? 0);
            $totalWeight += ($pWeight * $qty);

            $pType = $prod->delivery_charge_type ?: 'global';
            $isProdFree = ($pType === 'free' || (int) ($prod->free_delivery ?? 0) === 1);

            if (!$isProdFree) {
                $allPhysicalFree = false;
            }

            // Calculate per-product individual charge based on rule
            $individualCharge = 0.0;
            if ($isProdFree) {
                $individualCharge = 0.0;
            } elseif ($pType === 'custom' && $prod->custom_delivery_charge_id) {
                $customCharge = $prod->customDeliveryCharge;
                if ($customCharge && $customCharge->status) {
                    $individualCharge = (float) $customCharge->amount;
                } else {
                    // Fallback to global if custom charge is inactive/deleted
                    $individualCharge = self::resolveProductGlobalCharge($prod, $pWeight, $qty, $globalMethod, $divisionId, $districtId, $settings);
                }
            } elseif ($pType === 'weight_based') {
                $individualCharge = self::calculateWeightCost($pWeight * $qty, $settings);
            } else {
                // Global method for this product
                $individualCharge = self::resolveProductGlobalCharge($prod, $pWeight, $qty, $globalMethod, $divisionId, $districtId, $settings);
            }

            $productCalculatedCharges[] = [
                'product_id' => $prod->id,
                'name'       => $prod->name,
                'type'       => $pType,
                'charge'     => $individualCharge,
            ];
        }

        if ($allPhysicalFree) {
            return [
                'charge'        => 0.0,
                'active_method' => 'free_delivery',
                'is_free'       => true,
                'total_weight'  => $totalWeight,
                'details'       => ['reason' => 'All physical products have Free Delivery'],
            ];
        }

        // Global weight based calculation across combined cart weight
        $globalWeightCharge = 0.0;
        if ($globalMethod === 'weight_based') {
            $globalWeightCharge = self::calculateWeightCost($totalWeight, $settings);
        }

        // Multi-Product Rule:
        // "sokol delivery charge er moddhe besi ta apply hobe" (Highest charge applies)
        $maxCharge = 0.0;
        foreach ($productCalculatedCharges as $pc) {
            if ($pc['charge'] > $maxCharge) {
                $maxCharge = $pc['charge'];
            }
        }

        if ($globalMethod === 'weight_based' && $globalWeightCharge > $maxCharge) {
            $maxCharge = $globalWeightCharge;
        }

        // If no custom charge was resolved, apply global resolution
        if ($maxCharge <= 0 && !$allPhysicalFree) {
            if ($globalMethod === 'flat_rate') {
                $maxCharge = (float) $settings->flat_rate_amount;
            } elseif ($globalMethod === 'area_based') {
                $maxCharge = self::resolveAreaCharge($divisionId, $districtId, $settings);
            } elseif ($globalMethod === 'weight_based') {
                $maxCharge = self::calculateWeightCost($totalWeight, $settings);
            }
        }

        return [
            'charge'        => (float) $maxCharge,
            'active_method' => $globalMethod,
            'is_free'       => ($maxCharge <= 0),
            'total_weight'  => $totalWeight,
            'details'       => $productCalculatedCharges,
        ];
    }

    /**
     * Resolve area charge for division & district hierarchy without any legacy hardcoded dhaka checks.
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
     * Helper to resolve global method charge for a product.
     */
    protected static function resolveProductGlobalCharge(
        Product $prod,
        float $pWeight,
        int $qty,
        string $globalMethod,
        ?int $divisionId,
        ?int $districtId,
        DeliverySetting $settings
    ): float {
        if ($globalMethod === 'flat_rate') {
            return (float) $settings->flat_rate_amount;
        } elseif ($globalMethod === 'weight_based') {
            return self::calculateWeightCost($pWeight * $qty, $settings);
        } elseif ($globalMethod === 'area_based') {
            return self::resolveAreaCharge($divisionId, $districtId, $settings);
        }
        return 0.0;
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
