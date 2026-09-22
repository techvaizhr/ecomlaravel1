@extends('backEnd.layouts.master')
@section('title', 'Manage Users')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    .users-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 14px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
    }
    .user-stat-box {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 10px;
        padding: 10px 18px;
        text-align: center;
    }
    .user-stat-box h3 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
    .user-stat-box span { color: #94a3b8; font-size: 11.5px; }

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

    .user-avatar-wrap {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .user-avatar-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .role-tag {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .role-tag.admin { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

    .status-badge {
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    .status-dot-sm { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

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
    .btn-edit-c:hover { background: #e0e7ff; color: #4338ca; }
    .btn-del-c:hover { background: #fee2e2; color: #ef4444; }
    .btn-deact:hover { background: #fff1f2; color: #e11d48; }
    .btn-act:hover { background: #f0fdf4; color: #16a34a; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER CARD --}}
    <div class="users-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-users me-2"></i> Admin & Staff Users
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    অ্যাডমিন প্যানেল ব্যবহারকারী স্টাফদের তালিকা, ভূমিকা ও অ্যাকাউন্ট স্ট্যাটাস।
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="user-stat-box">
                    <h3>{{ $data->count() }}</h3>
                    <span>Total Staff</span>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fe-plus me-1"></i> Create New User
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
                            <th>Staff Member</th>
                            <th>Email Address</th>
                            <th>Assigned Roles</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $value)
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
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                
                                {{-- Name & Avatar --}}
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar-wrap">
                                            @if($value->image && file_exists(public_path($value->image)))
                                                <img src="{{ asset($value->image) }}" alt="{{ $value->name }}">
                                            @else
                                                {{ strtoupper(substr($value->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark font-size-14">{{ $value->name }}</strong>
                                            <small class="text-muted">ID: #{{ $value->id }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td>
                                    <span class="text-dark font-size-13"><i class="fe-mail text-muted me-1"></i> {{ $value->email }}</span>
                                </td>

                                {{-- Roles --}}
                                <td>
                                    @if(!empty($value->roles) && $value->roles->count() > 0)
                                        @foreach($value->roles as $r)
                                            <span class="role-tag {{ strtolower($r->name) == 'admin' ? 'admin' : '' }}">
                                                <i class="fe-shield font-size-10"></i> {{ $r->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted font-size-12">No role assigned</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($value->status == 1)
                                        <span class="status-badge status-active"><span class="status-dot-sm"></span> Active</span>
                                    @else
                                        <span class="status-badge status-inactive"><span class="status-dot-sm"></span> Inactive</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        
                                        {{-- Status Toggle --}}
                                        <form method="post" action="{{ $value->status == 1 ? route('users.inactive') : route('users.active') }}" class="d-inline"
                                              onsubmit="return confirm('স্টাফ অ্যাকাউন্টটি {{ $value->status == 1 ? 'নিষ্ক্রিয়' : 'সক্রিয়' }} করতে চান?');">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="action-btn-circle {{ $value->status == 1 ? 'btn-deact' : 'btn-act' }}" 
                                                    title="{{ $value->status == 1 ? 'Deactivate User' : 'Activate User' }}">
                                                <i class="fe-{{ $value->status == 1 ? 'thumbs-down' : 'thumbs-up' }} font-size-13"></i>
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <a href="{{ route('users.edit', $value->id) }}" class="action-btn-circle btn-edit-c" title="Edit Staff Member">
                                            <i class="fe-edit-2 font-size-13"></i>
                                        </a>

                                        {{-- Delete --}}
                                        @if($value->id != 1)
                                        <form method="post" action="{{ route('users.destroy') }}" class="d-inline"
                                              onsubmit="return confirm('এই স্টাফ অ্যাকাউন্ট মুছে ফেলবেন?');">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="action-btn-circle btn-del-c" title="Delete User">
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