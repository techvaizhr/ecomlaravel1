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
            'amount' => 'float',
            'status' => 'integer',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'custom_delivery_charge_id');
    }
}
