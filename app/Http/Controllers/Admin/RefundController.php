<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\FundTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class RefundController extends Controller
{
    /**
     * Display all refunds
     */
    public function index(Request $request)
    {
        $query = Refund::with(['order', 'customer', 'processedBy']);

        // Search keyword (refund_id, transaction_id, refund_account, customer name/phone, order invoice)
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('refund_id', 'like', "%{$keyword}%")
                  ->orWhere('transaction_id', 'like', "%{$keyword}%")
                  ->orWhere('refund_account', 'like', "%{$keyword}%")
                  ->orWhere('refund_account_name', 'like', "%{$keyword}%")
                  ->orWhereHas('order', function ($oq) use ($keyword) {
                      $oq->where('invoice_id', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($keyword) {
                      $cq->where('name', 'like', "%{$keyword}%")
                         ->orWhere('phone', 'like', "%{$keyword}%")
                         ->orWhere('email', 'like', "%{$keyword}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by refund method
        if ($request->filled('refund_method')) {
            $query->where('refund_method', $request->refund_method);
        }

        // Filter by order invoice
        if ($request->filled('order_invoice')) {
            $invoice = trim($request->order_invoice);
            $query->whereHas('order', function ($q) use ($invoice) {
                $q->where('invoice_id', 'like', "%{$invoice}%");
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Dynamic per page
        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100, 200])) {
            $perPage = 15;
        }

        $data = $query->latest()->paginate($perPage)->withQueryString();

        $statuses = ['pending', 'approved', 'rejected', 'processed'];
        
        // Comprehensive counts and amounts
        $statusCounts = Refund::selectRaw('status, COUNT(*) as total, SUM(amount + shipping_charge) as total_amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $totalCount = Refund::count();
        $totalAmount = Refund::sum(DB::raw('amount + shipping_charge'));

        return view('backEnd.refunds.index', compact('data', 'statuses', 'statusCounts', 'totalCount', 'totalAmount', 'perPage'));
    }

    /**
     * Show refund details
     */
    public function show($id)
    {
        $refund = Refund::with([
            'order.orderdetails.product.image',
            'order.orderdetails.image',
            'customer',
            'processedBy',
        ])->findOrFail($id);
        
        return view('backEnd.refunds.show', compact('refund'));
    }

    /**
     * Approve refund request
     */
    public function approve(Request $request, $id)
    {
        $refund = Refund::with(['order', 'customer'])->findOrFail($id);

        if ($refund->status !== 'pending') {
            Toastr::error('This refund has already been processed.', 'Error');
            return back();
        }

        // Check admin fund balance
        $adminFundBalance = \App\Helpers\FundHelper::balance();
        $totalRefundAmount = $refund->amount + $refund->shipping_charge;
        
        if ($adminFundBalance < $totalRefundAmount) {
            Toastr::error('Insufficient fund balance. Current balance: ৳' . number_format($adminFundBalance, 2), 'Error');
            return back();
        }

        DB::transaction(function () use ($refund, $request) {
            $refund->status = 'approved';
            $refund->admin_note = $request->admin_note;
            $refund->processed_by = Auth::id();
            $refund->save();

            // Deduct from admin fund
            FundTransaction::create([
                'direction'  => 'out',
                'source'     => 'refund',
                'source_id'  => $refund->id,
                'amount'     => $refund->amount + $refund->shipping_charge,
                'note'       => 'Refund approved for Order #' . $refund->order->invoice_id . ' - Refund ID: ' . $refund->refund_id,
                'created_by' => Auth::id(),
            ]);
        });

        Toastr::success('Refund approved successfully.', 'Success');
        return back();
    }

    /**
     * Reject refund request
     */
    public function reject(Request $request, $id)
    {
        $refund = Refund::findOrFail($id);

        if ($refund->status !== 'pending' && $refund->status !== 'approved') {
            Toastr::error('This refund has already been processed.', 'Error');
            return back();
        }

        DB::transaction(function () use ($refund, $request) {
            // If refund was already approved, reverse the fund transaction
            if ($refund->status === 'approved') {
                // Find and delete the fund transaction
                $fundTransaction = FundTransaction::where('source', 'refund')
                    ->where('source_id', $refund->id)
                    ->where('direction', 'out')
                    ->first();

                if ($fundTransaction) {
                    // Reverse the transaction by creating an 'in' transaction
                    FundTransaction::create([
                        'direction'  => 'in',
                        'source'     => 'refund_reversal',
                        'source_id'  => $refund->id,
                        'amount'     => $refund->amount + $refund->shipping_charge,
                        'note'       => 'Refund rejected - Reversal for Order #' . $refund->order->invoice_id . ' - Refund ID: ' . $refund->refund_id,
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            $refund->status = 'rejected';
            $refund->admin_note = $request->admin_note;
            $refund->processed_by = Auth::id();
            $refund->processed_at = now();
            $refund->save();
        });

        Toastr::success('Refund request rejected.', 'Success');
        return back();
    }

    /**
     * Process refund (mark as processed after payment)
     */
    public function process(Request $request, $id)
    {
        $refund = Refund::with(['order'])->findOrFail($id);

        if ($refund->status !== 'approved') {
            Toastr::error('Only approved refunds can be processed.', 'Error');
            return back();
        }

        $request->validate([
            'transaction_id' => 'required|string|max:255',
            'refund_method' => 'required|in:original_payment,bkash,nagad,bank,manual',
            'refund_account' => 'required|string|max:255',
            'refund_account_name' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($refund, $request) {
            $refund->status = 'processed';
            $refund->transaction_id = $request->transaction_id;
            $refund->refund_method = $request->refund_method;
            $refund->refund_account = $request->refund_account;
            $refund->refund_account_name = $request->refund_account_name;
            $refund->processed_at = now();
            $refund->save();

            // Restore product stock if order was cancelled
            if ($refund->order->order_status == 11) { // 11 = cancelled
                $orderDetails = OrderDetails::where('order_id', $refund->order_id)
                    ->with('product')
                    ->get();

                foreach ($orderDetails as $detail) {
                    if ($detail->product) {
                        $detail->product->stock += $detail->qty;
                        $detail->product->save();
                    }
                }
            }
        });

        Toastr::success('Refund processed successfully.', 'Success');
        return back();
    }

    /**
     * Delete refund (only if pending)
     */
    public function destroy($id)
    {
        $refund = Refund::findOrFail($id);

        if ($refund->status !== 'pending') {
            Toastr::error('Only pending refunds can be deleted.', 'Error');
            return back();
        }

        $refund->delete();
        Toastr::success('Refund request deleted successfully.', 'Success');
        return back();
    }
}
