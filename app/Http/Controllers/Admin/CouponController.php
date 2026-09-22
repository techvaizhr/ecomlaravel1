<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Brian2694\Toastr\Facades\Toastr;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        // Search by coupon code
        if ($request->filled('keyword')) {
            $query->where('code', 'like', '%' . trim($request->keyword) . '%');
        }

        // Filter by discount type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status / validity
        if ($request->filled('status')) {
            $today = date('Y-m-d');
            if ($request->status === 'active') {
                $query->where('status', 1)
                      ->where(function($q) use ($today) {
                          $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
                      });
            } elseif ($request->status === 'inactive') {
                $query->where('status', 0);
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('valid_to')->where('valid_to', '<', $today);
            }
        }

        // Apply global date filter
        if (function_exists('apply_date_filter')) {
            apply_date_filter($query, $request, 'created_at');
        }

        // Summary KPIs
        $today = date('Y-m-d');
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('status', 1)->where(function($q) use ($today) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
            })->count(),
            'expired' => Coupon::whereNotNull('valid_to')->where('valid_to', '<', $today)->count(),
            'inactive' => Coupon::where('status', 0)->count(),
        ];

        $per_page = $request->get('per_page', 15);
        $coupons = $query->latest()->paginate($per_page)->withQueryString();

        return view('backEnd.coupon.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        return view('backEnd.coupon.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons',
            'type' => 'required',
            'value' => 'required|numeric|min:1',
        ]);

        Coupon::create($request->all());
        Toastr::success('Coupon created successfully', 'Success');
        return redirect()->route('admin.coupons.index');
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('backEnd.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update($request->all());
        Toastr::success('Coupon updated successfully', 'Success');
        return redirect()->route('admin.coupons.index');
    }

    public function destroy($id)
    {
        Coupon::destroy($id);
        Toastr::success('Coupon deleted successfully', 'Success');
        return redirect()->back();
    }
}
