<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDeliveryCharge extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount'       => 'float',
            'status'       => 'integer',
            'category_ids' => 'array',
            'brand_ids'    => 'array',
            'product_ids'  => 'array',
        ];
    }

    /**
     * Check if a given product matches this custom delivery charge rule.
     */
    public function matchesProduct($product): bool
    {
        if (!$this->status) {
            return false;
        }

        $pId = is_object($product) ? ($product->id ?? null) : ($product['id'] ?? null);
        $catId = is_object($product) ? ($product->category_id ?? null) : ($product['category_id'] ?? null);
        $brandId = is_object($product) ? ($product->brand_id ?? null) : ($product['brand_id'] ?? null);

        // 1. Direct Product Match
        if (!empty($this->product_ids) && is_array($this->product_ids) && $pId) {
            if (in_array((int) $pId, array_map('intval', $this->product_ids), true)) {
                return true;
            }
        }

        // 2. Category Match
        if (!empty($this->category_ids) && is_array($this->category_ids) && $catId) {
            if (in_array((int) $catId, array_map('intval', $this->category_ids), true)) {
                return true;
            }
        }

        // 3. Brand Match
        if (!empty($this->brand_ids) && is_array($this->brand_ids) && $brandId) {
            if (in_array((int) $brandId, array_map('intval', $this->brand_ids), true)) {
                return true;
            }
        }

        return false;
    }
}
