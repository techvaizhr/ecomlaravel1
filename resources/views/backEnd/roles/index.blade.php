@extends('backEnd.layouts.master')
@section('title','Manage Roles')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    .roles-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 14px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
    }
    .role-stat-box {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 10px;
        padding: 10px 18px;
        text-align: center;
    }
    .role-stat-box h3 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
    .role-stat-box span { color: #94a3b8; font-size: 11.5px; }

    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        background: #fff;
        overflow: hidden;
    }

    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-modern tbody td {
        vertical-align: middle;
        padding: 14px 18px;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tbody tr:hover td { background-color: #f8fafc; }

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

    .action-btn-circle {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        transition: all 0.2s;
        text-decoration: none;
    }
    .action-btn-circle:hover { transform: translateY(-2px); }
    .btn-view-c:hover { background: #e0f2fe; color: #0284c7; }
    .btn-edit-c:hover { background: #e0e7ff; color: #4338ca; }
    .btn-del-c:hover { background: #fee2e2; color: #ef4444; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    {{-- HEADER CARD --}}
    <div class="roles-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-user-check me-2"></i> Roles & Permissions Management
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    অ্যাডমিন প্যানেলের ভূমিকা (Roles) এবং স্টাফ মেম্বারদের জন্য অ্যাক্সেস কন্ট্রোল নিয়ন্ত্রণ করুন।
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="role-stat-box">
                    <h3>{{ $show_data->count() }}</h3>
                    <span>Total Roles</span>
                </div>
                <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fe-plus me-1"></i> Create New Role
                </a>
            </div>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable-buttons" class="table table-modern w-100 dt-responsive nowrap">
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
                        @foreach($show_data as $key => $value)
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
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                
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
                                        <i class="fe-key text-primary font-size-11"></i> {{ $value->permissions_count ?? $value->permissions->count() }} Permissions
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
                                        <a href="{{ route('roles.show', $value->id) }}" class="action-btn-circle btn-view-c" title="View Role Permissions">
                                            <i class="fe-eye font-size-13"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('roles.edit', $value->id) }}" class="action-btn-circle btn-edit-c" title="Edit Role & Permissions">
                                            <i class="fe-edit-2 font-size-13"></i>
                                        </a>

                                        {{-- Delete --}}
                                        @if(!$isAdminRole)
                                        <form method="post" action="{{ route('roles.destroy') }}" class="d-inline"
                                              onsubmit="return confirm('এই রোলটি মুছে ফেলতে চান? এতে এই রোলে থাকা ইউজারদের অ্যাক্সেস রিসেট হবে।');">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="action-btn-circle btn-del-c" title="Delete Role">
                                                <i class="fe-trash-2 font-size-13"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/js/pages/datatables.init.js"></script>
@endsection