@extends('backEnd.layouts.master')
@section('title', 'Manage Staff Users')

@section('css')
<style>
    /* Premium Design System matching Vendor list */
    .user-header-card {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        border-radius: 16px;
        padding: 24px;
        color: #ffffff;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.15);
    }
    .metric-badge-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 12px;
        padding: 12px 18px;
        text-align: center;
        min-width: 110px;
    }
    .metric-badge-box h3 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .metric-badge-box span {
        color: rgba(255, 255, 255, 0.85);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* Filter Card */
    .filter-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
        padding: 18px 22px;
    }
    .form-label-modern {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .form-control-modern, .form-select-modern {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        padding: 8px 12px;
        font-size: 13.5px;
        color: #0f172a;
        transition: all 0.2s;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        background: #ffffff;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12);
        outline: none;
    }

    /* Modern Table */
    .table-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .table-modern {
        margin-bottom: 0;
        vertical-align: middle;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 13px 16px;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #1e293b;
    }
    .table-modern tbody tr:hover {
        background: #fafcff;
    }

    /* User Avatar */
    .user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        object-fit: cover;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .user-avatar-initials {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.2);
    }

    .role-pill {
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 6px;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .role-pill.admin { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

    /* Action Circle Buttons */
    .action-circle-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.2s;
        text-decoration: none;
        font-size: 13px;
    }
    .action-circle-btn:hover { transform: translateY(-2px); }
    .btn-action-edit:hover { background: #e0e7ff; color: #4338ca; }
    .btn-action-del:hover { background: #fee2e2; color: #dc2626; }
    .btn-action-deact:hover { background: #fff1f2; color: #e11d48; }
    .btn-action-act:hover { background: #f0fdf4; color: #16a34a; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER CARD --}}
    <div class="user-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-users me-2"></i> Admin & Staff Users
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    Manage system administrators, staff accounts, role assignments, and permissions.
                </p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="metric-badge-box">
                    <h3>{{ $stats['total'] ?? 0 }}</h3>
                    <span>Total Staff</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['active'] ?? 0 }}</h3>
                    <span>Active</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['inactive'] ?? 0 }}</h3>
                    <span>Inactive</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['roles_count'] ?? 0 }}</h3>
                    <span>System Roles</span>
                </div>
                <a href="{{ route('roles.index') }}" class="btn btn-warning rounded-pill px-3 shadow-sm font-size-13 fw-bold text-dark">
                    <i class="fe-shield me-1"></i> Roles & Perms
                </a>
                <a href="{{ route('users.create') }}" class="btn btn-outline-light rounded-pill px-3 shadow-sm font-size-13 fw-bold">
                    <i class="fe-plus me-1"></i> Create User
                </a>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card-modern">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label-modern">Search Keyword</label>
                    <input type="text" name="keyword" class="form-control form-control-modern" 
                           placeholder="Search staff name or email..." value="{{ request('keyword') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Filter By Role</label>
                    <select name="role_id" class="form-select form-select-modern">
                        <option value="">All Roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Account Status</label>
                    <select name="status" class="form-select form-select-modern">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Per Page</label>
                    <select name="per_page" class="form-select form-select-modern">
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary rounded-3 w-100 fw-bold font-size-13 py-2">
                            <i class="fe-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'role_id', 'status', 'per_page']))
                            <a href="{{ route('users.index') }}" class="btn btn-light border rounded-3 px-3 py-2" title="Reset Filters">
                                <i class="fe-rotate-ccw"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- USERS TABLE --}}
    <div class="table-card-modern">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Staff Member</th>
                        <th>Email Address</th>
                        <th>Assigned Roles</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $key => $value)
                        @php
                            $isAdminUser  = method_exists($value, 'hasRole') ? $value->hasRole('Admin') : false;
                            $isLoginAdmin = auth()->check() && method_exists(auth()->user(), 'hasRole')
                                            ? auth()->user()->hasRole('Admin')
                                            : false;
                        @endphp

                        @if($isAdminUser && !$isLoginAdmin)
                            @continue
                        @endif

                    <tr>
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($value->image)
                                    <img src="{{ asset($value->image) }}" alt="{{ $value->name }}" class="user-avatar" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                    <div class="user-avatar-initials" style="display:none;">
                                        {{ strtoupper(substr($value->name ?: 'U', 0, 1)) }}
                                    </div>
                                @else
                                    <div class="user-avatar-initials">
                                        {{ strtoupper(substr($value->name ?: 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark font-size-14">{{ $value->name }}</div>
                                    <span class="text-muted font-size-12">ID: #{{ $value->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark font-size-13"><i class="fe-mail text-muted me-1"></i> {{ $value->email }}</span>
                        </td>
                        <td>
                            @if(!empty($value->roles) && $value->roles->count() > 0)
                                @foreach($value->roles as $r)
                                    <span class="role-pill {{ strtolower($r->name) == 'admin' ? 'admin' : '' }}">
                                        <i class="fe-shield font-size-10"></i> {{ $r->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted font-size-12">No role assigned</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ $value->status == 1 ? route('users.inactive') : route('users.active') }}" class="d-inline">
                                @csrf
                                <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                <button type="submit" class="badge rounded-pill border-0 px-2 py-1 font-size-12 {{ $value->status == 1 ? 'bg-success text-white' : 'bg-danger text-white' }}" style="cursor: pointer;" title="Click to toggle status">
                                    {{ $value->status == 1 ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                {{-- Edit --}}
                                <a href="{{ route('users.edit', $value->id) }}" class="action-circle-btn btn-action-edit" title="Edit Staff Member">
                                    <i class="fe-edit"></i>
                                </a>

                                {{-- Delete --}}
                                @if($value->id != 1)
                                <form method="post" action="{{ route('users.destroy') }}" class="d-inline"
                                      onsubmit="return confirm('Delete this staff user account permanently?');">
                                    @csrf
                                    <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                    <button type="submit" class="action-circle-btn btn-action-del" title="Delete User">
                                        <i class="fe-trash-2"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fe-users font-size-36 d-block mb-2 text-slate-300"></i>
                            <p class="mb-0 font-size-14 fw-semibold">No staff users found matching your criteria.</p>
                            <small class="text-muted">Try changing your search keywords or resetting filters.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($data->hasPages() || $data->total() > 0)
        <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted font-size-13">
                Showing <strong>{{ $data->firstItem() ?? 0 }}</strong> to <strong>{{ $data->lastItem() ?? 0 }}</strong> of <strong>{{ $data->total() }}</strong> Users
            </div>
            <div>
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection