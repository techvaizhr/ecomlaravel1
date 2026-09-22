@extends('backEnd.layouts.master')
@section('title', 'Delivery Locations - Divisions')

@section('css')
<style>
    .metric-card {
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        background: #fff;
        border: 1px solid #edf2f7;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .metric-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .metric-icon.blue { background: #e0f2fe; color: #0284c7; }
    .metric-icon.green { background: #dcfce7; color: #16a34a; }
    .metric-icon.amber { background: #fef3c7; color: #d97706; }

    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
        overflow: hidden;
    }
    .table-modern th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid #edf2f7;
        white-space: nowrap;
    }
    .table-modern td {
        vertical-align: middle;
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #fbfcfe; }
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #f1f5f9; color: #64748b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Top Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="map-pin" class="text-primary me-2" style="width:26px;height:26px;"></i>
                Delivery Locations — Divisions
            </h4>
            <p class="text-muted small mb-0">Configure regional delivery divisions, districts, and upazilas.</p>
        </div>
        <a href="{{ route('admin.delivery.divisions.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i data-feather="plus" class="me-1" style="width:16px;height:16px;"></i> Add Division
        </a>
    </div>

    <!-- Live Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-icon blue">
                    <i data-feather="map" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Divisions</div>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($stats['total'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i data-feather="check-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Active Divisions</div>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['active'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="slash" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Inactive Divisions</div>
                    <h3 class="mb-0 fw-bold text-secondary">{{ number_format($stats['inactive'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.delivery.divisions.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" style="width:16px;"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Search division name..." value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-select">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / page</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / page</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / page</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i data-feather="filter" class="me-1" style="width:16px;"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'status', 'per_page']))
                            <a href="{{ route('admin.delivery.divisions.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                                <i data-feather="refresh-cw" style="width:16px;"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card card-modern">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Division Name</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th class="text-end" width="22%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($show_data as $key => $value)
                            <tr>
                                <td class="text-muted small">{{ $show_data->firstItem() ? $show_data->firstItem() + $key : $key + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $value->name }}</td>
                                <td><span class="badge bg-light text-muted border">{{ $value->sort_order }}</span></td>
                                <td>
                                    @if($value->status)
                                        <span class="badge-soft badge-active"><span class="status-dot"></span> Active</span>
                                    @else
                                        <span class="badge-soft badge-inactive"><span class="status-dot"></span> Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.delivery.districts.index', ['division' => $value->id]) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                            <i data-feather="map" class="me-1" style="width:14px;height:14px;"></i> Districts
                                        </a>
                                        <a href="{{ route('admin.delivery.divisions.edit', $value->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                            <i data-feather="edit-2" class="me-1" style="width:14px;height:14px;"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.delivery.divisions.destroy') }}" method="POST" class="d-inline delete-form-delivery">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i data-feather="trash-2" class="me-1" style="width:14px;height:14px;"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No delivery divisions found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($show_data->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $show_data->firstItem() }} to {{ $show_data->lastItem() }} of {{ $show_data->total() }} divisions
                    </div>
                    <div>
                        {{ $show_data->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-form-delivery').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the division and associated records!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(r) {
            if (r.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
