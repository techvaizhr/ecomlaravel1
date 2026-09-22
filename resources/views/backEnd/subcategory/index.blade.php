@extends('backEnd.layouts.master')
@section('title', 'Subcategory Manage')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Subcategory Hierarchy Aesthetics */
    .subcat-hero-header {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(124, 58, 237, 0.25);
        position: relative;
        overflow: hidden;
    }
    .subcat-hero-header::after {
        content: "";
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        pointer-events: none;
    }
    .subcat-stat-card {
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
    .subcat-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .subcat-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .subcat-stat-icon.purple { background: #f3e8ff; color: #9333ea; }
    .subcat-stat-icon.teal { background: #ccfbf1; color: #0d9488; }
    .subcat-stat-icon.blue { background: #dbeafe; color: #2563eb; }

    /* Modern Table Card */
    .subcat-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .subcat-table-card .card-body {
        padding: 22px 24px;
    }

    .table-subcat thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 14px 16px;
    }
    .table-subcat tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-subcat tbody tr:hover td {
        background: #faf5ff;
    }

    /* Parent Relationship Badge */
    .parent-cat-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .subcat-badge-arrow {
        color: #9333ea;
        font-weight: 800;
    }
    .subcat-name-highlight {
        font-weight: 700;
        color: #0f172a;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .subcat-name-highlight i {
        color: #a855f7;
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
    .act-btn.edit:hover { background: #f3e8ff; color: #9333ea; border-color: #d8b4fe; }
    .act-btn.delete:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
    .act-btn.toggle-on:hover { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .act-btn.toggle-off:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3">
    
    @php
        $totalSubcats = $data->count();
        $activeSubcats = $data->where('status', 1)->count();
        $uniqueParents = $data->pluck('category_id')->unique()->count();
    @endphp

    {{-- Hero Header --}}
    <div class="subcat-hero-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-git-branch me-2"></i> Subcategory Tree & Mapping</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">মূল ক্যাটাগরির অধীনে সাবক্যাটাগরি বা দ্বিতীয় স্তরের পণ্য বিভাগসমূহ পরিচালনা করুন।</p>
        </div>
        <a href="{{ route('subcategories.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark shadow-sm" style="font-size:14px;">
            <i class="fe-plus-circle me-1 text-primary"></i> Add Subcategory
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="subcat-stat-card">
                <div class="subcat-stat-icon purple">
                    <i class="fe-layers"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Subcategories</div>
                    <h4 class="mb-0 fw-bold">{{ $totalSubcats }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="subcat-stat-card">
                <div class="subcat-stat-icon teal">
                    <i class="fe-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Active Subcategories</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $activeSubcats }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="subcat-stat-card">
                <div class="subcat-stat-icon blue">
                    <i class="fe-folder"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Parent Categories Linked</div>
                    <h4 class="mb-0 fw-bold text-primary">{{ $uniqueParents }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="subcat-table-card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-subcat w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th>Parent Category</th>
                                <th>Subcategory Name</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                
                                <td>
                                    @if($value->category)
                                        <span class="parent-cat-chip">
                                            <i class="fe-folder text-primary"></i> {{ $value->category->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary rounded-pill px-2.5 py-1">No Parent</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="subcat-name-highlight">
                                        <span class="subcat-badge-arrow">↳</span>
                                        <i class="fe-tag"></i>
                                        <span>{{ $value->subcategoryName }}</span>
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
                                            <form method="post" action="{{route('subcategories.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-on" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('subcategories.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-off" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{route('subcategories.edit',$value->id)}}" class="act-btn edit" title="Edit">
                                            <i class="fe-edit-2"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="post" action="{{ route('subcategories.destroy') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}">
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
                searchPlaceholder: "Search subcategories...",
                paginate: {
                    previous: "<i class='fe-arrow-left'></i>",
                    next: "<i class='fe-arrow-right'></i>"
                }
            }
        });
    });
</script>
@endsection