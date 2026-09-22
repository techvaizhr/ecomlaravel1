<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResellerDeposit;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ResellerDepositController extends Controller
{
    /**
     * Display all reseller deposits with search, status filters, date range, per-page, and metrics.
     */
    public function index(Request $request)
    {
        $query = ResellerDeposit::with('user:id,name,email,shop_name');

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Keyword Search (User Name, Shop Name, Email, Transaction ID)
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('transaction_id', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function($uq) use ($keyword) {
                      $uq->where('name', 'like', "%{$keyword}%")
                         ->orWhere('shop_name', 'like', "%{$keyword}%")
                         ->orWhere('email', 'like', "%{$keyword}%");
                  });
            });
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Default: Pending first, then latest
        if (!$request->filled('status')) {
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END");
        }
        $query->latest('id');

        // Per page
        $perPage = $request->get('per_page', 20);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(ResellerDeposit::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $deposits = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total_count'      => ResellerDeposit::count(),
            'total_amount'     => ResellerDeposit::sum('amount'),
            'pending_count'    => ResellerDeposit::where('status', 'pending')->count(),
            'pending_amount'   => ResellerDeposit::where('status', 'pending')->sum('amount'),
            'completed_count'  => ResellerDeposit::where('status', 'completed')->count(),
            'completed_amount' => ResellerDeposit::where('status', 'completed')->sum('amount'),
        ];

        return view('backEnd.reseller.deposits', compact('deposits', 'stats'));
    }

    /**
     * Mark a pending deposit as completed and credit reseller wallet
     */
    public function markAsPaid($id)
    {
        $deposit = ResellerDeposit::where('id', $id)->where('status', 'pending')->firstOrFail();

        $deposit->status = 'completed';
        $deposit->transaction_id = $deposit->transaction_id ?? 'admin_' . now()->format('YmdHis');
        $deposit->save();

        $user = $deposit->user;
        if ($user) {
            $user->wallet_balance = ($user->wallet_balance ?? 0) + (float) $deposit->amount;
            $user->save();

            if (class_exists('\App\Models\ResellerWalletTransaction')) {
                \App\Models\ResellerWalletTransaction::log(
                    $user->id, 'deposit', (float) $deposit->amount,
                    'ResellerDeposit', $deposit->id,
                    'এডমিন কর্তৃক পেইড মার্ক - ডিপোজিট #' . $deposit->id
                );
            }
        }

        Toastr::success('Deposit marked as completed and credited to reseller wallet.', 'Success');
        return redirect()->back();
    }
}
