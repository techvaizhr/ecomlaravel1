@extends('backEnd.layouts.master')
@section('title', 'Reseller KYC Verification Requests')

@section('css')
<style>
    /* Header Card */
    .verification-header-card {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #7c3aed 100%);
        border-radius: 16px;
        padding: 24px;
        color: #ffffff;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15);
    }
    .metric-badge-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 12px;
        padding: 12px 18px;
        text-align: center;
        min-width: 110px;
    }
    .metric-badge-box h3 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .metric-badge-box span {
        color: rgba(255, 255, 255, 0.85);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* Filter Card */
    .filter-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
        padding: 18px 22px;
    }
    .form-label-modern {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .form-control-modern, .form-select-modern {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        padding: 8px 12px;
        font-size: 13.5px;
        color: #0f172a;
        transition: all 0.2s;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        outline: none;
    }

    /* Table */
    .table-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 13px 16px;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        vertical-align: middle;
        color: #1e293b;
    }
    .table-modern tbody tr:hover {
        background: #fafcff;
    }

    /* Doc Thumbnail */
    .doc-thumb-box {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .doc-thumb-box:hover { transform: scale(1.1); }
    .doc-thumb-box img { width: 100%; height: 100%; object-fit: cover; }

    /* Action Buttons */
    .action-circle-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.2s;
        text-decoration: none;
        font-size: 13px;
    }
    .action-circle-btn:hover { transform: translateY(-2px); }
    .btn-action-view:hover { background: #e0f2fe; color: #0284c7; }
    .btn-action-approve:hover { background: #dcfce7; color: #15803d; }
    .btn-action-reject:hover { background: #fee2e2; color: #dc2626; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER CARD --}}
    <div class="verification-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-shield me-2"></i> Reseller KYC Verification Requests
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    Review submitted NID cards and identity credentials of reseller partners.
                </p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="metric-badge-box">
                    <h3>{{ $stats['total'] }}</h3>
                    <span>Total</span>
                </div>
                <div class="metric-badge-box" style="background: rgba(234, 179, 8, 0.3);">
                    <h3>{{ $stats['pending'] }}</h3>
                    <span>Pending</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['approved'] }}</h3>
                    <span>Approved</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['rejected'] }}</h3>
                    <span>Rejected</span>
                </div>
                <a href="{{ route('admin.resellers.index') }}" class="btn btn-outline-light rounded-pill px-3 shadow-sm font-size-13 fw-bold">
                    <i class="fe-arrow-left me-1"></i> All Resellers
                </a>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card-modern">
        <form method="GET" action="{{ route('admin.reseller.verification.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label-modern">Search Reseller</label>
                    <input type="text" name="keyword" class="form-control form-control-modern" 
                           placeholder="Reseller name, shop, phone, email..." value="{{ request('keyword') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Status</label>
                    <select name="status" class="form-select form-select-modern">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Requests</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-modern" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-modern" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label-modern">Per Page</label>
                    <select name="per_page" class="form-select form-select-modern">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary rounded-3 w-100 fw-bold font-size-13 py-2" style="background: #6366f1; border-color: #6366f1;">
                            <i class="fe-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'status', 'date_from', 'date_to', 'per_page']))
                            <a href="{{ route('admin.reseller.verification.index') }}" class="btn btn-light border rounded-3 px-3 py-2" title="Reset Filters">
                                <i class="fe-rotate-ccw"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="table-card-modern">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Reseller & Shop</th>
                        <th>Contact Details</th>
                        <th>Documents</th>
                        <th>Submitted At</th>
                        <th>KYC Status</th>
                        <th class="text-end" style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resellers as $reseller)
                    <tr>
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration + ($resellers->currentPage() - 1) * $resellers->perPage() }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark font-size-14">{{ $reseller->name }}</div>
                            <span class="text-muted font-size-12">Shop: {{ $reseller->shop_name ?? '—' }} (ID: #{{ $reseller->id }})</span>
                        </td>
                        <td>
                            <div class="text-dark font-size-13"><i class="fe-phone me-1 text-muted"></i> {{ $reseller->phone ?? 'N/A' }}</div>
                            <div class="text-muted font-size-12"><i class="fe-mail me-1 text-muted"></i> {{ $reseller->email }}</div>
                        </td>
                        <td>
                            <div class="d-inline-flex gap-1">
                                @if($reseller->voter_id_front)
                                    <div class="doc-thumb-box" title="NID Front" onclick="window.open('{{ asset($reseller->voter_id_front) }}', '_blank')">
                                        <img src="{{ asset($reseller->voter_id_front) }}" alt="NID Front">
                                    </div>
                                @endif
                                @if($reseller->voter_id_back)
                                    <div class="doc-thumb-box" title="NID Back" onclick="window.open('{{ asset($reseller->voter_id_back) }}', '_blank')">
                                        <img src="{{ asset($reseller->voter_id_back) }}" alt="NID Back">
                                    </div>
                                @endif
                                @if($reseller->self_image)
                                    <div class="doc-thumb-box" title="Reseller Photo" onclick="window.open('{{ asset($reseller->self_image) }}', '_blank')">
                                        <img src="{{ asset($reseller->self_image) }}" alt="Selfie">
                                    </div>
                                @endif
                                @if(!$reseller->voter_id_front && !$reseller->voter_id_back && !$reseller->self_image)
                                    <span class="text-muted font-size-12 fst-italic">No Docs</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-dark font-size-13">{{ $reseller->created_at ? $reseller->created_at->format('d M, Y') : 'N/A' }}</div>
                            <span class="text-muted font-size-11">{{ $reseller->created_at ? $reseller->created_at->format('h:i A') : '' }}</span>
                        </td>
                        <td>
                            @if($reseller->verification_status === 'approved')
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-check-circle me-1"></i> Approved
                                </span>
                            @elseif($reseller->verification_status === 'rejected')
                                <span class="badge bg-soft-danger text-danger px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-x-circle me-1"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-clock me-1"></i> Pending Review
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.reseller.verification.show', $reseller->id) }}" class="action-circle-btn btn-action-view" title="Inspect Full KYC Details">
                                    <i class="fe-eye"></i>
                                </a>
                                @if($reseller->verification_status !== 'approved')
                                    <button type="button" class="action-circle-btn btn-action-approve" title="Quick Approve" data-bs-toggle="modal" data-bs-target="#modalApproveReseller{{ $reseller->id }}">
                                        <i class="fe-check"></i>
                                    </button>
                                @endif
                                @if($reseller->verification_status !== 'rejected')
                                    <button type="button" class="action-circle-btn btn-action-reject" title="Quick Reject" data-bs-toggle="modal" data-bs-target="#modalRejectReseller{{ $reseller->id }}">
                                        <i class="fe-x"></i>
                                    </button>
                                @endif
                            </div>

                            {{-- Approve Modal --}}
                            <div class="modal fade text-start" id="modalApproveReseller{{ $reseller->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('admin.reseller.verification.approve', $reseller->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title fw-bold"><i class="fe-check-circle me-1"></i> Approve KYC for {{ $reseller->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="text-muted font-size-13 mb-3">
                                                    Are you sure you want to verify and approve this reseller account?
                                                </p>
                                                <div class="mb-3">
                                                    <label class="form-label font-size-13 fw-semibold">Admin Note (Optional)</label>
                                                    <textarea name="admin_note" class="form-control" rows="3" placeholder="Optional approval remark..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success fw-semibold">Confirm Approval</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Reject Modal --}}
                            <div class="modal fade text-start" id="modalRejectReseller{{ $reseller->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('admin.reseller.verification.reject', $reseller->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold"><i class="fe-x-circle me-1"></i> Reject KYC for {{ $reseller->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label font-size-13 fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                                                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Unclear NID card image, mismatch name..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger fw-semibold">Reject Request</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fe-shield font-size-36 d-block mb-2 text-slate-300"></i>
                            <p class="mb-0 font-size-14 fw-semibold">No reseller KYC verification requests found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($resellers->hasPages() || $resellers->total() > 0)
        <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted font-size-13">
                Showing <strong>{{ $resellers->firstItem() ?? 0 }}</strong> to <strong>{{ $resellers->lastItem() ?? 0 }}</strong> of <strong>{{ $resellers->total() }}</strong> Requests
            </div>
            <div>
                {{ $resellers->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection