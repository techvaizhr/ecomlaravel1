<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ResellerVerificationController extends Controller
{
    /**
     * Display all reseller verification requests with filters, per-page, and stats
     */
    public function index(Request $request)
    {
        $resellerScope = function($q) {
            $q->where('role', 'reseller')
              ->orWhereHas('roles', function($r) {
                  $r->where('name', 'reseller');
              });
        };

        $query = User::where($resellerScope);

        // Filter by status
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
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('shop_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Default: pending first, then latest
        if (!$request->filled('status')) {
            $query->orderByRaw("CASE WHEN verification_status IS NULL OR verification_status = 'pending' THEN 1 WHEN verification_status = 'rejected' THEN 2 ELSE 3 END");
        }
        $query->latest('id');

        // Per page
        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(User::where($resellerScope)->count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $resellers = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total'    => User::where($resellerScope)->count(),
            'pending'  => User::where($resellerScope)->where(function ($q) {
                $q->whereNull('verification_status')
                    ->orWhere('verification_status', 'pending')
                    ->orWhereNotIn('verification_status', ['approved', 'rejected']);
            })->count(),
            'approved' => User::where($resellerScope)->where('verification_status', 'approved')->count(),
            'rejected' => User::where($resellerScope)->where('verification_status', 'rejected')->count(),
        ];

        return view('backEnd.reseller.verification.index', compact('resellers', 'stats'));
    }

    /**
     * Show verification details for a specific reseller
     */
    public function show($id)
    {
        $reseller = User::findOrFail($id);
        
        // Verify it's a reseller
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->route('admin.reseller.verification.index');
        }
        
        return view('backEnd.reseller.verification.show', compact('reseller'));
    }

    /**
     * Approve reseller verification
     */
    public function approve(Request $request, $id)
    {
        $reseller = User::findOrFail($id);
        
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->back();
        }
        
        if ($reseller->verification_status == 'approved') {
            Toastr::warning('Reseller is already approved.', 'Warning');
            return redirect()->back();
        }

        $reseller->verification_status = 'approved';
        $reseller->verified_at = now();
        $reseller->verification_note = $request->admin_note ?? 'Approved by Administrator';
        $reseller->save();

        Toastr::success('Reseller verification approved successfully.', 'Success');
        return redirect()->back();
    }

    /**
     * Reject reseller verification
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $reseller = User::findOrFail($id);
        
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->back();
        }
        
        if ($reseller->verification_status == 'rejected') {
            Toastr::warning('Reseller is already rejected.', 'Warning');
            return redirect()->back();
        }

        $reseller->verification_status = 'rejected';
        $reseller->verification_note = $request->rejection_reason;
        $reseller->save();

        Toastr::success('Reseller verification rejected.', 'Success');
        return redirect()->back();
    }
}
