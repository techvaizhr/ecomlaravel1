@extends('backEnd.layouts.master')
@section('title', 'Popup Offer Management')

@section('css')
<style>
    .stat-card-gradient {
        border: none;
        border-radius: 14px;
        color: #fff;
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .stat-card-gradient .stat-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2.5rem;
        opacity: 0.22;
    }
    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        background: #fff;
    }
    .table-modern th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.9rem 1rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-modern td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #f8fafc; }
    
    .popup-thumb-img {
        width: 80px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        cursor: pointer;
        transition: transform 0.2s;
    }
    .popup-thumb-img:hover { transform: scale(1.1); }

    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #fee2e2; color: #991b1b; }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: all 0.2s;
    }
    .btn-action-icon:hover {
        background: #f1f5f9;
        color: #0f172a;
        transform: translateY(-1px);
    }
    
    .upload-box-dashed {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s;
    }
    .upload-box-dashed:hover {
        border-color: #6366f1;
        background: #eef2ff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i data-feather="bell" class="text-primary" style="width: 24px; height: 24px;"></i>
                Popup Offers & Alerts
            </h4>
            <p class="text-muted small mb-0">Display promotional modal popups and alerts to store visitors.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createPopupModal">
                <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                <span>Add New Popup</span>
            </button>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total Popups</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Active Popups</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['active'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                <div class="stat-icon"><i class="fas fa-pause-circle"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Inactive</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['inactive'] ?? 0) }}</h3>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.popup.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    {{-- Keyword Search --}}
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 14px;"></i></span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control border-start-0" placeholder="Search title or details...">
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Global Date Filter --}}
                    <div class="col-12 col-md-3">
                        @include('backEnd.layouts.partials.smart_date_filter')
                    </div>

                    {{-- Per Page & Submit --}}
                    <div class="col-12 col-md-3 d-flex gap-2 justify-content-end align-items-center">
                        <select name="per_page" class="form-select form-select-sm" style="width: 75px;" onchange="document.getElementById('filterForm').submit()" title="Records per page">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                        @if(request()->anyFilled(['keyword', 'status', 'start_date', 'end_date', 'date_preset']))
                            <a href="{{ route('admin.popup.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i data-feather="rotate-ccw" style="width: 14px;"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="card card-modern">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="100">Banner</th>
                        <th>Title</th>
                        <th>Button & Link</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th width="100" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($popups as $key => $value)
                        <tr>
                            <td class="text-muted small">
                                @if(method_exists($popups, 'firstItem'))
                                    {{ $popups->firstItem() + $key }}
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            
                            {{-- Image --}}
                            <td>
                                @if($value->image)
                                    <a href="{{ url('public/'.$value->image) }}" target="_blank">
                                        <img src="{{ url('public/'.$value->image) }}" class="popup-thumb-img" alt="Popup Banner">
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted">No Image</span>
                                @endif
                            </td>

                            {{-- Title & Desc --}}
                            <td>
                                <div class="fw-bold text-dark">{{ $value->title ?: 'Promo Popup' }}</div>
                                @if($value->description)
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $value->description }}</small>
                                @endif
                            </td>

                            {{-- Button & Link --}}
                            <td>
                                @if($value->btn_text)
                                    <span class="badge bg-light text-dark border me-1">{{ $value->btn_text }}</span>
                                @endif
                                @if($value->link)
                                    <a href="{{ $value->link }}" target="_blank" class="small text-primary text-truncate d-inline-block" style="max-width: 180px;" title="{{ $value->link }}">
                                        {{ $value->link }}
                                    </a>
                                @else
                                    <span class="text-muted small">--</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($value->status == 1)
                                    <span class="badge-soft badge-active"><i class="fas fa-check-circle"></i> Active</span>
                                @else
                                    <span class="badge-soft badge-inactive"><i class="fas fa-pause-circle"></i> Inactive</span>
                                @endif
                            </td>

                            {{-- Created Date --}}
                            <td class="text-muted small">
                                {{ $value->created_at ? $value->created_at->format('d M, Y h:i A') : 'N/A' }}
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.popup.edit', $value->id) }}" class="btn-action-icon text-primary" title="Edit Popup">
                                        <i data-feather="edit-2" style="width:14px; height:14px;"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.popup.destroy', $value->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this popup?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon text-danger" title="Delete Popup">
                                            <i data-feather="trash-2" style="width:14px; height:14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-feather="bell" style="width: 48px; height: 48px; opacity: 0.35;" class="mb-2"></i>
                                    <p class="fw-bold mb-1">No Popup Alerts Found</p>
                                    <small class="text-muted">Create a popup offer to capture leads and boost promotions.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($popups, 'links') && $popups->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Showing {{ $popups->firstItem() }} to {{ $popups->lastItem() }} of {{ $popups->total() }} entries
            </div>
            <div>
                {{ $popups->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- CREATE POPUP MODAL --}}
<div class="modal fade" id="createPopupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-feather="plus-circle" class="text-primary" style="width: 20px;"></i> Add New Popup
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.popup.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    {{-- Upload Area --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Popup Image <span class="text-danger">*</span></label>
                        <div class="upload-box-dashed" onclick="document.getElementById('popupImageInput').click()">
                            <i data-feather="image" class="text-primary mb-2" style="width: 32px; height: 32px;"></i>
                            <div class="fw-bold text-dark">Click to upload popup image</div>
                            <small class="text-muted d-block mt-1">Recommended: 800x800 or 1000x1000 square/banner format. Auto WebP optimized (&lt;300KB).</small>
                            <input type="file" name="image" id="popupImageInput" class="d-none" accept="image/*" required onchange="previewPopupImage(this)">
                            <img id="popupImgPreview" src="" class="mt-3 rounded shadow-sm d-none" style="max-height: 150px; object-fit: contain;">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Title (Optional)</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Flash Mega Sale!">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Button Text (Optional)</label>
                            <input type="text" name="btn_text" class="form-control" placeholder="e.g. Shop Now">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Target Link / URL (Optional)</label>
                            <input type="url" name="link" class="form-control" placeholder="https://yourstore.com/special-offer">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Short Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief announcement details..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" id="popupStatusCheck" value="1" checked>
                                <label class="form-check-label fw-semibold" for="popupStatusCheck">Activate Immediately</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Popup</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script>
    function previewPopupImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('popupImgPreview');
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush