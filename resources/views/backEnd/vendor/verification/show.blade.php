@extends('backEnd.layouts.master')
@section('title', 'Vendor KYC Verification Details')

@section('css')
<style>
    .kyc-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .kyc-card-header {
        padding: 16px 20px;
        background: #fafbfe;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .kyc-card-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 15px;
        color: #0f172a;
    }
    .kyc-doc-card {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        transition: all 0.2s;
    }
    .kyc-doc-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }
    .kyc-doc-img {
        max-height: 280px;
        width: 100%;
        object-fit: contain;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .kyc-doc-img:hover {
        transform: scale(1.02);
    }
    .info-row-kyc {
        display: flex;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
    }
    .info-row-kyc:last-child { border-bottom: none; }
    .info-row-kyc .label { color: #64748b; font-weight: 500; }
    .info-row-kyc .val { color: #0f172a; font-weight: 600; text-align: right; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- BREADCRUMB & ACTIONS --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h4 class="page-title mb-1" style="font-weight: 700; color: #0f172a;">
                <i class="fe-shield text-primary me-2"></i> KYC Verification: {{ $vendor->shop_name }}
            </h4>
            <p class="text-muted font-size-13 mb-0">Review submitted identity documents and manage approval.</p>
        </div>
        <div class="col-md-6 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.vendor.verification.index') }}" class="btn btn-light rounded-pill border px-3 shadow-sm font-size-13">
                <i class="fe-arrow-left me-1"></i> Back to Requests
            </a>
            <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm font-size-13">
                <i class="fe-edit me-1"></i> Edit Vendor Info
            </a>
        </div>
    </div>

    <div class="row g-3">
        {{-- LEFT COLUMN: VENDOR INFO & STATUS --}}
        <div class="col-lg-4">
            {{-- Status Card --}}
            <div class="kyc-card">
                <div class="kyc-card-header">
                    <i class="fe-info text-info font-size-18"></i>
                    <h5>Verification Status</h5>
                </div>
                <div class="p-3 text-center">
                    <div class="mb-3">
                        @if($vendor->verification_status == 'approved')
                            <span class="badge bg-success rounded-pill px-3 py-2 font-size-14">
                                <i class="fe-check-circle me-1"></i> Verified & Approved
                            </span>
                        @elseif($vendor->verification_status == 'rejected')
                            <span class="badge bg-danger rounded-pill px-3 py-2 font-size-14">
                                <i class="fe-x-circle me-1"></i> KYC Rejected
                            </span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 font-size-14">
                                <i class="fe-clock me-1"></i> Pending Verification
                            </span>
                        @endif
                    </div>

                    @if($vendor->verified_at)
                        <div class="text-muted font-size-12 mb-2">
                            Verified At: <strong>{{ \Carbon\Carbon::parse($vendor->verified_at)->format('d M, Y h:i A') }}</strong>
                        </div>
                    @endif

                    @if($vendor->verification_note)
                        <div class="alert alert-light border text-start font-size-13 mt-2 p-2">
                            <strong>Note / Reason:</strong>
                            <div>{{ $vendor->verification_note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vendor Details Card --}}
            <div class="kyc-card">
                <div class="kyc-card-header">
                    <i class="fe-user text-primary font-size-18"></i>
                    <h5>Vendor Profile</h5>
                </div>
                <div class="p-3">
                    <div class="info-row-kyc">
                        <span class="label">Shop Name:</span>
                        <span class="val">{{ $vendor->shop_name }}</span>
                    </div>
                    <div class="info-row-kyc">
                        <span class="label">Owner Name:</span>
                        <span class="val">{{ $vendor->owner_name }}</span>
                    </div>
                    <div class="info-row-kyc">
                        <span class="label">Phone:</span>
                        <span class="val"><a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a></span>
                    </div>
                    <div class="info-row-kyc">
                        <span class="label">Email:</span>
                        <span class="val">{{ $vendor->email }}</span>
                    </div>
                    <div class="info-row-kyc">
                        <span class="label">Address:</span>
                        <span class="val">{{ $vendor->address ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row-kyc">
                        <span class="label">Commission:</span>
                        <span class="val">{{ $vendor->commission_rate }}%</span>
                    </div>
                    @if($vendor->wallet)
                    <div class="info-row-kyc">
                        <span class="label">Wallet Balance:</span>
                        <span class="val text-success">৳{{ number_format($vendor->wallet->balance, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: DOCUMENTS & ACTIONS --}}
        <div class="col-lg-8">
            {{-- Documents Preview --}}
            <div class="kyc-card">
                <div class="kyc-card-header">
                    <i class="fe-file-text text-success font-size-18"></i>
                    <h5>Uploaded Identification Documents</h5>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        {{-- Voter ID Front --}}
                        <div class="col-md-4">
                            <div class="kyc-doc-card">
                                <h6 class="fw-bold font-size-13 text-dark mb-2">Voter ID / NID (Front)</h6>
                                @if($vendor->voter_id_front)
                                    <img src="{{ asset($vendor->voter_id_front) }}" alt="NID Front" class="kyc-doc-img mb-2" onclick="window.open('{{ asset($vendor->voter_id_front) }}', '_blank')">
                                    <a href="{{ asset($vendor->voter_id_front) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                        <i class="fe-maximize-2 me-1"></i> View Full
                                    </a>
                                @else
                                    <div class="alert alert-warning font-size-12 mb-0">Not Uploaded</div>
                                @endif
                            </div>
                        </div>

                        {{-- Voter ID Back --}}
                        <div class="col-md-4">
                            <div class="kyc-doc-card">
                                <h6 class="fw-bold font-size-13 text-dark mb-2">Voter ID / NID (Back)</h6>
                                @if($vendor->voter_id_back)
                                    <img src="{{ asset($vendor->voter_id_back) }}" alt="NID Back" class="kyc-doc-img mb-2" onclick="window.open('{{ asset($vendor->voter_id_back) }}', '_blank')">
                                    <a href="{{ asset($vendor->voter_id_back) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                        <i class="fe-maximize-2 me-1"></i> View Full
                                    </a>
                                @else
                                    <div class="alert alert-warning font-size-12 mb-0">Not Uploaded</div>
                                @endif
                            </div>
                        </div>

                        {{-- Self Image --}}
                        <div class="col-md-4">
                            <div class="kyc-doc-card">
                                <h6 class="fw-bold font-size-13 text-dark mb-2">Selfie / Photo</h6>
                                @if($vendor->self_image)
                                    <img src="{{ asset($vendor->self_image) }}" alt="Self Photo" class="kyc-doc-img mb-2" onclick="window.open('{{ asset($vendor->self_image) }}', '_blank')">
                                    <a href="{{ asset($vendor->self_image) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                        <i class="fe-maximize-2 me-1"></i> View Full
                                    </a>
                                @else
                                    <div class="alert alert-warning font-size-12 mb-0">Not Uploaded</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Forms --}}
            <div class="kyc-card">
                <div class="kyc-card-header">
                    <i class="fe-check-square text-warning font-size-18"></i>
                    <h5>Decision & Actions</h5>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light h-100">
                                <h6 class="text-success fw-bold mb-2"><i class="fe-check-circle me-1"></i> Approve Verification</h6>
                                <form action="{{ route('admin.vendor.verification.approve', $vendor->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-size-12 fw-semibold">Admin Note (Optional)</label>
                                        <textarea name="admin_note" class="form-control" rows="2" placeholder="e.g. Valid NID and trade documents verified."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill" onclick="return confirm('Confirm approval for this vendor KYC?')">
                                        <i class="fe-check me-1"></i> Approve KYC
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light h-100">
                                <h6 class="text-danger fw-bold mb-2"><i class="fe-x-circle me-1"></i> Reject Verification</h6>
                                <form action="{{ route('admin.vendor.verification.reject', $vendor->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-size-12 fw-semibold">Rejection Reason <span class="text-danger">*</span></label>
                                        <textarea name="rejection_reason" class="form-control" rows="2" required placeholder="e.g. Documents are blurry or invalid..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill" onclick="return confirm('Confirm rejection of this vendor KYC?')">
                                        <i class="fe-x me-1"></i> Reject Request
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
