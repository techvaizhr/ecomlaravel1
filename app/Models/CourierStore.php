<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierStore extends Model
{
    use HasFactory;

    protected $table = 'courier_stores';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active'  => 'boolean',
            'raw_data'   => 'array',
        ];
    }

    /**
     * Scope for courier type
     */
    public function scopeCourier($query, string $type)
    {
        return $query->where('courier_type', strtolower($type));
    }

    /**
     * Scope for active stores
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for default store
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Helper to set a store as default for its courier type
     */
    public function makeDefault(): bool
    {
        // Remove default from all other stores of same courier
        self::where('courier_type', $this->courier_type)->update(['is_default' => false]);

        $this->is_default = true;
        $saved = $this->save();

        // Also update in courierapis if courier config exists
        $api = Courierapi::where('type', $this->courier_type)->first();
        if ($api) {
            $api->default_store_id = $this->store_id;
            $api->save();
        }

        return $saved;
    }
}
