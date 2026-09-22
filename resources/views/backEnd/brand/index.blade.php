@extends('backEnd.layouts.master')
@section('title', 'Brands Manage')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Brands Showcase Aesthetics */
    .brand-hero-header {
        background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.25);
        position: relative;
        overflow: hidden;
    }
    .brand-hero-header::after {
        content: "";
        position: absolute;
        bottom: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        pointer-events: none;
    }
    .brand-stat-card {
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
    .brand-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .brand-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .brand-stat-icon.orange { background: #fff7ed; color: #ea580c; }
    .brand-stat-icon.emerald { background: #ecfdf5; color: #10b981; }
    .brand-stat-icon.indigo { background: #eef2ff; color: #4f46e5; }

    /* Modern Table Card */
    .brand-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .brand-table-card .card-body {
        padding: 22px 24px;
    }

    .table-brand thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 14px 16px;
    }
    .table-brand tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-brand tbody tr:hover td {
        background: #fffbeb;
    }

    /* Brand Logo Box */
    .brand-logo-wrap {
        width: 60px;
        height: 48px;
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .brand-logo-wrap:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }
    .brand-logo-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .brand-logo-fallback {
        font-weight: 800;
        font-size: 16px;
        color: #ea580c;
    }

    /* Brand Name & Verified Chip */
    .brand-name-wrap {
        font-weight: 700;
        color: #0f172a;
        font-size: 14.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .brand-name-wrap .verified-check {
        color: #3b82f6;
        font-size: 14px;
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
    .act-btn.edit:hover { background: #fff7ed; color: #ea580c; border-color: #fed7aa; }
    .act-btn.delete:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
    .act-btn.toggle-on:hover { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .act-btn.toggle-off:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3">
    
    @php
        $totalBrands = $data->count();
        $activeBrands = $data->where('status', 1)->count();
    @endphp

    {{-- Hero Header --}}
    <div class="brand-hero-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-award me-2"></i> Brand Identity & Manufacturers</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">পণ্য প্রস্তুতকারক ব্র্যান্ড ও পার্টনার লোগোসমূহ প্রদর্শন ও পরিচালনা করুন।</p>
        </div>
        <a href="{{ route('brands.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark shadow-sm" style="font-size:14px;">
            <i class="fe-plus-circle me-1 text-warning"></i> Add New Brand
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-sm-6">
            <div class="brand-stat-card">
                <div class="brand-stat-icon orange">
                    <i class="fe-award"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Brands</div>
                    <h4 class="mb-0 fw-bold">{{ $totalBrands }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6">
            <div class="brand-stat-card">
                <div class="brand-stat-icon emerald">
                    <i class="fe-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Active Brands</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $activeBrands }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="brand-table-card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-brand w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th style="width: 80px;">Logo</th>
                                <th>Brand Name</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                
                                <td>
                                    <div class="brand-logo-wrap">
                                        @if($value->image && file_exists(public_path($value->image)))
                                            <img src="{{ asset($value->image) }}" class="brand-logo-img" alt="{{ $value->name }}">
                                        @else
                                            <span class="brand-logo-fallback">{{ strtoupper(substr($value->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="brand-name-wrap">
                                        <span>{{ $value->name }}</span>
                                        <i class="fe-check-circle verified-check" title="Registered Brand"></i>
                                    </div>
                                    <small class="text-muted" style="font-size:11px;font-family:monospace;">/{{ $value->slug }}</small>
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
                                            <form method="post" action="{{route('brands.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-on" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('brands.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-off" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{route('brands.edit',$value->id)}}" class="act-btn edit" title="Edit Brand">
                                            <i class="fe-edit-2"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="post" action="{{ route('brands.destroy') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="act-btn delete delete-confirm" title="Delete Brand">
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
                searchPlaceholder: "Search brands...",
                paginate: {
                    previous: "<i class='fe-arrow-left'></i>",
                    next: "<i class='fe-arrow-right'></i>"
                }
            }
        });
    });
</script>
@endsection