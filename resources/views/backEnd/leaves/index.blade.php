@extends('backEnd.layouts.master')
@section('title', 'Leave Management')

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
    .metric-icon.red { background: #fee2e2; color: #dc2626; }
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
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
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
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="calendar" class="text-primary me-2" style="width:26px;height:26px;"></i> Employee Leaves
            </h4>
            <p class="text-muted small mb-0">Track and manage employee leave applications, approvals, and history.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i data-feather="users" class="me-1" style="width:16px;height:16px;"></i> Employees
            </a>
            <a href="{{ route('admin.leaves.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i data-feather="plus-circle" class="me-1" style="width:16px;height:16px;"></i> Apply for Leave
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
                    <div class="text-muted small fw-semibold text-uppercase">Total Applications</div>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($stats['total_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="clock" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Pending Approvals</div>
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
                    <div class="text-muted small fw-semibold text-uppercase">Approved Days</div>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['approved_days'] ?? 0) }} Days</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon red">
                    <i data-feather="x-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Rejected Requests</div>
                    <h3 class="mb-0 fw-bold text-danger">{{ number_format($stats['rejected_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.leaves.index') }}">
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
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="leave_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="sick" {{ request('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                            <option value="casual" {{ request('leave_type') == 'casual' ? 'selected' : '' }}>Casual Leave</option>
                            <option value="annual" {{ request('leave_type') == 'annual' ? 'selected' : '' }}>Annual Leave</option>
                            <option value="emergency" {{ request('leave_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="maternity" {{ request('leave_type') == 'maternity' ? 'selected' : '' }}>Maternity</option>
                            <option value="paternity" {{ request('leave_type') == 'paternity' ? 'selected' : '' }}>Paternity</option>
                            <option value="unpaid" {{ request('leave_type') == 'unpaid' ? 'selected' : '' }}>Unpaid Leave</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="per_page" class="form-select">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / p</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / p</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / p</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i data-feather="filter" class="me-1" style="width:16px;"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'employee_id', 'status', 'leave_type', 'per_page']))
                            <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
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
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Date Range</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Reason / Note</th>
                            <th class="text-end" width="16%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $key => $leave)
                            <tr>
                                <td class="text-muted small">{{ $leaves->firstItem() ? $leaves->firstItem() + $key : $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($leave->employee->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $leave->employee->name ?? 'N/A' }}
                                                @if(isset($leave->employee->employee_id))
                                                    <span class="emp-id-badge">#{{ $leave->employee->employee_id }}</span>
                                                @endif
                                            </div>
                                            <span class="text-muted small">{{ $leave->employee->department ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark text-capitalize">{{ ucfirst($leave->leave_type) }} Leave</span>
                                </td>
                                <td>
                                    <span class="text-dark small d-block">
                                        {{ date('d M, Y', strtotime($leave->start_date)) }} — {{ date('d M, Y', strtotime($leave->end_date)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border px-2 py-1 fw-bold fs-6">{{ $leave->total_days }} {{ Str::plural('day', $leave->total_days) }}</span>
                                </td>
                                <td>
                                    @if($leave->status == 'approved')
                                        <span class="badge-soft badge-approved"><span class="status-dot"></span> Approved</span>
                                    @elseif($leave->status == 'rejected')
                                        <span class="badge-soft badge-rejected"><span class="status-dot"></span> Rejected</span>
                                    @else
                                        <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $leave->reason ? Str::limit($leave->reason, 25) : '—' }}</span>
                                    @if($leave->admin_note)
                                        <small class="text-danger d-block fst-italic">Note: {{ Str::limit($leave->admin_note, 25) }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 justify-content-end">
                                        @if($leave->status == 'pending')
                                            <form action="{{ route('admin.leaves.approve', $leave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2" title="Approve Leave">
                                                    <i data-feather="check" style="width:14px;height:14px;"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}" title="Reject Leave">
                                                <i data-feather="x" style="width:14px;height:14px;"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.leaves.edit', $leave->id) }}" class="btn btn-sm btn-primary rounded-pill px-2" title="Edit Record">
                                            <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                                        </a>
                                        <form action="{{ route('admin.leaves.destroy', $leave->id) }}" method="POST" class="d-inline delete-leave-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Delete">
                                                <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-bottom">
                                                    <h5 class="modal-title text-danger fw-bold">
                                                        <i data-feather="alert-circle" class="me-2"></i> Reject Leave Request #{{ $leave->id }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.leaves.reject', $leave->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body py-4">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                                                            <textarea name="admin_note" class="form-control" rows="3" required placeholder="Enter rejection reason..."></textarea>
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
                                    <p class="mb-0">No leave applications found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leaves->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $leaves->firstItem() }} to {{ $leaves->lastItem() }} of {{ $leaves->total() }} leave requests
                    </div>
                    <div>
                        {{ $leaves->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-leave-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the leave request!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(r) {
            if (r.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection