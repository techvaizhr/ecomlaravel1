<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Review extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'ratting'     => 'integer',
            'review_date' => 'datetime',
            'created_at'  => 'datetime',
            'updated_at'  => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::saved(function ($review) {
            if ($review->product_id) {
                Cache::forget("product_reviews_stats_{$review->product_id}");
                Cache::forget("product_reviews_top3_{$review->product_id}");
            }
            Cache::forget('frontend_homepage_v4');
        });

        static::deleted(function ($review) {
            if ($review->product_id) {
                Cache::forget("product_reviews_stats_{$review->product_id}");
                Cache::forget("product_reviews_top3_{$review->product_id}");
            }
            Cache::forget('frontend_homepage_v4');
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

