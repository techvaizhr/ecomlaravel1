@extends('backEnd.layouts.master')
@section('title', 'Manage Roles & Permissions')

@section('css')
<style>
    /* Premium Design System matching Vendor list */
    .roles-header-card {
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

    .role-badge-pill {
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .role-superadmin { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .role-admin { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
    .role-manager { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .role-default { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

    .badge-count {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

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
    .btn-action-view:hover { background: #e0f2fe; color: #0284c7; }
    .btn-action-edit:hover { background: #e0e7ff; color: #4338ca; }
    .btn-action-del:hover { background: #fee2e2; color: #dc2626; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    {{-- HEADER CARD --}}
    <div class="roles-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-shield me-2"></i> Roles & Permissions Management
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    Configure staff roles, customize permission matrices, and auto-sync system keys.
                </p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="metric-badge-box">
                    <h3>{{ $stats['total_roles'] ?? 0 }}</h3>
                    <span>Total Roles</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['total_permissions'] ?? 0 }}</h3>
                    <span>Permissions</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['assigned_staff'] ?? 0 }}</h3>
                    <span>Assigned Staff</span>
                </div>
                <form action="{{ route('roles.syncPermissions') }}" method="POST" class="d-inline" onsubmit="return confirm('Do you want to scan and synchronize all standard system permissions?');">
                    @csrf
                    <button type="submit" class="btn btn-warning rounded-pill px-3 shadow-sm font-size-13 fw-bold text-dark" title="Sync default system permissions">
                        <i class="fe-refresh-cw me-1"></i> Sync Permissions
                    </button>
                </form>
                <a href="{{ route('roles.create') }}" class="btn btn-outline-light rounded-pill px-3 shadow-sm font-size-13 fw-bold">
                    <i class="fe-plus me-1"></i> Create Role
                </a>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card-modern">
        <form method="GET" action="{{ route('roles.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label-modern">Search Role Name</label>
                    <input type="text" name="keyword" class="form-control form-control-modern" 
                           placeholder="Search role by name..." value="{{ request('keyword') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label-modern">Per Page</label>
                    <select name="per_page" class="form-select form-select-modern">
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary rounded-3 w-100 fw-bold font-size-13 py-2">
                            <i class="fe-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'per_page']))
                            <a href="{{ route('roles.index') }}" class="btn btn-light border rounded-3 px-3 py-2" title="Reset Filters">
                                <i class="fe-rotate-ccw"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ROLES TABLE --}}
    <div class="table-card-modern">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Role Name</th>
                        <th>Assigned Permissions</th>
                        <th>Staff Users</th>
                        <th>Guard</th>
                        <th class="text-end" style="width: 150px;">Action</th>
                    </tr>
                </thead>                
                <tbody>
                    @forelse($show_data as $key => $value)
                        @php
                            $roleLower = strtolower($value->name);
                            $isAdminRole = ($roleLower === 'admin' || $roleLower === 'superadmin' || $roleLower === 'super admin');
                            $isLoginAdmin = auth()->check() && method_exists(auth()->user(), 'hasRole')
                                            ? auth()->user()->hasRole('Admin')
                                            : false;
                        @endphp

                        @if($isAdminRole && !$isLoginAdmin)
                            @continue
                        @endif

                        <tr>
                            <td class="text-muted fw-semibold">
                                {{ $loop->iteration + ($show_data->currentPage() - 1) * $show_data->perPage() }}
                            </td>
                            
                            <td>
                                @if($roleLower == 'superadmin' || $roleLower == 'super admin')
                                    <span class="role-badge-pill role-superadmin"><i class="fe-award font-size-12"></i> {{ $value->name }}</span>
                                @elseif($roleLower == 'admin')
                                    <span class="role-badge-pill role-admin"><i class="fe-shield font-size-12"></i> {{ $value->name }}</span>
                                @elseif($roleLower == 'manager' || $roleLower == 'editor')
                                    <span class="role-badge-pill role-manager"><i class="fe-user-check font-size-12"></i> {{ $value->name }}</span>
                                @else
                                    <span class="role-badge-pill role-default"><i class="fe-users font-size-12"></i> {{ $value->name }}</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-count">
                                    <i class="fe-key text-primary font-size-11"></i> {{ $value->permissions_count ?? ($value->permissions ? $value->permissions->count() : 0) }} Permissions
                                </span>
                            </td>

                            <td>
                                <span class="badge-count">
                                    <i class="fe-user text-success font-size-11"></i> {{ $value->users_count ?? 0 }} Users
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">{{ $value->guard_name ?? 'admin' }}</span>
                            </td>

                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    {{-- Show / View --}}
                                    <a href="{{ route('roles.show', $value->id) }}" class="action-circle-btn btn-action-view" title="View Role Permissions">
                                        <i class="fe-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('roles.edit', $value->id) }}" class="action-circle-btn btn-action-edit" title="Edit Role & Permissions">
                                        <i class="fe-edit"></i>
                                    </a>

                                    {{-- Delete --}}
                                    @if(!$isAdminRole)
                                    <form method="post" action="{{ route('roles.destroy') }}" class="d-inline"
                                          onsubmit="return confirm('Delete this role? Users assigned to this role will lose their permissions.');">
                                        @csrf
                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                        <button type="submit" class="action-circle-btn btn-action-del" title="Delete Role">
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
                            <i class="fe-shield font-size-36 d-block mb-2 text-slate-300"></i>
                            <p class="mb-0 font-size-14 fw-semibold">No roles found matching your search.</p>
                            <small class="text-muted">Try changing your search keyword.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($show_data->hasPages() || $show_data->total() > 0)
        <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted font-size-13">
                Showing <strong>{{ $show_data->firstItem() ?? 0 }}</strong> to <strong>{{ $show_data->lastItem() ?? 0 }}</strong> of <strong>{{ $show_data->total() }}</strong> Roles
            </div>
            <div>
                {{ $show_data->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection