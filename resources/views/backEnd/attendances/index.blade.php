@extends('backEnd.layouts.master')
@section('title', 'Attendance Management')

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
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
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
    .badge-present { background: #dcfce7; color: #166534; }
    .badge-absent { background: #fee2e2; color: #991b1b; }
    .badge-late { background: #fef3c7; color: #92400e; }
    .badge-half { background: #e0f2fe; color: #075985; }
    .badge-holiday { background: #f1f5f9; color: #475569; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="clock" class="text-primary me-2" style="width:26px;height:26px;"></i> Employee Attendance
            </h4>
            <p class="text-muted small mb-0">Track daily check-ins, leaves, late entries, and attendance logs.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i data-feather="users" class="me-1" style="width:16px;height:16px;"></i> Employees
            </a>
            <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i data-feather="plus-circle" class="me-1" style="width:16px;height:16px;"></i> Mark Attendance
            </a>
        </div>
    </div>

    <!-- Live Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon blue">
                    <i data-feather="calendar" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Records</div>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($stats['total_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i data-feather="check" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Present Days</div>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['present_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon red">
                    <i data-feather="x" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Absent Days</div>
                    <h3 class="mb-0 fw-bold text-danger">{{ number_format($stats['absent_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="alert-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Late / Half Days</div>
                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($stats['late_half_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.attendances.index') }}">
                <div class="row g-2 align-items-center">
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
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}" placeholder="Specific Date">
                    </div>
                    <div class="col-md-2">
                        <input type="month" name="month" class="form-control" value="{{ request('month') }}" placeholder="Month">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                            <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                            <option value="holiday" {{ request('status') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-select">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 / page</option>
                            <option value="30" {{ request('per_page', 30) == 30 ? 'selected' : '' }}>30 / page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / page</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / page</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i data-feather="filter" class="me-1" style="width:16px;"></i> Filter
                        </button>
                        @if(request()->anyFilled(['employee_id', 'date', 'month', 'status', 'per_page']))
                            <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
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
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th class="text-end" width="12%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $key => $attendance)
                            <tr>
                                <td class="text-muted small">{{ $attendances->firstItem() ? $attendances->firstItem() + $key : $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($attendance->employee->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $attendance->employee->name ?? 'N/A' }}
                                                @if(isset($attendance->employee->employee_id))
                                                    <span class="emp-id-badge">#{{ $attendance->employee->employee_id }}</span>
                                                @endif
                                            </div>
                                            <span class="text-muted small">{{ $attendance->employee->department ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ date('d M, Y', strtotime($attendance->attendance_date)) }}</span>
                                    <span class="text-muted small d-block">{{ date('l', strtotime($attendance->attendance_date)) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted font-monospace">{{ $attendance->check_in ? date('h:i A', strtotime($attendance->check_in)) : '—' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted font-monospace">{{ $attendance->check_out ? date('h:i A', strtotime($attendance->check_out)) : '—' }}</span>
                                </td>
                                <td>
                                    @if($attendance->status == 'present')
                                        <span class="badge-soft badge-present"><span class="status-dot"></span> Present</span>
                                    @elseif($attendance->status == 'absent')
                                        <span class="badge-soft badge-absent"><span class="status-dot"></span> Absent</span>
                                    @elseif($attendance->status == 'late')
                                        <span class="badge-soft badge-late"><span class="status-dot"></span> Late</span>
                                    @elseif($attendance->status == 'half_day')
                                        <span class="badge-soft badge-half"><span class="status-dot"></span> Half Day</span>
                                    @elseif($attendance->status == 'holiday')
                                        <span class="badge-soft badge-holiday"><span class="status-dot"></span> Holiday</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $attendance->notes ? Str::limit($attendance->notes, 30) : '—' }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn btn-sm btn-primary rounded-pill px-2" title="Edit Record">
                                            <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                                        </a>
                                        <form action="{{ route('admin.attendances.destroy', $attendance->id) }}" method="POST" class="d-inline delete-att-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Delete">
                                                <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No attendance logs found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} attendance records
                    </div>
                    <div>
                        {{ $attendances->links() }}
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
document.querySelectorAll('.delete-att-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the attendance record!',
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