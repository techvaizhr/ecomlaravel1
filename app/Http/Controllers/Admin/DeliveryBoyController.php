<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoySalaryPayment;
use App\Models\DeliveryBoyWithdrawal;
use App\Services\DeliveryBoyWalletService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeliveryBoyController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryBoy::query();

        // Search
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('phone', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', (int)$request->status);
        }

        $query->latest('id');

        // Per page
        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(DeliveryBoy::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $rows = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total_count'    => DeliveryBoy::count(),
            'active_count'   => DeliveryBoy::where('status', 1)->count(),
            'inactive_count' => DeliveryBoy::where('status', 0)->count(),
            'total_balance'  => DeliveryBoy::sum('wallet_balance'),
        ];

        return view('backEnd.delivery_boys.index', compact('rows', 'stats'));
    }

    public function create()
    {
        return view('backEnd.delivery_boys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:191',
            'phone'                   => 'required|string|max:20|unique:delivery_boys,phone',
            'email'                   => 'nullable|email|unique:delivery_boys,email',
            'password'                => 'required|string|min:6',
            'commission_per_delivery' => 'required|numeric|min:0',
            'monthly_salary_amount'   => 'nullable|numeric|min:0',
            'status'                  => 'required|in:0,1',
            'image'                   => 'nullable|image|max:2048',
        ]);

        $payload = [
            'name'                    => $request->name,
            'phone'                   => $request->phone,
            'email'                   => $request->email,
            'password'                => Hash::make($request->password),
            'commission_per_delivery' => $request->commission_per_delivery,
            'monthly_salary_amount'   => $request->monthly_salary_amount ?? 0,
            'status'                  => (int) $request->status,
        ];
        if ($request->hasFile('image')) {
            $payload['image'] = $this->uploadImage($request->file('image'));
        }
        DeliveryBoy::create($payload);

        Toastr::success('Delivery person created successfully', 'Success');
        return redirect()->route('admin.delivery-boys.index');
    }

    private function uploadImage($file): string
    {
        $name = 'delivery_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $path = 'public/uploads/delivery_boys';
        if (! is_dir(base_path($path))) {
            mkdir(base_path($path), 0755, true);
        }
        $file->move(base_path($path), $name);

        return 'public/uploads/delivery_boys/'.$name;
    }

    public function edit(int $id)
    {
        $edit_data = DeliveryBoy::findOrFail($id);

        return view('backEnd.delivery_boys.edit', compact('edit_data'));
    }

    public function update(Request $request, int $id)
    {
        $row = DeliveryBoy::findOrFail($id);
        $request->validate([
            'name'                    => 'required|string|max:191',
            'phone'                   => 'required|string|max:20|unique:delivery_boys,phone,'.$id,
            'email'                   => 'nullable|email|unique:delivery_boys,email,'.$id,
            'password'                => 'nullable|string|min:6',
            'commission_per_delivery' => 'required|numeric|min:0',
            'monthly_salary_amount'   => 'nullable|numeric|min:0',
            'status'                  => 'required|in:0,1',
            'image'                   => 'nullable|image|max:2048',
        ]);

        $row->name = $request->name;
        $row->phone = $request->phone;
        $row->email = $request->email;
        $row->commission_per_delivery = $request->commission_per_delivery;
        $row->monthly_salary_amount = $request->monthly_salary_amount ?? 0;
        $row->status = (int) $request->status;
        if ($request->filled('password')) {
            $row->password = Hash::make($request->password);
        }
        if ($request->hasFile('image')) {
            $row->image = $this->uploadImage($request->file('image'));
        }
        $row->save();

        Toastr::success('Delivery person updated successfully', 'Success');
        return redirect()->route('admin.delivery-boys.index');
    }

    public function wallet(int $id)
    {
        $boy = DeliveryBoy::findOrFail($id);
        $tx = $boy->walletTransactions()->orderByDesc('id')->paginate(30);

        return view('backEnd.delivery_boys.wallet', compact('boy', 'tx'));
    }

    public function paySalary(Request $request, DeliveryBoyWalletService $wallet)
    {
        $request->validate([
            'delivery_boy_id' => 'required|exists:delivery_boys,id',
            'amount'          => 'required|numeric|min:1',
            'salary_month'    => 'required|regex:/^\d{4}-\d{2}$/',
            'note'            => 'nullable|string|max:500',
        ]);

        $boy = DeliveryBoy::findOrFail($request->delivery_boy_id);

        DeliveryBoySalaryPayment::create([
            'delivery_boy_id' => $boy->id,
            'amount'          => $request->amount,
            'salary_month'    => $request->salary_month,
            'note'            => $request->note,
            'created_by'      => auth()->id(),
        ]);

        $wallet->credit(
            $boy,
            'salary',
            (float) $request->amount,
            null,
            'Salary '.$request->salary_month.($request->note ? ' — '.$request->note : '')
        );

        Toastr::success('Salary credited to wallet', 'Success');
        return redirect()->back();
    }

    public function withdrawals(Request $request)
    {
        $query = DeliveryBoyWithdrawal::with('deliveryBoy');

        // Search
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('payout_number', 'LIKE', "%{$keyword}%")
                    ->orWhere('payout_method', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('deliveryBoy', function ($bq) use ($keyword) {
                        $bq->where('name', 'LIKE', "%{$keyword}%")
                            ->orWhere('phone', 'LIKE', "%{$keyword}%");
                    });
            });
        }

        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Method
        if ($request->filled('payout_method')) {
            $query->where('payout_method', $request->payout_method);
        }

        // Date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $query->latest('id');

        // Per page
        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(DeliveryBoyWithdrawal::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $rows = $query->paginate($perPage)->withQueryString();

        $adminFundBalance = 0;
        if (class_exists('\App\Helpers\FundHelper')) {
            $adminFundBalance = \App\Helpers\FundHelper::balance();
        }

        $stats = [
            'total_count'     => DeliveryBoyWithdrawal::count(),
            'total_amount'    => DeliveryBoyWithdrawal::sum('amount'),
            'pending_count'   => DeliveryBoyWithdrawal::where('status', 'pending')->count(),
            'pending_amount'  => DeliveryBoyWithdrawal::where('status', 'pending')->sum('amount'),
            'approved_count'  => DeliveryBoyWithdrawal::where('status', 'approved')->count(),
            'approved_amount' => DeliveryBoyWithdrawal::where('status', 'approved')->sum('amount'),
            'rejected_count'  => DeliveryBoyWithdrawal::where('status', 'rejected')->count(),
            'fund_balance'    => $adminFundBalance,
        ];

        return view('backEnd.delivery_boys.withdrawals', compact('rows', 'stats'));
    }

    public function approveWithdrawal(Request $request, DeliveryBoyWalletService $wallet)
    {
        $request->validate([
            'id'         => 'required|exists:delivery_boy_withdrawals,id',
            'admin_note' => 'nullable|string|max:500',
        ]);
        $w = DeliveryBoyWithdrawal::with('deliveryBoy')->findOrFail($request->id);
        if ($w->status !== 'pending') {
            Toastr::error('Withdrawal has already been processed.', 'Error');
            return redirect()->back();
        }

        // Check admin fund balance
        if (class_exists('\App\Helpers\FundHelper')) {
            $adminFundBalance = \App\Helpers\FundHelper::balance();
            if ($adminFundBalance < $w->amount) {
                Toastr::error('Insufficient admin fund balance. Current balance: ৳' . number_format($adminFundBalance, 2), 'Error');
                return redirect()->back();
            }
        }

        // Check rider wallet balance
        if ($w->deliveryBoy && $w->deliveryBoy->wallet_balance < $w->amount) {
            Toastr::error('Rider has insufficient wallet balance for this withdrawal.', 'Error');
            return redirect()->back();
        }

        if ($request->has('admin_note')) {
            $w->admin_note = $request->admin_note;
            $w->save();
        }

        try {
            $wallet->approveWithdrawal($w);

            // Record fund transaction
            if (class_exists('\App\Models\FundTransaction')) {
                \App\Models\FundTransaction::create([
                    'direction'  => 'out',
                    'source'     => 'rider_withdrawal',
                    'source_id'  => $w->id,
                    'amount'     => $w->amount,
                    'note'       => 'Payout for rider ' . ($w->deliveryBoy->name ?? 'ID: ' . $w->delivery_boy_id) . ' (' . ucfirst($w->payout_method) . ': ' . $w->payout_number . ')',
                    'created_by' => auth()->id(),
                ]);
            }

            Toastr::success('Withdrawal approved and deducted from rider wallet & admin fund.', 'Success');
        } catch (\Throwable $e) {
            Toastr::error($e->getMessage(), 'Error');
        }

        return redirect()->back();
    }

    public function rejectWithdrawal(Request $request)
    {
        $request->validate([
            'id'         => 'required|exists:delivery_boy_withdrawals,id',
            'admin_note' => 'nullable|string|max:500',
        ]);
        $w = DeliveryBoyWithdrawal::findOrFail($request->id);
        if ($w->status !== 'pending') {
            Toastr::error('Withdrawal has already been processed.', 'Error');
            return redirect()->back();
        }
        $w->status = 'rejected';
        $w->processed_at = now();
        $w->processed_by = auth()->id();
        $w->admin_note = $request->input('admin_note');
        $w->save();

        Toastr::info('Withdrawal request rejected.', 'Info');
        return redirect()->back();
    }
}
