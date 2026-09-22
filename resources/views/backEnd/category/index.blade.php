@extends('backEnd.layouts.master')
@section('title', 'Category Manage')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Category Hub Aesthetics */
    .cat-hub-header {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 50%, #06b6d4 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.25);
        position: relative;
        overflow: hidden;
    }
    .cat-hub-header::after {
        content: "";
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
    }
    .cat-stat-card {
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
    .cat-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .cat-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .cat-stat-icon.indigo { background: #eef2ff; color: #4f46e5; }
    .cat-stat-icon.emerald { background: #ecfdf5; color: #10b981; }
    .cat-stat-icon.amber { background: #fffbeb; color: #f59e0b; }

    /* Modern Table Card */
    .cat-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .cat-table-card .card-body {
        padding: 22px 24px;
    }

    .table-cat thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 14px 16px;
    }
    .table-cat tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-cat tbody tr:hover td {
        background: #f8fafc;
    }

    /* Category Image Box */
    .cat-avatar-wrap {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .cat-avatar-wrap:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }
    .cat-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .cat-avatar-fallback {
        font-weight: 800;
        font-size: 18px;
        color: #4f46e5;
    }

    /* Category Title & Badges */
    .cat-name-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 14.5px;
        margin-bottom: 2px;
    }
    .cat-slug-badge {
        display: inline-block;
        font-size: 11px;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 6px;
        font-family: monospace;
    }
    .cat-front-badge {
        background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
        color: #4338ca;
        border: 1px solid #c7d2fe;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Action Buttons */
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
    .act-btn.edit:hover { background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; }
    .act-btn.delete:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
    .act-btn.toggle-on:hover { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .act-btn.toggle-off:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3">
    
    @php
        $totalCats = $data->count();
        $activeCats = $data->where('status', 1)->count();
        $frontCats = $data->where('front_view', 1)->count();
    @endphp

    {{-- Category Hub Hero Header --}}
    <div class="cat-hub-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-grid me-2"></i> Category Catalog Hub</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">ই-কমার্সের মূল ক্যাটাগরিগুলো সুবিন্যস্তভাবে পরিচালনা ও হোমপেজে প্রদর্শন নিয়ন্ত্রণ করুন।</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary shadow-sm" style="font-size:14px;">
            <i class="fe-plus-circle me-1"></i> Add New Category
        </a>
    </div>

    {{-- Stats Summary Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="cat-stat-card">
                <div class="cat-stat-icon indigo">
                    <i class="fe-folder"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Categories</div>
                    <h4 class="mb-0 fw-bold">{{ $totalCats }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="cat-stat-card">
                <div class="cat-stat-icon emerald">
                    <i class="fe-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Active Categories</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $activeCats }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="cat-stat-card">
                <div class="cat-stat-icon amber">
                    <i class="fe-eye"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Homepage Featured</div>
                    <h4 class="mb-0 fw-bold text-warning">{{ $frontCats }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="cat-table-card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-cat w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th style="width: 70px;">Thumbnail</th>
                                <th>Category Details</th>
                                <th>Homepage Display</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                
                                <td>
                                    <div class="cat-avatar-wrap">
                                        @if($value->image && file_exists(public_path($value->image)))
                                            <img src="{{ asset($value->image) }}" class="cat-avatar-img" alt="{{ $value->name }}">
                                        @else
                                            <span class="cat-avatar-fallback">{{ strtoupper(substr($value->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="cat-name-title">{{ $value->name }}</div>
                                    <span class="cat-slug-badge">/{{ $value->slug }}</span>
                                </td>

                                <td>
                                    @if ($value->front_view == 1)
                                        <span class="cat-front-badge">
                                            <i class="fe-star"></i> Featured on Home
                                        </span>
                                    @else
                                        <span class="text-muted small"><i class="fe-minus"></i> Standard</span>
                                    @endif
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
                                            <form method="post" action="{{route('categories.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-on" title="Deactivate Category">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('categories.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="act-btn toggle-off" title="Activate Category">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{route('categories.edit',$value->id)}}" class="act-btn edit" title="Edit Category">
                                            <i class="fe-edit-2"></i>
                                        </a>

                                        {{-- Delete --}}
                                        @can('category-delete')
                                        <form method="post" action="{{route('categories.destroy')}}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                            <button type="submit" class="act-btn delete delete-confirm" title="Delete Category">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </form>
                                        @endcan
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
                searchPlaceholder: "Search categories...",
                paginate: {
                    previous: "<i class='fe-arrow-left'></i>",
                    next: "<i class='fe-arrow-right'></i>"
                }
            }
        });
    });
</script>
@endsection