<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CustomDeliveryCharge;
use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use App\Models\DeliverySetting;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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

        $customCharges = Schema::hasTable('custom_delivery_charges')
            ? CustomDeliveryCharge::orderBy('id', 'desc')->get()
            : collect();

        $categories = Category::where('status', 1)->select('id', 'name', 'image')->orderBy('name')->get();
        $brands     = Brand::where('status', 1)->select('id', 'name', 'image')->orderBy('name')->get();
        $products   = Product::with('image')->where('status', 1)->select('id', 'name', 'product_code', 'category_id', 'brand_id')->orderBy('name')->get();

        return view('backEnd.delivery_settings.index', compact('setting', 'divisions', 'customCharges', 'categories', 'brands', 'products'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'active_method'       => 'required|in:free_delivery,flat_rate,weight_based,area_based',
            'flat_rate_amount'    => 'nullable|numeric|min:0',
            'weight_base_cost'    => 'nullable|numeric|min:0',
            'weight_base_kg'      => 'nullable|numeric|min:0.1',
            'weight_extra_per_kg' => 'nullable|numeric|min:0',
        ]);

        $setting = DeliverySetting::first() ?: new DeliverySetting();
        $setting->active_method       = $request->active_method;
        $setting->flat_rate_amount    = (float) ($request->flat_rate_amount ?? 100.00);
        $setting->weight_base_cost    = (float) ($request->weight_base_cost ?? 60.00);
        $setting->weight_base_kg      = (float) ($request->weight_base_kg ?? 1.00);
        $setting->weight_extra_per_kg = (float) ($request->weight_extra_per_kg ?? 20.00);
        $setting->save();

        // If area based was updated with division/district rates
        if ($request->has('divisions') && is_array($request->divisions)) {
            foreach ($request->divisions as $divId => $data) {
                $charge = isset($data['charge']) && $data['charge'] !== '' ? (float) $data['charge'] : 0.00;
                DeliveryDivision::where('id', $divId)->update(['delivery_charge' => $charge]);
            }
        }

        if ($request->has('districts') && is_array($request->districts)) {
            foreach ($request->districts as $distId => $data) {
                $charge = isset($data['charge']) && $data['charge'] !== '' ? (float) $data['charge'] : 0.00;
                DeliveryDistrict::where('id', $distId)->update(['delivery_charge' => $charge]);
            }
        }

        DeliverySetting::clearCache();

        Toastr::success('ডেলিভারি চার্জ সেটিংস সফলভাবে সংরক্ষিত হয়েছে!', 'Success');
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
            'name'         => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'category_ids' => 'nullable|array',
            'brand_ids'    => 'nullable|array',
            'product_ids'  => 'nullable|array',
            'status'       => 'nullable|in:0,1',
        ]);

        CustomDeliveryCharge::create([
            'name'         => trim($request->name),
            'amount'       => (float) $request->amount,
            'category_ids' => $request->category_ids ? array_values(array_filter($request->category_ids)) : null,
            'brand_ids'    => $request->brand_ids ? array_values(array_filter($request->brand_ids)) : null,
            'product_ids'  => $request->product_ids ? array_values(array_filter($request->product_ids)) : null,
            'status'       => $request->has('status') ? (int) $request->status : 1,
        ]);

        DeliverySetting::clearCache();

        Toastr::success('কাস্টম ডেলিভারি চার্জ সফলভাবে তৈরি হয়েছে!', 'Success');
        return redirect()->back();
    }

    public function updateCustomCharge(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'category_ids' => 'nullable|array',
            'brand_ids'    => 'nullable|array',
            'product_ids'  => 'nullable|array',
            'status'       => 'nullable|in:0,1',
        ]);

        $custom = CustomDeliveryCharge::findOrFail($id);
        $custom->update([
            'name'         => trim($request->name),
            'amount'       => (float) $request->amount,
            'category_ids' => $request->category_ids ? array_values(array_filter($request->category_ids)) : null,
            'brand_ids'    => $request->brand_ids ? array_values(array_filter($request->brand_ids)) : null,
            'product_ids'  => $request->product_ids ? array_values(array_filter($request->product_ids)) : null,
            'status'       => $request->has('status') ? (int) $request->status : 1,
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
