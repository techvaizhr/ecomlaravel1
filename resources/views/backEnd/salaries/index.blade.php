@extends('backEnd.layouts.master')
@section('title', 'Salary Management')

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
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
    .badge-calculated { background: #e0f2fe; color: #075985; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-days { background: #f1f5f9; color: #475569; padding: 3px 6px; border-radius: 4px; font-size: 0.75rem; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="dollar-sign" class="text-primary me-2" style="width:26px;height:26px;"></i> Salary & Payroll
            </h4>
            <p class="text-muted small mb-0">Calculate, review attendance-based deductions, and approve monthly payroll.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.salary_payments.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i data-feather="credit-card" class="me-1" style="width:16px;height:16px;"></i> Salary Payments
            </a>
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#calculateModal">
                <i data-feather="plus" class="me-1" style="width:16px;height:16px;"></i> Single Calculate
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkCalculateModal">
                <i data-feather="zap" class="me-1" style="width:16px;height:16px;"></i> Bulk Calculate
            </button>
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
                    <div class="text-muted small fw-semibold text-uppercase">Total Processed</div>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($stats['total_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon purple">
                    <i data-feather="dollar-sign" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Net Payable</div>
                    <h3 class="mb-0 fw-bold text-primary">৳{{ number_format($stats['total_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i data-feather="check-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Paid Salaries</div>
                    <h3 class="mb-0 fw-bold text-success">৳{{ number_format($stats['paid_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="clock" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Pending Payment</div>
                    <h3 class="mb-0 fw-bold text-warning">৳{{ number_format($stats['pending_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.salaries.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" style="width:16px;"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Employee name or ID..." value="{{ request('keyword') }}">
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
                        <input type="month" name="month" class="form-control" value="{{ request('month') }}" placeholder="Month">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="calculated" {{ request('status') == 'calculated' ? 'selected' : '' }}>Calculated</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="per_page" class="form-select">
                            <option value="15" {{ request('per_page', 20) == 15 ? 'selected' : '' }}>15 / p</option>
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 / p</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / p</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i data-feather="filter" class="me-1" style="width:16px;"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'employee_id', 'month', 'status', 'per_page']))
                            <a href="{{ route('admin.salaries.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
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
                            <th>Attendance Summary</th>
                            <th>Basic Salary</th>
                            <th>Deduction / Bonus</th>
                            <th>Net Payable</th>
                            <th>Status</th>
                            <th class="text-end" width="12%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $key => $salary)
                            <tr>
                                <td class="text-muted small">{{ $salaries->firstItem() ? $salaries->firstItem() + $key : $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($salary->employee->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $salary->employee->name ?? 'N/A' }}
                                                @if(isset($salary->employee->employee_id))
                                                    <span class="emp-id-badge">#{{ $salary->employee->employee_id }}</span>
                                                @endif
                                            </div>
                                            <span class="text-muted small">{{ $salary->employee->department ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($salary->salary_month)->format('M Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge-days" title="Working Days">W: {{ $salary->working_days }}</span>
                                        <span class="badge-days text-success" title="Present Days">P: {{ $salary->present_days }}</span>
                                        <span class="badge-days text-danger" title="Absent Days">A: {{ $salary->absent_days }}</span>
                                        <span class="badge-days text-info" title="Leave Days">L: {{ $salary->leave_days ?? 0 }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark">৳{{ number_format($salary->basic_salary, 2) }}</span>
                                </td>
                                <td>
                                    <small class="text-danger d-block">-৳{{ number_format($salary->deduction, 2) }}</small>
                                    @if($salary->bonus > 0)
                                        <small class="text-success d-block">+৳{{ number_format($salary->bonus, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-primary fs-6">৳{{ number_format($salary->net_salary, 2) }}</span>
                                </td>
                                <td>
                                    @if($salary->status == 'paid')
                                        <span class="badge-soft badge-paid"><span class="status-dot"></span> Paid</span>
                                    @elseif($salary->status == 'calculated')
                                        <span class="badge-soft badge-calculated"><span class="status-dot"></span> Calculated</span>
                                    @else
                                        <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.salaries.show', $salary->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2" title="View Breakdown">
                                            <i data-feather="eye" style="width:14px;height:14px;"></i>
                                        </a>
                                        @if($salary->status == 'calculated')
                                            <a href="{{ route('admin.salary_payments.create', ['employee_id' => $salary->employee_id, 'salary_id' => $salary->id]) }}" class="btn btn-sm btn-success rounded-pill px-2" title="Pay Salary">
                                                <i data-feather="credit-card" style="width:14px;height:14px;"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No salary records found matching your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($salaries->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $salaries->firstItem() }} to {{ $salaries->lastItem() }} of {{ $salaries->total() }} salary records
                    </div>
                    <div>
                        {{ $salaries->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Calculate Modal -->
<div class="modal fade" id="calculateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Calculate Employee Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.salaries.calculate') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Choose Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Salary Month <span class="text-danger">*</span></label>
                        <input type="month" name="salary_month" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Calculate Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Calculate Modal -->
<div class="modal fade" id="bulkCalculateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Bulk Salary Calculation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.salaries.bulk_calculate') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="alert alert-info py-2 mb-3 small">
                        <i data-feather="info" class="me-1" style="width:14px;"></i> This will auto-calculate salaries for all active employees for the chosen month based on attendance and leaves.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Month <span class="text-danger">*</span></label>
                        <input type="month" name="salary_month" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Start Bulk Calculation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection