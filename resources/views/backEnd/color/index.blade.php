@extends('backEnd.layouts.master')
@section('title', 'Manage Colors')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Color Studio Aesthetics */
    .color-hero-header {
        background: linear-gradient(135deg, #ec4899 0%, #f43f5e 50%, #8b5cf6 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(236, 72, 153, 0.25);
        position: relative;
        overflow: hidden;
    }
    .color-hero-header::after {
        content: "";
        position: absolute;
        top: -30px;
        right: -30px;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        pointer-events: none;
    }
    .color-stat-card {
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
    .color-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .color-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .color-stat-icon.pink { background: #fdf2f8; color: #ec4899; }
    .color-stat-icon.emerald { background: #ecfdf5; color: #10b981; }

    /* Modern Table Card */
    .color-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .color-table-card .card-body {
        padding: 22px 24px;
    }

    .table-color thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 14px 16px;
    }
    .table-color tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-color tbody tr:hover td {
        background: #fdf2f8;
    }

    /* 3D Color Swatch & Copy Chip */
    .swatch-box {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15), inset 0 2px 4px rgba(255, 255, 255, 0.4);
        position: relative;
        flex-shrink: 0;
        transition: transform 0.2s;
    }
    .swatch-box:hover {
        transform: scale(1.15);
    }
    .color-hex-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: monospace;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 10px;
        background: #f1f5f9;
        color: #475569;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s;
    }
    .color-hex-chip:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .color-name-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 14.5px;
        margin-bottom: 2px;
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
    .act-btn.edit:hover { background: #fdf2f8; color: #ec4899; border-color: #fbcfe8; }
    .act-btn.delete:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
    .act-btn.toggle-on:hover { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .act-btn.toggle-off:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3">
    
    @php
        $totalColors = $show_data->count();
        $activeColors = $show_data->where('status', 1)->count();
    @endphp

    {{-- Hero Header --}}
    <div class="color-hero-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-aperture me-2"></i> Color Palette & Swatches Studio</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">প্রোডাক্ট ভ্যারিয়েন্টে ব্যবহারের জন্য বিভিন্ন কালার শেড ও হেক্স কোড পরিচালনা করুন।</p>
        </div>
        <a href="{{ route('colors.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark shadow-sm" style="font-size:14px;">
            <i class="fe-plus-circle me-1 text-danger"></i> Add New Color
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-sm-6">
            <div class="color-stat-card">
                <div class="color-stat-icon pink">
                    <i class="fe-droplet"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Colors Registered</div>
                    <h4 class="mb-0 fw-bold">{{ $totalColors }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6">
            <div class="color-stat-card">
                <div class="color-stat-icon emerald">
                    <i class="fe-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Active Palettes</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $activeColors }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="color-table-card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-color w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th>Color Palette & Preview</th>
                                <th>Hex Code</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($show_data as $key=>$value)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="swatch-box" style="background-color: {{ $value->color }};" title="{{ $value->color }}"></div>
                                        <div>
                                            <div class="color-name-title">{{ $value->colorName }}</div>
                                            <small class="text-muted">Color Attribute</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="color-hex-chip" onclick="navigator.clipboard.writeText('{{ $value->color }}'); alert('Copied: {{ $value->color }}');" title="Click to copy Hex Code">
                                        <i class="fe-copy text-muted"></i> {{ $value->color }}
                                    </span>
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
                                            <form method="post" action="{{route('colors.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-on" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('colors.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-off" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{route('colors.edit',$value->id)}}" class="act-btn edit" title="Edit Color">
                                            <i class="fe-edit-2"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="post" action="{{ route('colors.destroy') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="act-btn delete delete-confirm" title="Delete Color">
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
                searchPlaceholder: "Search colors...",
                paginate: {
                    previous: "<i class='fe-arrow-left'></i>",
                    next: "<i class='fe-arrow-right'></i>"
                }
            }
        });
    });
</script>
@endsection