<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class VendorWithdrawalController extends Controller
{
    /**
     * Display all vendor withdrawal requests with filtering, per-page, and metrics
     */
    public function index(Request $request)
    {
        $query = VendorWithdrawal::with('vendor');

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payout Method Filter
        if ($request->filled('payout_method')) {
            $query->where('payout_method', $request->payout_method);
        }

        // Keyword Search (Vendor Shop Name, Owner, Account Name, Account Number)
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('account_name', 'like', "%{$keyword}%")
                  ->orWhere('account_number', 'like', "%{$keyword}%")
                  ->orWhere('note', 'like', "%{$keyword}%")
                  ->orWhereHas('vendor', function($vq) use ($keyword) {
                      $vq->where('shop_name', 'like', "%{$keyword}%")
                         ->orWhere('owner_name', 'like', "%{$keyword}%")
                         ->orWhere('phone', 'like', "%{$keyword}%");
                  });
            });
        }

        // Date Filter (Presets + Custom Range)
        apply_date_filter($query, $request);

        // Default: Pending on top, then latest
        if (!$request->filled('status')) {
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'approved' THEN 2 ELSE 3 END");
        }
        $query->latest('id');

        // Per page
        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(VendorWithdrawal::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $data = $query->paginate($perPage)->withQueryString();

        // Calculate metrics
        $adminFundBalance = 0;
        if (class_exists('\App\Helpers\FundHelper')) {
            $adminFundBalance = \App\Helpers\FundHelper::balance();
        }

        $stats = [
            'total_count'     => VendorWithdrawal::count(),
            'total_amount'    => VendorWithdrawal::sum('amount'),
            'pending_count'   => VendorWithdrawal::where('status', 'pending')->count(),
            'pending_amount'  => VendorWithdrawal::where('status', 'pending')->sum('amount'),
            'approved_count'  => VendorWithdrawal::where('status', 'approved')->count(),
            'approved_amount' => VendorWithdrawal::where('status', 'approved')->sum('amount'),
            'rejected_count'  => VendorWithdrawal::where('status', 'rejected')->count(),
            'rejected_amount' => VendorWithdrawal::where('status', 'rejected')->sum('amount'),
            'fund_balance'    => $adminFundBalance,
        ];

        return view('backEnd.vendor.withdrawals.index', compact('data', 'stats'));
    }

    /**
     * Approve vendor withdrawal request safely
     */
    public function approve(Request $request, $id)
    {
        $withdrawal = VendorWithdrawal::with('vendor')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            Toastr::error('Withdrawal has already been processed.', 'Error');
            return back();
        }

        // Check admin fund balance if available
        if (class_exists('\App\Helpers\FundHelper')) {
            $adminFundBalance = \App\Helpers\FundHelper::balance();
            if ($adminFundBalance < $withdrawal->amount) {
                Toastr::error('Insufficient admin fund balance. Current balance: ৳' . number_format($adminFundBalance, 2), 'Error');
                return back();
            }
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->status = 'approved';
            $withdrawal->processed_at = now();
            $withdrawal->admin_note = $request->admin_note ?? 'Approved by Administrator';
            $withdrawal->save();

            $wallet = VendorWallet::firstOrCreate(['vendor_id' => $withdrawal->vendor_id]);
            $wallet->total_withdrawn += $withdrawal->amount;
            $wallet->save();

            VendorWalletTransaction::where('source_type', 'withdraw')
                ->where('source_id', $withdrawal->id)
                ->update(['status' => 'completed']);

            // Deduct from admin fund and create fund transaction
            if (class_exists('\App\Models\FundTransaction')) {
                FundTransaction::create([
                    'direction'  => 'out',
                    'source'     => 'vendor_withdrawal',
                    'source_id'  => $withdrawal->id,
                    'amount'     => $withdrawal->amount,
                    'note'       => 'Vendor withdrawal approved - ' . ($withdrawal->vendor->shop_name ?? 'Vendor #' . $withdrawal->vendor_id) . ' - Amount: ৳' . number_format($withdrawal->amount, 2),
                    'created_by' => Auth::id(),
                ]);
            }
        });

        Toastr::success('Withdrawal approved successfully.', 'Success');
        return back();
    }

    /**
     * Reject vendor withdrawal request and refund wallet balance
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        $withdrawal = VendorWithdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            Toastr::error('Withdrawal has already been processed.', 'Error');
            return back();
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->status = 'rejected';
            $withdrawal->processed_at = now();
            $withdrawal->admin_note = $request->admin_note;
            $withdrawal->save();

            $wallet = VendorWallet::firstOrCreate(['vendor_id' => $withdrawal->vendor_id]);
            // Refund the held balance
            $wallet->balance += $withdrawal->amount;
            $wallet->save();

            VendorWalletTransaction::where('source_type', 'withdraw')
                ->where('source_id', $withdrawal->id)
                ->update(['status' => 'rejected']);
        });

        Toastr::success('Withdrawal rejected and amount returned to vendor balance.', 'Success');
        return back();
    }
}
