<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use App\Models\ResellerWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class ResellerWithdrawalController extends Controller
{
    /**
     * Display all reseller withdrawal requests with filtering, per-page, and metrics
     */
    public function index(Request $request)
    {
        $query = ResellerWithdrawal::with('user');

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payout Method Filter
        if ($request->filled('payout_method')) {
            $query->where('payout_method', $request->payout_method);
        }

        // Keyword Search (Reseller Name, Shop, Account Name, Account Number, Phone)
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('account_name', 'like', "%{$keyword}%")
                  ->orWhere('account_number', 'like', "%{$keyword}%")
                  ->orWhere('note', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function($uq) use ($keyword) {
                      $uq->where('name', 'like', "%{$keyword}%")
                         ->orWhere('shop_name', 'like', "%{$keyword}%")
                         ->orWhere('email', 'like', "%{$keyword}%")
                         ->orWhere('phone', 'like', "%{$keyword}%");
                  });
            });
        }

        // Date Filter (Presets + Custom Range)
        apply_date_filter($query, $request);

        // Default: Pending first, then latest
        if (!$request->filled('status')) {
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'approved' THEN 2 ELSE 3 END");
        }
        $query->latest('id');

        // Per page
        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(ResellerWithdrawal::count(), 1);
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
            'total_count'     => ResellerWithdrawal::count(),
            'total_amount'    => ResellerWithdrawal::sum('amount'),
            'pending_count'   => ResellerWithdrawal::where('status', 'pending')->count(),
            'pending_amount'  => ResellerWithdrawal::where('status', 'pending')->sum('amount'),
            'approved_count'  => ResellerWithdrawal::where('status', 'approved')->count(),
            'approved_amount' => ResellerWithdrawal::where('status', 'approved')->sum('amount'),
            'rejected_count'  => ResellerWithdrawal::where('status', 'rejected')->count(),
            'rejected_amount' => ResellerWithdrawal::where('status', 'rejected')->sum('amount'),
            'fund_balance'    => $adminFundBalance,
        ];

        return view('backEnd.reseller.withdrawals.index', compact('data', 'stats'));
    }

    /**
     * Approve reseller withdrawal request safely
     */
    public function approve(Request $request, $id)
    {
        $withdrawal = ResellerWithdrawal::with('user')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            Toastr::error('Withdrawal has already been processed.', 'Error');
            return back();
        }

        // Check admin fund balance
        if (class_exists('\App\Helpers\FundHelper')) {
            $adminFundBalance = \App\Helpers\FundHelper::balance();
            if ($adminFundBalance < $withdrawal->amount) {
                Toastr::error('Insufficient fund balance. Current balance: ৳' . number_format($adminFundBalance, 2), 'Error');
                return back();
            }
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->status = 'approved';
            $withdrawal->processed_at = now();
            $withdrawal->admin_note = $request->admin_note ?? 'Approved by Administrator';
            $withdrawal->save();

            // Deduct from admin fund and create fund transaction
            if (class_exists('\App\Models\FundTransaction')) {
                FundTransaction::create([
                    'direction'  => 'out',
                    'source'     => 'reseller_withdrawal',
                    'source_id'  => $withdrawal->id,
                    'amount'     => $withdrawal->amount,
                    'note'       => 'Reseller withdrawal approved - ' . ($withdrawal->user->shop_name ?? $withdrawal->user->name) . ' - Amount: ৳' . number_format($withdrawal->amount, 2),
                    'created_by' => Auth::id(),
                ]);
            }
        });

        Toastr::success('Withdrawal approved successfully.', 'Success');
        return back();
    }

    /**
     * Reject reseller withdrawal request and refund wallet balance
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        $withdrawal = ResellerWithdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            Toastr::error('Withdrawal has already been processed.', 'Error');
            return back();
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->status = 'rejected';
            $withdrawal->processed_at = now();
            $withdrawal->admin_note = $request->admin_note;
            $withdrawal->save();

            // Refund the held balance back to reseller wallet
            $reseller = $withdrawal->user;
            if ($reseller) {
                $reseller->wallet_balance = ($reseller->wallet_balance ?? 0) + $withdrawal->amount;
                $reseller->save();

                if (class_exists('\App\Models\ResellerWalletTransaction')) {
                    \App\Models\ResellerWalletTransaction::log(
                        $reseller->id, 'withdrawal_reversed', (float) $withdrawal->amount,
                        'ResellerWithdrawal', $withdrawal->id,
                        'উইথড্র রিজেক্ট - ফেরত #' . $withdrawal->id
                    );
                }
            }
        });

        Toastr::success('Withdrawal rejected and amount returned to reseller balance.', 'Success');
        return back();
    }
}
