<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_v1');
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_v2');
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_v3');
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_v4');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_v1');
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_v2');
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_v3');
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_v4');
        });
    }
}
