@extends('backEnd.layouts.master')
@section('title', 'Bonus Management')

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
        background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%);
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
    .badge-approved { background: #e0f2fe; color: #075985; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="gift" class="text-primary me-2" style="width:26px;height:26px;"></i> Employee Bonuses & Incentives
            </h4>
            <p class="text-muted small mb-0">Record festival bonuses, performance rewards, and incentive disbursements.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i data-feather="users" class="me-1" style="width:16px;height:16px;"></i> Employees
            </a>
            <a href="{{ route('admin.bonuses.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i data-feather="plus-circle" class="me-1" style="width:16px;height:16px;"></i> Add New Bonus
            </a>
        </div>
    </div>

    <!-- Live Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon blue">
                    <i data-feather="gift" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Bonuses</div>
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
                    <div class="text-muted small fw-semibold text-uppercase">Total Bonus Amount</div>
                    <h3 class="mb-0 fw-bold text-primary">৳{{ number_format($stats['total_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="clock" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Pending Approval</div>
                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($stats['pending_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i data-feather="check-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Disbursed Amount</div>
                    <h3 class="mb-0 fw-bold text-success">৳{{ number_format($stats['paid_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.bonuses.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" style="width:16px;"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Employee name, ID or bonus type..." value="{{ request('keyword') }}">
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
                        <select name="bonus_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($bonusTypes as $type)
                                <option value="{{ $type }}" {{ request('bonus_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                        @if(request()->anyFilled(['keyword', 'employee_id', 'bonus_type', 'status', 'per_page']))
                            <a href="{{ route('admin.bonuses.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
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
                            <th>Bonus Type</th>
                            <th>Amount</th>
                            <th>Target Month</th>
                            <th>Status</th>
                            <th>Reason / Note</th>
                            <th class="text-end" width="16%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bonuses as $key => $bonus)
                            <tr>
                                <td class="text-muted small">{{ $bonuses->firstItem() ? $bonuses->firstItem() + $key : $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($bonus->employee->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $bonus->employee->name ?? 'N/A' }}
                                                @if(isset($bonus->employee->employee_id))
                                                    <span class="emp-id-badge">#{{ $bonus->employee->employee_id }}</span>
                                                @endif
                                            </div>
                                            <span class="text-muted small">{{ $bonus->employee->department ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $bonus->bonus_type }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">৳{{ number_format($bonus->amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $bonus->salary_month ? date('M Y', strtotime($bonus->salary_month)) : '—' }}</span>
                                </td>
                                <td>
                                    @if($bonus->status == 'paid')
                                        <span class="badge-soft badge-paid"><span class="status-dot"></span> Paid</span>
                                    @elseif($bonus->status == 'approved')
                                        <span class="badge-soft badge-approved"><span class="status-dot"></span> Approved</span>
                                    @elseif($bonus->status == 'rejected')
                                        <span class="badge-soft badge-rejected"><span class="status-dot"></span> Rejected</span>
                                    @else
                                        <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $bonus->reason ? Str::limit($bonus->reason, 25) : '—' }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 justify-content-end">
                                        @if($bonus->status == 'pending')
                                            <form action="{{ route('admin.bonuses.approve', $bonus->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2" title="Approve Bonus">
                                                    <i data-feather="check" style="width:14px;height:14px;"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $bonus->id }}" title="Reject Bonus">
                                                <i data-feather="x" style="width:14px;height:14px;"></i>
                                            </button>
                                        @elseif($bonus->status == 'approved')
                                            <form action="{{ route('admin.bonuses.pay', $bonus->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-2" title="Disburse from Fund" onclick="return confirm('Pay bonus ৳{{ number_format($bonus->amount, 2) }}? Amount will be deducted from fund.');">
                                                    <i data-feather="dollar-sign" style="width:14px;height:14px;"></i> Pay
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($bonus->status !== 'paid')
                                            <a href="{{ route('admin.bonuses.edit', $bonus->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Edit">
                                                <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $bonus->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-bottom">
                                                    <h5 class="modal-title text-danger fw-bold">Reject Bonus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.bonuses.reject', $bonus->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body py-4">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Reason for Rejection</label>
                                                            <textarea name="notes" class="form-control" rows="3" placeholder="Enter reason..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top">
                                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4">Confirm Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No bonus records found matching your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bonuses->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $bonuses->firstItem() }} to {{ $bonuses->lastItem() }} of {{ $bonuses->total() }} bonus records
                    </div>
                    <div>
                        {{ $bonuses->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection