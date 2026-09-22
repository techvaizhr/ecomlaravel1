@extends('backEnd.layouts.master')
@section('title', 'Newsletter Subscribers')

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
    
    .email-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 500;
        color: #0f172a;
    }

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
                <i data-feather="mail" class="text-primary" style="width: 24px; height: 24px;"></i>
                Newsletter Subscribers
            </h4>
            <p class="text-muted small mb-0">Email addresses gathered from website subscription widgets and footers.</p>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total Subscribers</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Subscribed Today</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['today'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">This Month</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['this_month'] ?? 0) }}</h3>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.newsletter.subscribers') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    {{-- Keyword Search --}}
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 14px;"></i></span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control border-start-0" placeholder="Search email address...">
                        </div>
                    </div>

                    {{-- Global Date Filter --}}
                    <div class="col-12 col-md-4">
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
                        @if(request()->anyFilled(['keyword', 'start_date', 'end_date', 'date_preset']))
                            <a href="{{ route('admin.newsletter.subscribers') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i data-feather="rotate-ccw" style="width: 14px;"></i></a>
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
                        <th>Subscriber Email</th>
                        <th>Subscribed Date & Time</th>
                        <th width="120" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $key => $row)
                        <tr>
                            <td class="text-muted small">
                                @if(method_exists($subscribers, 'firstItem'))
                                    {{ $subscribers->firstItem() + $key }}
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            
                            {{-- Email --}}
                            <td>
                                <div class="email-chip">
                                    <i data-feather="mail" class="text-primary" style="width: 14px; height: 14px;"></i>
                                    <span>{{ $row->email }}</span>
                                    <button type="button" class="btn btn-link p-0 text-muted ms-2" onclick="copyEmail('{{ $row->email }}')" title="Copy email">
                                        <i data-feather="copy" style="width: 12px; height: 12px;"></i>
                                    </button>
                                </div>
                            </td>

                            {{-- Subscribed Date --}}
                            <td class="text-muted small">
                                {{ $row->created_at ? $row->created_at->format('d M, Y h:i A') : 'N/A' }}
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="mailto:{{ $row->email }}" class="btn-action-icon text-primary" title="Send Email">
                                        <i data-feather="send" style="width:14px; height:14px;"></i>
                                    </a>
                                    <form action="{{ route('admin.newsletter.subscribers.delete', $row->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to remove this subscriber?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon text-danger" title="Delete Subscriber">
                                            <i data-feather="trash-2" style="width:14px; height:14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-feather="mail" style="width: 48px; height: 48px; opacity: 0.35;" class="mb-2"></i>
                                    <p class="fw-bold mb-1">No Subscribers Found</p>
                                    <small class="text-muted">Newsletter subscriptions from the storefront will be cataloged here.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($subscribers, 'links') && $subscribers->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Showing {{ $subscribers->firstItem() }} to {{ $subscribers->lastItem() }} of {{ $subscribers->total() }} entries
            </div>
            <div>
                {{ $subscribers->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script>
    function copyEmail(email) {
        navigator.clipboard.writeText(email).then(() => {
            toastr.success('Email "' + email + '" copied to clipboard!');
        });
    }
</script>
@endpush
