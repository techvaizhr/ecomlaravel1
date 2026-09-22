@extends('backEnd.layouts.master')
@section('title', 'Manage TikTok Pixels')

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
    
    .tiktok-badge {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        color: #0f172a;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 6px 12px;
        border-radius: 8px;
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

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
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i data-feather="film" class="text-dark" style="width: 24px; height: 24px;"></i>
                TikTok Pixels & Events
            </h4>
            <p class="text-muted small mb-0">Configure TikTok Pixel ID for tracking conversion events from TikTok ad campaigns.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tiktok.pixels.create') }}" class="btn btn-dark px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                <span>Add TikTok Pixel</span>
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="stat-icon"><i class="fab fa-tiktok"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total TikTok Pixels</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Active</div>
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
            <form method="GET" action="{{ route('tiktok.pixels.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    {{-- Keyword Search --}}
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 14px;"></i></span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control border-start-0" placeholder="Search TikTok Pixel ID...">
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
                            <a href="{{ route('tiktok.pixels.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i data-feather="rotate-ccw" style="width: 14px;"></i></a>
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
                        <th>TikTok Pixel ID</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th width="140" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $key => $value)
                        <tr>
                            <td class="text-muted small">
                                @if(method_exists($data, 'firstItem'))
                                    {{ $data->firstItem() + $key }}
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            
                            {{-- Code --}}
                            <td>
                                <span class="tiktok-badge" onclick="copyTiktokCode('{{ $value->code }}')" title="Click to copy">
                                    <i data-feather="film" style="width: 14px; height: 14px;"></i>
                                    {{ $value->code }}
                                    <i data-feather="copy" style="width: 12px; height: 12px;" class="text-muted ms-1"></i>
                                </span>
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
                                {{ $value->created_at ? $value->created_at->format('d M, Y') : 'N/A' }}
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($value->status == 1)
                                        <form action="{{ route('tiktok.pixels.inactive') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn-action-icon text-warning" title="Deactivate Pixel">
                                                <i data-feather="pause-circle" style="width:14px; height:14px;"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('tiktok.pixels.active') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn-action-icon text-success" title="Activate Pixel">
                                                <i data-feather="check-circle" style="width:14px; height:14px;"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('tiktok.pixels.edit', $value->id) }}" class="btn-action-icon text-primary" title="Edit TikTok Pixel">
                                        <i data-feather="edit-2" style="width:14px; height:14px;"></i>
                                    </a>
                                    
                                    <form action="{{ route('tiktok.pixels.destroy') }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this TikTok pixel?');">
                                        @csrf
                                        <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                        <button type="submit" class="btn-action-icon text-danger" title="Delete Pixel">
                                            <i data-feather="trash-2" style="width:14px; height:14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-feather="film" style="width: 48px; height: 48px; opacity: 0.35;" class="mb-2"></i>
                                    <p class="fw-bold mb-1">No TikTok Pixels Found</p>
                                    <small class="text-muted">Add your TikTok Pixel ID to track customer conversions from TikTok ads.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($data, 'links') && $data->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} entries
            </div>
            <div>
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script>
    function copyTiktokCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            toastr.success('TikTok Pixel ID "' + code + '" copied to clipboard!');
        });
    }
</script>
@endpush
