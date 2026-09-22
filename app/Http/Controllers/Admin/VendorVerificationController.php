<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class VendorVerificationController extends Controller
{
    /**
     * Display all vendor verification requests with filters, perpage, and stats
     */
    public function index(Request $request)
    {
        $query = Vendor::with('wallet');

        // Status Filter
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('verification_status')
                      ->orWhere('verification_status', 'pending')
                      ->orWhereNotIn('verification_status', ['approved', 'rejected']);
                });
            } else {
                $query->where('verification_status', $request->status);
            }
        }

        // Search Keyword
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('shop_name', 'like', "%{$keyword}%")
                  ->orWhere('owner_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Default Ordering: Pending first, then latest
        if (!$request->filled('status')) {
            $query->orderByRaw("CASE WHEN verification_status IS NULL OR verification_status = 'pending' THEN 1 WHEN verification_status = 'rejected' THEN 2 ELSE 3 END");
        }
        $query->latest('id');

        // Per page
        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(Vendor::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $vendors = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total'    => Vendor::count(),
            'pending'  => Vendor::where(function ($q) {
                $q->whereNull('verification_status')
                    ->orWhere('verification_status', 'pending')
                    ->orWhereNotIn('verification_status', ['approved', 'rejected']);
            })->count(),
            'approved' => Vendor::where('verification_status', 'approved')->count(),
            'rejected' => Vendor::where('verification_status', 'rejected')->count(),
        ];

        return view('backEnd.vendor.verification.index', compact('vendors', 'stats'));
    }

    /**
     * Show verification details for a specific vendor
     */
    public function show($id)
    {
        $vendor = Vendor::with('wallet')->findOrFail($id);
        return view('backEnd.vendor.verification.show', compact('vendor'));
    }

    /**
     * Approve vendor verification
     */
    public function approve(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        
        if ($vendor->verification_status == 'approved') {
            Toastr::warning('Vendor is already approved.', 'Warning');
            return redirect()->back();
        }

        $vendor->verification_status = 'approved';
        $vendor->verified_at = now();
        $vendor->verification_note = $request->admin_note ?? 'Approved by Administrator';
        $vendor->save();

        Toastr::success('Vendor verification approved successfully.', 'Success');
        return redirect()->back();
    }

    /**
     * Reject vendor verification
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($id);
        
        if ($vendor->verification_status == 'rejected') {
            Toastr::warning('Vendor is already rejected.', 'Warning');
            return redirect()->back();
        }

        $vendor->verification_status = 'rejected';
        $vendor->verification_note = $request->rejection_reason;
        $vendor->save();

        Toastr::success('Vendor verification rejected.', 'Success');
        return redirect()->back();
    }
}
