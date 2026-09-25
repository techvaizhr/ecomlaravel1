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
            $this->normalizeBangla($rawQ),
            ...$this->generateBanglaCandidates($rawQ),
        ]));

        $results = [];
        $seen = [];

        // 1. Upazilas Search (division > district > upazila)
        $upazilas = DeliveryUpazila::query()
            ->with(['district.division'])
            ->where('status', 1)
            ->where(function ($query) use ($candidates) {
                foreach ($candidates as $cq) {
                    if (mb_strlen($cq) >= 2) {
                        $query->orWhere('name', 'like', "%{$cq}%");
                    }
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
                    if (mb_strlen($cq) >= 2) {
                        $query->orWhere('name', 'like', "%{$cq}%");
                    }
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
                    if (mb_strlen($cq) >= 2) {
                        $query->orWhere('name', 'like', "%{$cq}%");
                    }
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
        return str_replace(['ড়', 'ঢ়', 'য়', 'ণ', 'ী', 'ূ'], ['র', 'র', 'য', 'ন', 'ি', 'ু'], $str);
    }

    private function generateBanglaCandidates(string $str): array
    {
        $clean = strtolower(trim($str));
        if ($clean === '') return [];

        $list = [];

        $directMap = [
            // Divisions & 64 Districts
            'dhaka' => 'ঢাকা', 'rajshahi' => 'রাজশাহী', 'chattogram' => 'চট্টগ্রাম',
            'chittagong' => 'চট্টগ্রাম', 'ctg' => 'চট্টগ্রাম', 'khulna' => 'খুলনা',
            'barishal' => 'বরিশাল', 'barisal' => 'বরিশাল', 'sylhet' => 'সিলেট',
            'rangpur' => 'রংপুর', 'mymensingh' => 'ময়মনসিংহ', 'bagerhat' => 'বাগেরহাট',
            'bandarban' => 'বান্দরবান', 'barguna' => 'বরগুনা', 'bhola' => 'ভোলা',
            'bogura' => 'বগুড়া', 'bogra' => 'বগুড়া', 'brahmanbaria' => 'ব্রাহ্মণবাড়িয়া',
            'bbaria' => 'ব্রাহ্মণবাড়িয়া', 'chandpur' => 'চাঁদপুর', 'chapainawabganj' => 'চাঁপাইনবাবগঞ্জ',
            'nawabganj' => 'চাঁপাইনবাবগঞ্জ', 'chuadanga' => 'চুয়াডাঙ্গা', 'cumilla' => 'কুমিল্লা',
            'comilla' => 'কুমিল্লা', 'coxsbazar' => 'কক্সবাজার', 'cox\'s bazar' => 'কক্সবাজার',
            'dinajpur' => 'দিনাজপুর', 'faridpur' => 'ফরিদপুর', 'feni' => 'ফেনী',
            'gaibandha' => 'গাইবান্ধা', 'gazipur' => 'গাজীপুর', 'gopalganj' => 'গোপালগঞ্জ',
            'habiganj' => 'হবিগঞ্জ', 'jamalpur' => 'জামালপুর', 'jashore' => 'যশোর',
            'jessore' => 'যশোর', 'jhalokathi' => 'ঝালকাঠি', 'jhalakati' => 'ঝালকাঠি',
            'jhenaidah' => 'ঝিনাইদহ', 'joypurhat' => 'জয়পুরহাট', 'khagrachhari' => 'খাগড়াছড়ি',
            'khagrachari' => 'খাগড়াছড়ি', 'kishoreganj' => 'কিশোরগঞ্জ', 'kurigram' => 'কুড়িগ্রাম',
            'kushtia' => 'কুষ্টিয়া', 'lakshmipur' => 'লক্ষ্মীপুর', 'laxmipur' => 'লক্ষ্মীপুর',
            'lalmonirhat' => 'লালমনিরহাট', 'madaripur' => 'মাদারীপুর', 'magura' => 'মাগুরা',
            'manikganj' => 'মানিকগঞ্জ', 'meherpur' => 'মেহেরপুর', 'moulvibazar' => 'মৌলভীবাজার',
            'maulvibazar' => 'মৌলভীবাজার', 'munshiganj' => 'মুন্সীগঞ্জ', 'naogaon' => 'নওগাঁ',
            'narail' => 'নড়াইল', 'narayanganj' => 'নারায়ণগঞ্জ', 'narsingdi' => 'নরসিংদী',
            'natore' => 'নাটোর', 'netrokona' => 'নেত্রকোণা', 'netrakona' => 'নেত্রকোণা',
            'nilphamari' => 'নীলফামারী', 'noakhali' => 'নোয়াখালী', 'pabna' => 'পাবনা',
            'panchagarh' => 'পঞ্চগড়', 'patuakhali' => 'পটুয়াখালী', 'pirojpur' => 'পিরোজপুর',
            'rajbari' => 'রাজবাড়ী', 'rangamati' => 'রাঙ্গামাটি', 'satkhira' => 'সাতক্ষীরা',
            'shariatpur' => 'শরীয়তপুর', 'sherpur' => 'শেরপুর', 'sirajganj' => 'সিরাজগঞ্জ',
            'sunamganj' => 'সুনামগঞ্জ', 'tangail' => 'টাঙ্গাইল', 'thakurgaon' => 'ঠাকুরগাঁও',

            // Major Thanas / Upazilas
            'godagari' => 'গোদাগাড়ী', 'godagari' => 'গোদাগাড়ি', 'bagha' => 'বাঘা',
            'charghat' => 'চারঘাট', 'paba' => 'পবা', 'puthia' => 'পুঠিয়া', 'tanore' => 'তানোর',
            'mohanpur' => 'মোহনপুর', 'durgapur' => 'দুর্গাপুর', 'bagmara' => 'বাগমারা',
            'savar' => 'সাভার', 'dhamrai' => 'ধামরাই', 'keraniganj' => 'কেরানীগঞ্জ',
            'dohar' => 'দোহার', 'mirpur' => 'মিরপুর', 'uttara' => 'উত্তরা',
            'dhanmondi' => 'ধানমন্ডি', 'gulshan' => 'গুলশান', 'banani' => 'বনানী',
            'mohammadpur' => 'মোহাম্মদপুর', 'badda' => 'বাড্ডা', 'motijheel' => 'মতিঝিল',
            'tejgaon' => 'তেজগাঁও', 'demra' => 'ডেমরা', 'jatrabari' => 'যাত্রাবাড়ী',
            'khilgaon' => 'খিলগাঁও', 'rampura' => 'রামপুরা', 'paltan' => 'পল্টন',
            'shahbagh' => 'শাহবাগ', 'kafrul' => 'কাফরুল', 'cantonment' => 'ক্যান্টনমেন্ট',
            'pallabi' => 'পল্লবী', 'hazaribagh' => 'হাজারীবাগ', 'lalbagh' => 'লালবাগ',
            'kamrangirchar' => 'কামরাঙ্গীরচর', 'sutrapur' => 'সূত্রাপুর', 'kotwali' => 'কোতোয়ালী',
            'wari' => 'ওয়ারী', 'gendaria' => 'গেন্ডারিয়া', 'kadamtali' => 'কদমতলী',
            'shyampur' => 'শ্যামপুর', 'khilkhet' => 'খিলক্ষেত', 'vatara' => 'ভাটারা',
            'turag' => 'তুরাগ', 'uttarkhan' => 'উত্তরখান', 'dakshinkhan' => 'দক্ষিণখান',
            'adabor' => 'আদাবর', 'darussalam' => 'দারুস সালাম', 'rupnagar' => 'রূপনগর',
            'hatirjheel' => 'হাতিরঝিল', 'tongi' => 'টঙ্গী', 'kaliakair' => 'কালিয়াকৈর',
            'kapasia' => 'কাপাসিয়া', 'sreepur' => 'শ্রীপুর', 'kaliganj' => 'কালীগঞ্জ',
            'bandar' => 'বন্দর', 'fatullah' => 'ফতুল্লা', 'siddhirganj' => 'সিদ্ধিরগঞ্জ',
            'rupganj' => 'রূপগঞ্জ', 'sonargaon' => 'সোনারগাঁ', 'araihazar' => 'আড়াইহাজার',
            'singra' => 'সিংড়া', 'gurudaspur' => 'গুরুদাসপুর', 'baraigram' => 'বড়াইগ্রাম',
            'lalpur' => 'লালপুর', 'bagatipara' => 'বাগাতিপাড়া', 'ishwardi' => 'ঈশ্বরদী',
            'sreemangal' => 'শ্রীমঙ্গল', 'sitakunda' => 'সীতাকুণ্ড', 'hathazari' => 'হাটহাজারী',
            'raozan' => 'রাউজান', 'rangunia' => 'রাঙ্গুনিয়া', 'mirsharai' => 'মীরসরাই',
            'patiya' => 'পটিয়া', 'boalkhali' => 'বোয়ালখালী', 'anwara' => 'আনোয়ারা',
            'banshkhali' => 'বাঁশখালী', 'lohagara' => 'লোহাগাড়া', 'satkania' => 'সাতকানিয়া',
            'sandwip' => 'সন্দ্বীপ', 'teknaf' => 'টেকনাফ', 'chakaria' => 'চকরিয়া',
            'maheshkhali' => 'মহেশখালী', 'ramu' => 'রামু', 'ukhiya' => 'উখিয়া',
        ];

        // Direct matching
        if (isset($directMap[$clean])) {
            $list[] = $directMap[$clean];
        }

        // Prefix matching on dictionary
        foreach ($directMap as $k => $v) {
            if (str_starts_with($k, $clean)) {
                $list[] = $v;
            }
        }

        // Phonetic transliteration
        $patterns = [
            'kkh' => 'ক্ষ', 'ggy' => 'জ্ঞ', 'cch' => 'চ্ছ', 'ksh' => 'ক্ষ',
            'sh' => 'শ', 'ch' => 'চ', 'kh' => 'খ', 'gh' => 'ঘ', 'ng' => 'ঙ',
            'th' => 'থ', 'dh' => 'ধ', 'ph' => 'ফ', 'bh' => 'ভ', 'jh' => 'ঝ',
            'zh' => 'ঝ', 'ee' => 'ী', 'oo' => 'ূ', 'oi' => 'ৈ', 'ou' => 'ৌ',
            'au' => 'ৌ', 'ai' => 'াই', 'ei' => 'েই', 'aa' => 'া',
            'k' => 'ক', 'g' => 'গ', 'j' => 'জ', 't' => 'ট', 'd' => 'ড',
            'n' => 'ন', 'p' => 'প', 'f' => 'ফ', 'b' => 'ব', 'v' => 'ভ',
            'm' => 'ম', 'r' => 'র', 'l' => 'ল', 's' => 'স', 'h' => 'হ',
            'w' => 'ও', 'y' => 'য়', 'z' => 'য', 'a' => 'া', 'i' => 'ি',
            'u' => 'ু', 'e' => 'ে', 'o' => 'ো',
        ];
        $phonetic = str_replace(array_keys($patterns), array_values($patterns), $clean);
        if ($phonetic !== $clean) {
            $list[] = $phonetic;
            $list[] = str_replace('র', 'ড়', $phonetic);
            $list[] = str_replace('ড়', 'র', $phonetic);
        }

        return array_unique(array_filter($list));
    }
}
