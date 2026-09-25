<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use App\Models\DeliveryUpazila;

class DeliveryAjaxController extends Controller
{
    public function districts(int $divisionId)
    {
        $division = DeliveryDivision::query()->whereKey($divisionId)->where('status', 1)->first();
        if (! $division) {
            return response()->json(['data' => []]);
        }

        $rows = DeliveryDistrict::query()
            ->where('division_id', $division->id)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'delivery_charge']);

        return response()->json(['data' => $rows]);
    }

    public function upazilas(int $districtId)
    {
        $district = DeliveryDistrict::query()->whereKey($districtId)->where('status', 1)->first();
        if (! $district) {
            return response()->json(['data' => []]);
        }

        $rows = DeliveryUpazila::query()
            ->where('district_id', $district->id)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $rows]);
    }

    public function search(\Illuminate\Http\Request $request)
    {
        $rawQ = trim((string) $request->get('q', ''));
        if ($rawQ === '') {
            return response()->json(['data' => []]);
        }

        $candidates = array_unique(array_filter([
            $rawQ,
            $this->phoneticToBangla($rawQ),
            $this->normalizeBangla($rawQ),
        ]));

        $results = [];
        $seen = [];

        // 1. Upazilas Search (division > district > upazila)
        $upazilas = DeliveryUpazila::query()
            ->with(['district.division'])
            ->where('status', 1)
            ->where(function ($query) use ($candidates) {
                foreach ($candidates as $cq) {
                    $query->orWhere('name', 'like', "%{$cq}%");
                }
            })
            ->limit(30)
            ->get();

        foreach ($upazilas as $upa) {
            $dist = $upa->district;
            $div = $dist ? $dist->division : null;
            if ($dist && $div) {
                $key = "{$div->id}-{$dist->id}-{$upa->id}";
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $results[] = [
                        'type' => 'upazila',
                        'division_id' => $div->id,
                        'division_name' => $div->name,
                        'district_id' => $dist->id,
                        'district_name' => $dist->name,
                        'upazila_id' => $upa->id,
                        'upazila_name' => $upa->name,
                        'delivery_charge' => (float) ($dist->delivery_charge ?? 0),
                        'full_path' => "{$div->name} > {$dist->name} > {$upa->name}",
                        'matched_name' => $upa->name,
                    ];
                }
            }
        }

        // 2. Districts Search
        $districts = DeliveryDistrict::query()
            ->with(['division', 'upazilas' => fn($q) => $q->where('status', 1)->orderBy('sort_order')->orderBy('name')])
            ->where('status', 1)
            ->where(function ($query) use ($candidates) {
                foreach ($candidates as $cq) {
                    $query->orWhere('name', 'like', "%{$cq}%");
                }
            })
            ->limit(15)
            ->get();

        foreach ($districts as $dist) {
            $div = $dist->division;
            if ($div) {
                $firstUpa = $dist->upazilas->first();
                $key = "{$div->id}-{$dist->id}-" . ($firstUpa ? $firstUpa->id : 0);
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $results[] = [
                        'type' => 'district',
                        'division_id' => $div->id,
                        'division_name' => $div->name,
                        'district_id' => $dist->id,
                        'district_name' => $dist->name,
                        'upazila_id' => $firstUpa ? $firstUpa->id : null,
                        'upazila_name' => $firstUpa ? $firstUpa->name : $dist->name,
                        'delivery_charge' => (float) ($dist->delivery_charge ?? 0),
                        'full_path' => "{$div->name} > {$dist->name}" . ($firstUpa ? " > {$firstUpa->name}" : ''),
                        'matched_name' => $dist->name,
                    ];
                }
            }
        }

        // 3. Divisions Search
        $divisions = DeliveryDivision::query()
            ->with(['districts' => fn($q) => $q->where('status', 1)->orderBy('sort_order')->orderBy('name')->with('upazilas')])
            ->where('status', 1)
            ->where(function ($query) use ($candidates) {
                foreach ($candidates as $cq) {
                    $query->orWhere('name', 'like', "%{$cq}%");
                }
            })
            ->limit(8)
            ->get();

        foreach ($divisions as $div) {
            $firstDist = $div->districts->first();
            $firstUpa = $firstDist ? $firstDist->upazilas->first() : null;
            if ($firstDist) {
                $key = "{$div->id}-{$firstDist->id}-" . ($firstUpa ? $firstUpa->id : 0);
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $results[] = [
                        'type' => 'division',
                        'division_id' => $div->id,
                        'division_name' => $div->name,
                        'district_id' => $firstDist->id,
                        'district_name' => $firstDist->name,
                        'upazila_id' => $firstUpa ? $firstUpa->id : null,
                        'upazila_name' => $firstUpa ? $firstUpa->name : $firstDist->name,
                        'delivery_charge' => (float) ($firstDist->delivery_charge ?? 0),
                        'full_path' => "{$div->name} > {$firstDist->name}" . ($firstUpa ? " > {$firstUpa->name}" : ''),
                        'matched_name' => $div->name,
                    ];
                }
            }
        }

        return response()->json(['data' => array_values($results)]);
    }

    private function normalizeBangla(string $str): string
    {
        return str_replace(['ড়', 'ঢ়', 'য়', 'ণ'], ['র', 'র', 'য', 'ন'], $str);
    }

    private function phoneticToBangla(string $str): string
    {
        $str = strtolower(trim($str));
        if ($str === '' || preg_match('/[\x{0980}-\x{09FF}]/u', $str)) {
            return $str;
        }

        // Direct common mappings
        $directMap = [
            'dhaka' => 'ঢাকা', 'rajshahi' => 'রাজশাহী', 'godagari' => 'গোদাগাড়ী',
            'bagha' => 'বাঘা', 'charghat' => 'চারঘাট', 'paba' => 'পবা', 'puthia' => 'পুঠিয়া',
            'tanore' => 'তানোর', 'mohanpur' => 'মোহনপুর', 'durgapur' => 'দুর্গাপুর',
            'chattogram' => 'চট্টগ্রাম', 'chittagong' => 'চট্টগ্রাম', 'khulna' => 'খুলনা',
            'barishal' => 'বরিশাল', 'barisal' => 'বরিশাল', 'sylhet' => 'সিলেট',
            'rangpur' => 'রংপুর', 'mymensingh' => 'ময়মনসিংহ', 'cumilla' => 'কুমিল্লা',
            'comilla' => 'কুমিল্লা', 'gazipur' => 'গাজীপুর', 'savar' => 'সাভার',
            'mirpur' => 'মিরপুর', 'uttara' => 'উত্তরা', 'dhanmondi' => 'ধানমন্ডি',
            'gulshan' => 'গুলশান', 'bogura' => 'বগুড়া', 'bogra' => 'বগুড়া',
            'natore' => 'নাটোর', 'singra' => 'সিংড়া', 'pabna' => 'পাবনা',
            'naogaon' => 'নওগাঁ', 'sirajganj' => 'সিরাজগঞ্জ', 'tangail' => 'টাঙ্গাইল',
            'jashore' => 'যশোর', 'jessore' => 'যশোর', 'kushtia' => 'কুষ্টিয়া',
            'feni' => 'ফেনী', 'noakhali' => 'নোয়াখালী', 'coxsbazar' => 'কক্সবাজার',
            'dinajpur' => 'দিনাজপুর', 'jamalpur' => 'জামালপুর', 'faridpur' => 'ফরিদপুর',
            'narayanganj' => 'নারায়ণগঞ্জ', 'narail' => 'নড়াইল', 'satkhira' => 'সাতক্ষীরা',
            'bagerhat' => 'বাগেরহাট', 'chuadanga' => 'চুয়াডাঙ্গা', 'meherpur' => 'মেহেরপুর',
            'magura' => 'মাগুরা', 'jhenaidah' => 'ঝিনাইদহ', 'bhola' => 'ভোলা',
            'patuakhali' => 'পটুয়াখালী', 'barguna' => 'বরগুনা', 'pirojpur' => 'পিরোজপুর',
            'jhalokathi' => 'ঝালকাঠি', 'habiganj' => 'হবিগঞ্জ', 'moulvibazar' => 'মৌলভীবাজার',
            'sunamganj' => 'সুনামগঞ্জ', 'brahmanbaria' => 'ব্রাহ্মণবাড়িয়া', 'chandpur' => 'চাঁদপুর',
            'lakshmipur' => 'লক্ষ্মীপুর', 'bandarban' => 'বান্দরবান', 'rangamati' => 'রাঙ্গামাটি',
            'khagrachhari' => 'খাগড়াছড়ি', 'kurigram' => 'কুড়িগ্রাম', 'gaibandha' => 'গাইবান্ধা',
            'lalmonirhat' => 'লালমনিরহাট', 'nilphamari' => 'নীলফামারী', 'panchagarh' => 'পঞ্চগড়',
            'thakurgaon' => 'ঠাকুরগাঁও', 'sherpur' => 'শেরপুর', 'netrokona' => 'নেত্রকোণা',
            'kishoreganj' => 'কিশোরগঞ্জ', 'manikganj' => 'মানিকগঞ্জ', 'munshiganj' => 'মুন্সীগঞ্জ',
            'narsingdi' => 'নরসিংদী', 'gopalganj' => 'গোপালগঞ্জ', 'madaripur' => 'মাদারীপুর',
            'shariatpur' => 'শরীয়তপুর', 'rajbari' => 'রাজবাড়ী', 'chapainawabganj' => 'চাঁপাইনবাবগঞ্জ',
            'joypurhat' => 'জয়পুরহাট',
        ];

        if (isset($directMap[$str])) {
            return $directMap[$str];
        }

        // Substring / partial direct match
        foreach ($directMap as $en => $bn) {
            if (str_starts_with($en, $str)) {
                return $bn;
            }
        }

        // Rule-based phonetic transliteration
        $patterns = [
            'kkh' => 'ক্ষ', 'ggy' => 'জ্ঞ', 'cch' => 'চ্ছ', 'kkh' => 'ক্ষ',
            'sh' => 'শ', 'ch' => 'চ', 'kh' => 'খ', 'gh' => 'ঘ', 'ng' => 'ঙ',
            'th' => 'থ', 'dh' => 'ধ', 'ph' => 'ফ', 'bh' => 'ভ', 'jh' => 'ঝ',
            'zh' => 'ঝ', 'ee' => 'ী', 'oo' => 'ূ', 'oi' => 'ৈ', 'ou' => 'ৌ',
            'k' => 'ক', 'g' => 'গ', 'j' => 'জ', 't' => 'ট', 'd' => 'ড',
            'n' => 'ন', 'p' => 'প', 'f' => 'ফ', 'b' => 'ব', 'v' => 'ভ',
            'm' => 'ম', 'r' => 'র', 'l' => 'ল', 's' => 'স', 'h' => 'হ',
            'w' => 'ও', 'y' => 'য়', 'z' => 'য', 'a' => 'া', 'i' => 'ি',
            'u' => 'ু', 'e' => 'ে', 'o' => 'ো',
        ];

        $out = str_replace(array_keys($patterns), array_values($patterns), $str);
        return $out;
    }
}
