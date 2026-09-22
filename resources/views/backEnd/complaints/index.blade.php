@extends('backEnd.layouts.master')
@section('title', 'Customer Complaints & Disputes')

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
    
    .complaint-thumb-img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .complaint-thumb-img:hover { transform: scale(1.15); }

    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-pending { background: #fef3c7; color: #d97706; }
    .badge-processing { background: #e0f2fe; color: #0284c7; }
    .badge-resolved { background: #dcfce7; color: #166534; }

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
                <i data-feather="alert-circle" class="text-danger" style="width: 24px; height: 24px;"></i>
                Customer Complaints & Inquiries
            </h4>
            <p class="text-muted small mb-0">Track and resolve customer support tickets, issues and refund complaints.</p>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="stat-icon"><i class="fas fa-inbox"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total Inquiries</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Pending</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['pending'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Processing</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['processing'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-icon"><i class="fas fa-check-double"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Resolved</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['resolved'] ?? 0) }}</h3>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('backEnd.complaints.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    {{-- Keyword Search --}}
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 14px;"></i></span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control border-start-0" placeholder="Search name, phone, order #, message...">
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
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
                            <a href="{{ route('backEnd.complaints.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i data-feather="rotate-ccw" style="width: 14px;"></i></a>
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
                        <th>Customer</th>
                        <th>Order #</th>
                        <th>Evidence / Proof</th>
                        <th width="30%">Complaint Details</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="100" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $key => $complaint)
                        <tr>
                            <td class="text-muted small">
                                @if(method_exists($complaints, 'firstItem'))
                                    {{ $complaints->firstItem() + $key }}
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            
                            {{-- Customer --}}
                            <td>
                                <div class="fw-bold text-dark">{{ $complaint->name }}</div>
                                <a href="tel:{{ $complaint->phone }}" class="small text-muted d-flex align-items-center gap-1 mt-1">
                                    <i data-feather="phone" style="width: 12px; height: 12px;"></i> {{ $complaint->phone }}
                                </a>
                            </td>

                            {{-- Order Number --}}
                            <td>
                                @if($complaint->order_number)
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        #{{ $complaint->order_number }}
                                    </span>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>

                            {{-- Image Evidence --}}
                            <td>
                                @if($complaint->image)
                                    <a href="{{ asset('public/complaints/' . $complaint->image) }}" target="_blank">
                                        <img src="{{ asset('public/complaints/' . $complaint->image) }}" class="complaint-thumb-img" alt="Proof">
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted">No Image</span>
                                @endif
                            </td>

                            {{-- Message --}}
                            <td>
                                <div class="small text-dark text-break" style="line-height: 1.4;">
                                    {{ $complaint->message }}
                                </div>
                            </td>

                            {{-- Status Changer --}}
                            <td>
                                <form action="{{ route('backEnd.complaints.updateStatus', $complaint->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()" style="width: 120px;">
                                        <option value="pending" {{ $complaint->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                        <option value="processing" {{ $complaint->status == 'processing' ? 'selected' : '' }}>⚙️ Processing</option>
                                        <option value="resolved" {{ $complaint->status == 'resolved' ? 'selected' : '' }}>✅ Resolved</option>
                                    </select>
                                </form>
                            </td>

                            {{-- Date --}}
                            <td class="text-muted small">
                                {{ $complaint->created_at ? $complaint->created_at->format('d M, Y h:i A') : 'N/A' }}
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <form action="{{ route('backEnd.complaints.destroy', $complaint->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon text-danger" title="Delete Complaint">
                                        <i data-feather="trash-2" style="width:14px; height:14px;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.35;" class="mb-2"></i>
                                    <p class="fw-bold mb-1">No Complaints Found</p>
                                    <small class="text-muted">Customer issues and feedback tickets will appear here.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($complaints, 'links') && $complaints->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Showing {{ $complaints->firstItem() }} to {{ $complaints->lastItem() }} of {{ $complaints->total() }} entries
            </div>
            <div>
                {{ $complaints->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush