@extends('backEnd.layouts.master')
@section('title', 'Salary Payments')

@section('css')
<style>
    .metric-card {
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        background: #fff;
        border: 1px solid #edf2f7;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .metric-icon.blue { background: #e0f2fe; color: #0284c7; }
    .metric-icon.green { background: #dcfce7; color: #16a34a; }
    .metric-icon.purple { background: #f3e8ff; color: #9333ea; }
    .metric-icon.amber { background: #fef3c7; color: #d97706; }

    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
        overflow: hidden;
    }
    .table-modern th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid #edf2f7;
        white-space: nowrap;
    }
    .table-modern td {
        vertical-align: middle;
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #fbfcfe; }
    .user-avatar {
        width: 38px; height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 13px;
        flex-shrink: 0;
    }
    .emp-id-badge {
        font-size: 0.7rem; background: #f1f5f9; color: #475569;
        padding: 2px 6px; border-radius: 4px; font-weight: 600; margin-left: 6px;
    }
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-failed { background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .copy-btn {
        background: none;
        border: none;
        padding: 2px 6px;
        color: #64748b;
        cursor: pointer;
        transition: color 0.15s;
    }
    .copy-btn:hover { color: #0284c7; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="credit-card" class="text-primary me-2" style="width:26px;height:26px;"></i> Salary Disbursal Payments
            </h4>
            <p class="text-muted small mb-0">Transaction records of all paid employee salaries and disbursements.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i data-feather="dollar-sign" class="me-1" style="width:16px;height:16px;"></i> Salaries
            </a>
            <a href="{{ route('admin.salary_payments.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i data-feather="plus" class="me-1" style="width:16px;height:16px;"></i> Make Salary Payment
            </a>
        </div>
    </div>

    <!-- Live Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon blue">
                    <i data-feather="file-text" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Payments</div>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($stats['total_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i data-feather="check-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Disbursed</div>
                    <h3 class="mb-0 fw-bold text-success">৳{{ number_format($stats['total_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon purple">
                    <i data-feather="briefcase" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Bank Transfers</div>
                    <h3 class="mb-0 fw-bold text-primary">৳{{ number_format($stats['bank_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="smartphone" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Mobile Banking (MFS)</div>
                    <h3 class="mb-0 fw-bold text-warning">৳{{ number_format($stats['mfs_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.salary_payments.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" style="width:16px;"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Employee, Trx ID, Account..." value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="employee_id" class="form-select">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="bkash" {{ request('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                            <option value="nagad" {{ request('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                            <option value="rocket" {{ request('payment_method') == 'rocket' ? 'selected' : '' }}>Rocket</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="check" {{ request('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="month" name="month" class="form-control" value="{{ request('month') }}" placeholder="Month">
                    </div>
                    <div class="col-md-1">
                        <select name="per_page" class="form-select">
                            <option value="15" {{ request('per_page', 20) == 15 ? 'selected' : '' }}>15 / p</option>
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 / p</option>
                            <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50 / p</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i data-feather="filter" class="me-1" style="width:16px;"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'employee_id', 'payment_method', 'month', 'status', 'per_page']))
                            <a href="{{ route('admin.salary_payments.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                                <i data-feather="refresh-cw" style="width:16px;"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card card-modern">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Employee Details</th>
                            <th>Salary Month</th>
                            <th>Amount Paid</th>
                            <th>Payment Method & Account</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                            <th class="text-end" width="8%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $key => $payment)
                            <tr>
                                <td class="text-muted small">{{ $payments->firstItem() ? $payments->firstItem() + $key : $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($payment->employee->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $payment->employee->name ?? 'N/A' }}
                                                @if(isset($payment->employee->employee_id))
                                                    <span class="emp-id-badge">#{{ $payment->employee->employee_id }}</span>
                                                @endif
                                            </div>
                                            <span class="text-muted small">{{ $payment->employee->department ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $payment->payment_month }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success fs-6">৳{{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-semibold text-dark text-capitalize">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                        @if($payment->account_number || $payment->transaction_id)
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <span class="font-monospace text-muted small">{{ $payment->account_number ?: $payment->transaction_id }}</span>
                                                <button type="button" class="copy-btn" onclick="copyNumber('{{ $payment->account_number ?: $payment->transaction_id }}', this)" title="Copy Account / TrxID">
                                                    <i data-feather="copy" style="width:12px;height:12px;"></i>
                                                </button>
                                            </div>
                                        @endif
                                        @if($payment->bank_name)
                                            <small class="text-muted d-block">{{ $payment->bank_name }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark small d-block">{{ date('d M, Y', strtotime($payment->payment_date)) }}</span>
                                </td>
                                <td>
                                    @if($payment->status == 'paid')
                                        <span class="badge-soft badge-paid"><span class="status-dot"></span> Paid</span>
                                    @elseif($payment->status == 'failed')
                                        <span class="badge-soft badge-failed"><span class="status-dot"></span> Failed</span>
                                    @else
                                        <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.salary_payments.show', $payment->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="View Receipt">
                                        <i data-feather="eye" class="me-1" style="width:14px;height:14px;"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No salary payment records found matching your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} payment records
                    </div>
                    <div>
                        {{ $payments->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyNumber(text, btn) {
    if (!navigator.clipboard) {
        var dummy = document.createElement("input");
        document.body.appendChild(dummy);
        dummy.setAttribute("value", text);
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
    } else {
        navigator.clipboard.writeText(text);
    }
    
    var originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="text-success" style="font-size:11px;">Copied!</span>';
    setTimeout(function() {
        btn.innerHTML = originalHTML;
        feather.replace();
    }, 1500);
}
</script>
@endsection