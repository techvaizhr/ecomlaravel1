<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'customer_id',
        'name',
        'phone',
        'address',
        'area',
        'division_id',
        'district_id',
        'upazila_id',
    ];
    
    public function division()
    {
        return $this->belongsTo(DeliveryDivision::class, 'division_id');
    }

    public function district()
    {
        return $this->belongsTo(DeliveryDistrict::class, 'district_id');
    }

    public function upazila()
    {
        return $this->belongsTo(DeliveryUpazila::class, 'upazila_id');
    }

    /**
     * Get complete, unified full address combining street address, area, and delivery location.
     */
    public function getFullAddressAttribute(): string
    {
        $addr = trim((string) ($this->address ?? ''));
        $area = trim((string) ($this->area ?? ''));

        $locParts = [];
        if ($this->upazila_id) {
            if ($this->relationLoaded('upazila') && $this->upazila) {
                $locParts[] = $this->upazila->name;
            } else {
                $u = DeliveryUpazila::find($this->upazila_id);
                if ($u && $u->name) $locParts[] = $u->name;
            }
        }

        if ($this->district_id) {
            if ($this->relationLoaded('district') && $this->district) {
                $locParts[] = $this->district->name;
            } else {
                $d = DeliveryDistrict::find($this->district_id);
                if ($d && $d->name) $locParts[] = $d->name;
            }
        }

        if ($this->division_id) {
            if ($this->relationLoaded('division') && $this->division) {
                $locParts[] = $this->division->name;
            } else {
                $div = DeliveryDivision::find($this->division_id);
                if ($div && $div->name) $locParts[] = $div->name;
            }
        }

        $locationLabel = implode(', ', array_unique(array_filter($locParts)));

        $extra = '';
        if ($area !== '' && !in_array(strtolower($area), ['digital / free shipping', 'n/a', 'null', 'none'])) {
            $extra = $area;
        } elseif ($locationLabel !== '') {
            $extra = $locationLabel;
        }

        if ($addr === '') {
            return $extra ?: '';
        }

        if ($extra === '') {
            return $addr;
        }

        // Avoid duplicate area/location if already mentioned inside $addr
        if (mb_stripos($addr, $extra) !== false) {
            return $addr;
        }

        $extraParts = array_map('trim', explode(',', $extra));
        $missing = [];
        foreach ($extraParts as $p) {
            if ($p !== '' && mb_stripos($addr, $p) === false) {
                $missing[] = $p;
            }
        }

        if (!empty($missing)) {
            return rtrim($addr, ', ') . ', ' . implode(', ', $missing);
        }

        return $addr;
    }
}

