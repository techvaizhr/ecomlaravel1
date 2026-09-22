<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeBonus;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Toastr;

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeBonus::with('employee', 'approvedBy', 'createdBy')->orderBy('created_at', 'DESC');

        // Search
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('bonus_type', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('reason', 'LIKE', '%' . $keyword . '%')
                  ->orWhereHas('employee', function($eq) use ($keyword) {
                      $eq->where('name', 'LIKE', '%' . $keyword . '%')
                         ->orWhere('employee_id', 'LIKE', '%' . $keyword . '%');
                  });
            });
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by bonus type
        if ($request->filled('bonus_type')) {
            $query->where('bonus_type', $request->bonus_type);
        }

        // Per page
        $perPage = $request->get('per_page', 20);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(EmployeeBonus::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $bonuses = $query->paginate($perPage)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $bonusTypes = EmployeeBonus::distinct()->whereNotNull('bonus_type')->where('bonus_type', '!=', '')->pluck('bonus_type');

        $stats = [
            'total_count'    => EmployeeBonus::count(),
            'total_amount'   => EmployeeBonus::sum('amount'),
            'pending_count'  => EmployeeBonus::where('status', 'pending')->count(),
            'approved_count' => EmployeeBonus::where('status', 'approved')->count(),
            'paid_amount'    => EmployeeBonus::where('status', 'paid')->sum('amount'),
        ];

        return view('backEnd.bonuses.index', compact('bonuses', 'employees', 'bonusTypes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('backEnd.bonuses.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'bonus_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'salary_month' => 'nullable|date_format:Y-m',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        EmployeeBonus::create([
            'employee_id' => $request->employee_id,
            'bonus_type' => $request->bonus_type,
            'amount' => $request->amount,
            'salary_month' => $request->salary_month,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        Toastr::success('Bonus created successfully!', 'Success');
        return redirect()->route('admin.bonuses.index');
    }

    /**
     * Approve bonus
     */
    public function approve($id)
    {
        $bonus = EmployeeBonus::with('employee')->findOrFail($id);
        
        $bonus->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Toastr::success('Bonus approved successfully!', 'Success');
        return back();
    }

    /**
     * Pay bonus (deduct from fund)
     */
    public function pay($id)
    {
        $bonus = EmployeeBonus::with('employee')->findOrFail($id);

        if ($bonus->status !== 'approved') {
            Toastr::error('Bonus must be approved before payment!', 'Error');
            return back();
        }

        if ($bonus->status === 'paid') {
            Toastr::error('Bonus already paid!', 'Error');
            return back();
        }

        // Check fund balance
        if (class_exists('\App\Helpers\FundHelper')) {
            $fundBalance = \App\Helpers\FundHelper::balance();
            if ($fundBalance < $bonus->amount) {
                Toastr::error('Insufficient fund balance! Current balance: ৳' . number_format($fundBalance, 2), 'Error');
                return back();
            }
        }

        // Deduct from fund
        if (class_exists('\App\Models\FundTransaction')) {
            FundTransaction::create([
                'direction' => 'out',
                'source' => 'employee_bonus',
                'source_id' => $bonus->id,
                'amount' => $bonus->amount,
                'note' => 'Bonus payment for ' . ($bonus->employee->name ?? 'Employee') . ' - ' . $bonus->bonus_type . ' - Amount: ৳' . number_format($bonus->amount, 2),
                'created_by' => Auth::id(),
            ]);
        }

        $bonus->update([
            'status' => 'paid',
        ]);

        Toastr::success('Bonus paid successfully! Amount deducted from fund.', 'Success');
        return back();
    }

    /**
     * Reject bonus
     */
    public function reject(Request $request, $id)
    {
        $bonus = EmployeeBonus::findOrFail($id);
        
        $bonus->update([
            'status' => 'rejected',
            'notes' => $request->notes ?? $bonus->notes,
        ]);

        Toastr::success('Bonus rejected!');
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bonus = EmployeeBonus::with('employee')->findOrFail($id);
        return view('backEnd.bonuses.edit', compact('bonus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bonus = EmployeeBonus::findOrFail($id);

        if ($bonus->status === 'paid') {
            Toastr::error('Cannot update paid bonus!');
            return back();
        }

        $request->validate([
            'bonus_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'salary_month' => 'nullable|date_format:Y-m',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $bonus->update($request->all());

        Toastr::success('Bonus updated successfully!');
        return redirect()->route('admin.bonuses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bonus = EmployeeBonus::findOrFail($id);

        if ($bonus->status === 'paid') {
            Toastr::error('Cannot delete paid bonus!');
            return back();
        }

        $bonus->delete();

        Toastr::success('Bonus deleted successfully!');
        return redirect()->route('admin.bonuses.index');
    }
}
