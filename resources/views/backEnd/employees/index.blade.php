@extends('backEnd.layouts.master')
@section('title', 'Employee Management')

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
        width: 40px; height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 14px;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(79, 70, 229, 0.25);
    }
    .emp-id-badge {
        font-size: 0.7rem; background: #f1f5f9; color: #475569;
        padding: 2px 6px; border-radius: 4px; font-weight: 600; margin-left: 6px;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fef3c7; color: #92400e; }
    .status-terminated { background: #fee2e2; color: #991b1b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="users" class="text-primary me-2" style="width:26px;height:26px;"></i> Employee Directory
            </h4>
            <p class="text-muted small mb-0">Manage employees, departmental designations, profiles, and basic salary info.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i data-feather="clock" class="me-1" style="width:16px;height:16px;"></i> Attendance
            </a>
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i data-feather="plus" class="me-1" style="width:16px;height:16px;"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- Live Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon blue">
                    <i data-feather="users" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Employees</div>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($stats['total_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i data-feather="user-check" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Active Staff</div>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['active_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="user-x" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Inactive / Left</div>
                    <h3 class="mb-0 fw-bold text-secondary">{{ number_format(($stats['inactive_count'] ?? 0) + ($stats['terminated_count'] ?? 0)) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon purple">
                    <i data-feather="dollar-sign" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Monthly Payroll Base</div>
                    <h3 class="mb-0 fw-bold text-primary">৳{{ number_format($stats['total_payroll'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.employees.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" style="width:16px;"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Name, email, phone or ID..." value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-select">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / page</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / page</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / page</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / page</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All Records</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i data-feather="filter" class="me-1" style="width:16px;"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'department', 'status', 'designation', 'per_page']))
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
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
                            <th width="28%">Employee Details</th>
                            <th width="20%">Role & Department</th>
                            <th width="15%">Contact</th>
                            <th width="12%">Basic Salary</th>
                            <th width="10%">Status</th>
                            <th width="10%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $key => $employee)
                            <tr>
                                <td class="text-muted small">{{ $employees->firstItem() ? $employees->firstItem() + $key : $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $employee->name }}
                                                @if($employee->employee_id)
                                                    <span class="emp-id-badge">#{{ $employee->employee_id }}</span>
                                                @endif
                                            </div>
                                            <span class="text-muted small">{{ $employee->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $employee->designation ?? 'N/A' }}</span>
                                    <span class="text-muted small"><i data-feather="briefcase" style="width:12px;height:12px;" class="me-1"></i>{{ $employee->department ?? 'General' }}</span>
                                </td>
                                <td>
                                    <span class="text-dark font-monospace small d-block">{{ $employee->phone ?? '—' }}</span>
                                    @if($employee->joining_date)
                                        <span class="text-muted" style="font-size: 0.75rem;">Joined: {{ date('d M, Y', strtotime($employee->joining_date)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">৳{{ number_format($employee->basic_salary, 2) }}</span>
                                </td>
                                <td>
                                    @if($employee->status == 'active')
                                        <span class="status-badge status-active"><span class="status-dot"></span> Active</span>
                                    @elseif($employee->status == 'inactive')
                                        <span class="status-badge status-inactive"><span class="status-dot"></span> Inactive</span>
                                    @else
                                        <span class="status-badge status-terminated"><span class="status-dot"></span> Terminated</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2" title="View Details">
                                            <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-sm btn-primary rounded-pill px-2" title="Edit Info">
                                            <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="d-inline delete-employee-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Delete">
                                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No employees found matching your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} employees
                    </div>
                    <div>
                        {{ $employees->links() }}
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
document.querySelectorAll('.delete-employee-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the employee record!',
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