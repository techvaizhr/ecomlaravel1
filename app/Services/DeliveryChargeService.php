<?php

namespace App\Services;

use App\Models\CustomDeliveryCharge;
use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use App\Models\DeliverySetting;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Collection;

class DeliveryChargeService
{
    /**
     * Calculate delivery charge for cart or item collection based on Delivery Settings and Custom Rules.
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

        // Load active custom delivery charges (assigned to category, brand, or specific products)
        $customCharges = \Illuminate\Support\Facades\Schema::hasTable('custom_delivery_charges')
            ? CustomDeliveryCharge::where('status', 1)->get()
            : collect();

        $productCalculatedCharges = [];
        $hasAnyCustomMatch = false;

        foreach ($itemsList as $it) {
            $pid = is_object($it) ? ($it->id ?? null) : ($it['id'] ?? null);
            $qty = is_object($it) ? (int) ($it->qty ?? 1) : (int) ($it['qty'] ?? 1);
            $prod = $productsMap->get($pid);
            if (!$prod) continue;

            $isDigital = (int) ($it->options->is_digital ?? $prod->is_digital ?? 0) === 1;
            if ($isDigital) continue;

            $pWeight = (float) ($prod->weight ?? 0);

            // 1. TOP PRIORITY: Check matching custom charge for this product
            $matchedCustomCharge = null;
            foreach ($customCharges as $cc) {
                if ($cc->matchesProduct($prod)) {
                    // If multiple match, choose highest charge
                    if ($matchedCustomCharge === null || $cc->amount > $matchedCustomCharge->amount) {
                        $matchedCustomCharge = $cc;
                    }
                }
            }

            if ($matchedCustomCharge !== null) {
                $hasAnyCustomMatch = true;
                $itemCharge = (float) $matchedCustomCharge->amount;
                $itemMethod = 'custom: ' . $matchedCustomCharge->name;
            } elseif ((int) ($prod->free_delivery ?? 0) === 1) {
                // If product has promo free delivery and not overridden by custom charge
                $itemCharge = 0.0;
                $itemMethod = 'product_free';
            } else {
                // Fallback to global active system mode
                if ($globalMethod === 'free_delivery') {
                    $itemCharge = 0.0;
                    $itemMethod = 'free_delivery';
                } elseif ($globalMethod === 'flat_rate') {
                    $itemCharge = (float) $settings->flat_rate_amount;
                    $itemMethod = 'flat_rate';
                } elseif ($globalMethod === 'weight_based') {
                    $itemCharge = self::calculateWeightCost($pWeight * $qty, $settings);
                    $itemMethod = 'weight_based';
                } else {
                    $itemCharge = self::resolveAreaCharge($divisionId, $districtId, $settings);
                    $itemMethod = 'area_based';
                }
            }

            $productCalculatedCharges[] = [
                'product_id' => $prod->id,
                'name'       => $prod->name,
                'charge'     => $itemCharge,
                'method'     => $itemMethod,
            ];
        }

        // Multi-Product Rule:
        // Highest (maximum) delivery charge among items in cart applies
        $maxCharge = 0.0;
        foreach ($productCalculatedCharges as $pc) {
            if ($pc['charge'] > $maxCharge) {
                $maxCharge = $pc['charge'];
            }
        }

        // If global method is weight_based and no custom charges, calculate total combined weight charge
        if ($globalMethod === 'weight_based') {
            $totalCartWeightCharge = self::calculateWeightCost($totalWeight, $settings);
            if ($totalCartWeightCharge > $maxCharge) {
                $maxCharge = $totalCartWeightCharge;
            }
        }

        // If no items produced a charge and not free delivery, apply global resolution
        if ($maxCharge <= 0 && $globalMethod !== 'free_delivery' && !$allPhysicalFree) {
            if ($globalMethod === 'flat_rate') {
                $maxCharge = (float) $settings->flat_rate_amount;
            } elseif ($globalMethod === 'area_based') {
                $maxCharge = self::resolveAreaCharge($divisionId, $districtId, $settings);
            }
        }

        return [
            'charge'        => (float) $maxCharge,
            'active_method' => $hasAnyCustomMatch ? 'custom_priority' : $globalMethod,
            'is_free'       => ($maxCharge <= 0),
            'total_weight'  => $totalWeight,
            'details'       => $productCalculatedCharges,
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

        // 3. Fallback: 0 if not defined
        return 0.00;
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
