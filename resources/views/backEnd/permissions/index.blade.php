@extends('backEnd.layouts.master')
@section('title','Manage Permissions')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    .perm-header-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border-radius: 14px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(30, 27, 75, 0.15);
    }
    .perm-stat-box {
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.18);
        border-radius: 10px;
        padding: 10px 18px;
        text-align: center;
    }
    .perm-stat-box h3 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
    .perm-stat-box span { color: #c7d2fe; font-size: 11.5px; }

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
    .table-modern tbody tr:hover td {
        background-color: #f8fafc;
    }

    .module-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .perm-code {
        font-family: 'Consolas', 'Courier New', monospace;
        background: #f1f5f9;
        color: #0f172a;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
    }
    .badge-core {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .action-btn-circle {
        width: 32px;
        height: 32px;
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

    .info-callout {
        background: #f0fdf4;
        border-left: 4px solid #22c55e;
        padding: 12px 16px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 20px;
        font-size: 12.5px;
        color: #166534;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER CARD --}}
    <div class="perm-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-shield me-2"></i> Permissions & Access Control
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    সিস্টেমের সকল পারমিশন ও মডিউল অ্যাক্সেস কন্ট্রোল তালিকা।
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="perm-stat-box">
                    <h3>{{ $show_data->count() }}</h3>
                    <span>Total Permissions</span>
                </div>
                <a href="{{ route('permissions.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fe-plus me-1"></i> Add New Permission
                </a>
            </div>
        </div>
    </div>

    <div class="info-callout">
        <strong>💡 পারমিশন কীভাবে কাজ করে?</strong> পারমিশনগুলোর নাম সরাসরি ব্যাকএন্ড কন্ট্রোলারের সাথে যুক্ত। কোনো রোলের অ্যাক্সেস সেট করতে <strong>Roles → Create / Edit</strong> পেজ থেকে সহজে চেকবক্স সিলেক্ট করে দিন।
    </div>

    <div class="card card-modern">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable-buttons" class="table table-modern w-100 dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Module / Category</th>
                            <th>Permission Key (Code)</th>
                            <th>Guard</th>
                            <th>Type</th>
                            <th class="text-end" style="width: 120px;">Action</th>
                        </tr>
                    </thead>                
                    <tbody>
                        @foreach($show_data as $key => $value)
                        @php
                            $parts = explode('-', $value->name);
                            if (count($parts) > 1) {
                                $action = array_pop($parts);
                                $moduleName = ucwords(str_replace('_', ' ', implode(' ', $parts)));
                            } else {
                                $moduleName = 'General';
                            }
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <span class="module-pill">
                                    <i class="fe-folder font-size-11"></i> {{ $moduleName }}
                                </span>
                            </td>
                            <td>
                                <span class="perm-code">{{ $value->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $value->guard_name ?? 'admin' }}</span>
                            </td>
                            <td>
                                <span class="badge-core"><i class="fe-check font-size-10"></i> System</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('permissions.edit', $value->id) }}" class="action-btn-circle btn-edit-c" title="Edit Permission">
                                        <i class="fe-edit-2 font-size-13"></i>
                                    </a>
                                    <form method="post" action="{{ route('permissions.destroy') }}" class="d-inline"
                                          onsubmit="return confirm('সতর্কতা! এই পারমিশন ডিলিট করলে সংশ্লিষ্ট রোলের ইউজারদের ওই ফিচারের অ্যাক্সেস নষ্ট হতে পারে। নিশ্চিত?');">
                                        @csrf
                                        <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                        <button type="submit" class="action-btn-circle btn-del-c" title="Delete Permission">
                                            <i class="fe-trash-2 font-size-13"></i>
                                        </button>
                                    </form>
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