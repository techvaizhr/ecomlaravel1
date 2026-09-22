@extends('backEnd.layouts.master')
@section('title', 'Childcategory Manage')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Childcategory Hub Aesthetics */
    .childcat-hero-header {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #14b8a6 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25);
        position: relative;
        overflow: hidden;
    }
    .childcat-hero-header::after {
        content: "";
        position: absolute;
        top: -30px;
        right: -30px;
        width: 170px;
        height: 170px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        pointer-events: none;
    }
    .childcat-stat-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 16px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .childcat-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .childcat-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .childcat-stat-icon.emerald { background: #ecfdf5; color: #059669; }
    .childcat-stat-icon.teal { background: #ccfbf1; color: #0f766e; }
    .childcat-stat-icon.cyan { background: #cffafe; color: #0891b2; }

    /* Modern Table Card */
    .childcat-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .childcat-table-card .card-body {
        padding: 22px 24px;
    }

    .table-childcat thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 14px 16px;
    }
    .table-childcat tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-childcat tbody tr:hover td {
        background: #f0fdf4;
    }

    /* 3-Tier Pathway Chip */
    .pathway-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
    }
    .pathway-chip .arrow {
        color: #10b981;
        font-weight: bold;
    }
    .child-name-badge {
        font-weight: 700;
        color: #0f172a;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .child-name-badge i {
        color: #10b981;
    }

    /* Actions */
    .act-btn {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 1.5px solid transparent;
        transition: all 0.2s ease;
        background: #f8fafc;
        color: #64748b;
    }
    .act-btn:hover {
        transform: translateY(-1px);
    }
    .act-btn.edit:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
    .act-btn.delete:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
    .act-btn.toggle-on:hover { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .act-btn.toggle-off:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3">
    
    @php
        $totalChildcats = $data->count();
        $activeChildcats = $data->where('status', 1)->count();
        $uniqueSubcats = $data->pluck('subcategory_id')->unique()->count();
    @endphp

    {{-- Hero Header --}}
    <div class="childcat-hero-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-git-commit me-2"></i> Childcategory Classification</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">নির্দিষ্ট সাবক্যাটাগরির আওতায় তৃতীয় স্তরের চাইল্ড ক্যাটাগরিগুলো পরিচালনা করুন।</p>
        </div>
        <a href="{{ route('childcategories.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark shadow-sm" style="font-size:14px;">
            <i class="fe-plus-circle me-1 text-success"></i> Add Childcategory
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="childcat-stat-card">
                <div class="childcat-stat-icon emerald">
                    <i class="fe-tag"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Child Categories</div>
                    <h4 class="mb-0 fw-bold">{{ $totalChildcats }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="childcat-stat-card">
                <div class="childcat-stat-icon teal">
                    <i class="fe-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Active Categories</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $activeChildcats }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="childcat-stat-card">
                <div class="childcat-stat-icon cyan">
                    <i class="fe-layers"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Subcategories Covered</div>
                    <h4 class="mb-0 fw-bold text-info">{{ $uniqueSubcats }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="childcat-table-card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-childcat w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th>Subcategory Pathway</th>
                                <th>Childcategory Name</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                
                                <td>
                                    @if($value->subcategory)
                                        <div class="pathway-chip">
                                            @if($value->subcategory->category)
                                                <span class="text-muted"><i class="fe-folder"></i> {{ $value->subcategory->category->name }}</span>
                                                <span class="arrow">›</span>
                                            @endif
                                            <span class="fw-semibold text-primary"><i class="fe-layers"></i> {{ $value->subcategory->subcategoryName }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary rounded-pill px-2.5 py-1">No Parent</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="child-name-badge">
                                        <span style="color:#10b981;font-weight:bold;">↳</span>
                                        <i class="fe-bookmark"></i>
                                        <span>{{ $value->childcategoryName }}</span>
                                    </div>
                                    <small class="text-muted" style="font-size:11px;font-family:monospace;margin-left:22px;">/{{ $value->slug }}</small>
                                </td>
                                
                                <td>
                                    @if($value->status == 1)
                                        <span class="badge bg-soft-success text-success px-2.5 py-1.5 rounded-pill fw-bold" style="font-size:11.5px;">
                                            <i class="fe-check-circle me-1"></i> Active
                                        </span> 
                                    @else 
                                        <span class="badge bg-soft-danger text-danger px-2.5 py-1.5 rounded-pill fw-bold" style="font-size:11.5px;">
                                            <i class="fe-x-circle me-1"></i> Inactive
                                        </span> 
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Status Toggle --}}
                                        @if($value->status == 1)
                                            <form method="post" action="{{route('childcategories.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-on" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('childcategories.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-off" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{route('childcategories.edit',$value->id)}}" class="act-btn edit" title="Edit">
                                            <i class="fe-edit-2"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="post" action="{{ route('childcategories.destroy') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="act-btn delete delete-confirm" title="Delete">
                                                <i class="fe-trash-2"></i>
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
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        $('#datatable-buttons').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search childcategories...",
                paginate: {
                    previous: "<i class='fe-arrow-left'></i>",
                    next: "<i class='fe-arrow-right'></i>"
                }
            }
        });
    });
</script>
@endsection