<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomDeliveryCharge;
use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use App\Models\DeliverySetting;
use Illuminate\Http\Request;
use Toastr;

class DeliverySettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $setting = DeliverySetting::instance();
        $divisions = DeliveryDivision::with(['districts' => function ($q) {
            $q->orderBy('sort_order')->orderBy('name');
        }])->ordered()->get();

        $customCharges = CustomDeliveryCharge::orderBy('id', 'desc')->get();

        return view('backEnd.delivery_settings.index', compact('setting', 'divisions', 'customCharges'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'active_method'       => 'required|in:area_based,flat_rate,free_delivery,weight_based',
            'flat_rate_amount'    => 'nullable|numeric|min:0',
            'default_area_charge' => 'nullable|numeric|min:0',
            'weight_base_cost'    => 'nullable|numeric|min:0',
            'weight_base_kg'      => 'nullable|numeric|min:0.1',
            'weight_extra_per_kg' => 'nullable|numeric|min:0',
        ]);

        $setting = DeliverySetting::first() ?: new DeliverySetting();
        $setting->active_method       = $request->active_method;
        $setting->flat_rate_amount    = (float) ($request->flat_rate_amount ?? 100.00);
        $setting->default_area_charge = (float) ($request->default_area_charge ?? 100.00);
        $setting->weight_base_cost    = (float) ($request->weight_base_cost ?? 60.00);
        $setting->weight_base_kg      = (float) ($request->weight_base_kg ?? 1.00);
        $setting->weight_extra_per_kg = (float) ($request->weight_extra_per_kg ?? 20.00);
        $setting->save();

        DeliverySetting::clearCache();

        Toastr::success('ডেলিভারি চার্জ সেটিংস সফলভাবে আপডেট হয়েছে!', 'Success');
        return redirect()->back();
    }

    public function updateAreaRates(Request $request)
    {
        // 1. Division rates
        if ($request->has('divisions') && is_array($request->divisions)) {
            foreach ($request->divisions as $divId => $data) {
                $charge = isset($data['charge']) && $data['charge'] !== '' ? (float) $data['charge'] : 0.00;
                DeliveryDivision::where('id', $divId)->update(['delivery_charge' => $charge]);
            }
        }

        // 2. District override rates
        if ($request->has('districts') && is_array($request->districts)) {
            foreach ($request->districts as $distId => $data) {
                $charge = isset($data['charge']) && $data['charge'] !== '' ? (float) $data['charge'] : 0.00;
                DeliveryDistrict::where('id', $distId)->update(['delivery_charge' => $charge]);
            }
        }

        DeliverySetting::clearCache();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'এরিয়া ভিত্তিক রেট সফলভাবে সংরক্ষিত হয়েছে!']);
        }

        Toastr::success('এরিয়া ভিত্তিক ডেলিভারি রেট সফলভাবে সংরক্ষিত হয়েছে!', 'Success');
        return redirect()->back();
    }

    public function updateDistrictRate(Request $request)
    {
        $request->validate([
            'district_id' => 'required|integer|exists:districts,id',
            'charge'      => 'nullable|numeric|min:0',
        ]);

        $charge = (float) ($request->charge ?? 0);
        DeliveryDistrict::where('id', $request->district_id)->update(['delivery_charge' => $charge]);

        DeliverySetting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'জেলার চার্জ আপডেট হয়েছে (৳' . $charge . ')',
            'charge'  => $charge,
        ]);
    }

    // ==========================================
    // CUSTOM DELIVERY CHARGES CRUD
    // ==========================================

    public function storeCustomCharge(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:0,1',
        ]);

        CustomDeliveryCharge::create([
            'name'   => trim($request->name),
            'amount' => (float) $request->amount,
            'status' => $request->has('status') ? (int) $request->status : 1,
        ]);

        DeliverySetting::clearCache();

        Toastr::success('কাস্টম ডেলিভারি চার্জ সফলভাবে তৈরি হয়েছে!', 'Success');
        return redirect()->back();
    }

    public function updateCustomCharge(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:0,1',
        ]);

        $custom = CustomDeliveryCharge::findOrFail($id);
        $custom->update([
            'name'   => trim($request->name),
            'amount' => (float) $request->amount,
            'status' => $request->has('status') ? (int) $request->status : 1,
        ]);

        DeliverySetting::clearCache();

        Toastr::success('কাস্টম ডেলিভারি চার্জ সফলভাবে আপডেট হয়েছে!', 'Success');
        return redirect()->back();
    }

    public function destroyCustomCharge($id)
    {
        $custom = CustomDeliveryCharge::findOrFail($id);
        $custom->delete();

        DeliverySetting::clearCache();

        Toastr::success('কাস্টম ডেলিভারি চার্জ সফলভাবে মুছে ফেলা হয়েছে!', 'Success');
        return redirect()->back();
    }
}
