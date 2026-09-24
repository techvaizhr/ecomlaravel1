@extends('backEnd.layouts.master')
@section('title', 'Manage Facebook Pixels')

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
    
    .pixel-badge {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        color: #1877f2;
        background: #eef4ff;
        border: 1px solid #c7d9fd;
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

    .catalog-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        transition: all 0.25s ease;
    }
    .catalog-box:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    .feed-input-group .form-control {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.825rem;
        background: #ffffff;
        font-weight: 600;
        color: #1e293b;
    }
    .badge-channel {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .badge-fb { background: #e7f0fd; color: #1877f2; border: 1px solid #c7d9fd; }
    .badge-google { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-csv { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i data-feather="facebook" class="text-primary" style="width: 24px; height: 24px;"></i>
                Facebook & Meta Pixels
            </h4>
            <p class="text-muted small mb-0">Configure client-side tracking pixels to track conversions and page views.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('pixels.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                <span>Add Pixel</span>
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #1877f2 0%, #0d5ec4 100%);">
                <div class="stat-icon"><i class="fab fa-facebook-f"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total Pixels</div>
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

    {{-- PRODUCT CATALOG FEEDS (FACEBOOK & GOOGLE) --}}
    <div class="card card-modern mb-4 border-0" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
        <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #e7f0fd; border-radius: 8px;">
                        <i class="fab fa-facebook text-primary fs-5"></i>
                    </span>
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #fef3c7; border-radius: 8px;">
                        <i class="fab fa-google text-warning fs-5"></i>
                    </span>
                    <h5 class="mb-0 fw-bold text-dark">Facebook & Google Product Catalog Data Feeds</h5>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1 rounded-pill small">Auto-Sync Active</span>
                </div>
                <p class="text-muted small mb-0 mt-1">
                    সঠিক ফরম্যাটের ক্যাটালগ ডাটা ফিড লিংক। ফেসবুক কমার্স ম্যানেজার (Meta Commerce Manager) ও গুগল মার্চেন্ট সেন্টারে এই লিংক দিলেই স্বয়ংক্রিয়ভাবে ক্যাটালগ কানেক্ট ও প্রোডাক্ট সিঙ্ক হয়ে যাবে।
                </p>
            </div>
            <div>
                <a href="#howToConnectGuide" data-bs-toggle="collapse" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1">
                    <i class="fas fa-question-circle"></i>
                    <span>কীভাবে কানেক্ট করবেন?</span>
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            {{-- Image Rule Alert --}}
            <div class="alert alert-info border-0 rounded-3 mb-4 d-flex align-items-center gap-3 py-2 px-3" style="background-color: #e0f2fe; color: #0369a1;">
                <div class="fs-4 text-info"><i class="fas fa-info-circle"></i></div>
                <div class="small">
                    <strong>ইমেজ রুলস (Image Resolution Rules):</strong> ভ্যারিয়েশন প্রোডাক্টের ক্ষেত্রে কালার/সাইজ অনুযায়ী ভ্যারিয়েশনের ছবি দেখাবে (না থাকলে স্বয়ংক্রিয়ভাবে মূল ছবি), এবং সাধারণ (Single) প্রোডাক্টের ক্ষেত্রে মূল ছবি দেখাবে।
                </div>
            </div>

            <div class="row g-3">
                {{-- Facebook XML Feed --}}
                <div class="col-12 col-lg-6">
                    <div class="catalog-box h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fab fa-facebook text-primary fs-5"></i>
                                    <span class="fw-bold text-dark">Facebook Catalog Feed (XML)</span>
                                </div>
                                <span class="badge-channel badge-fb">Recommended for Meta</span>
                            </div>
                            <p class="text-muted small mb-3">
                                Meta Commerce Manager, Facebook Shop, Instagram Shopping & Advantage+ Catalog Ads-এর জন্য নির্ধারিত RSS 2.0 XML ফিড।
                            </p>
                        </div>
                        <div>
                            <div class="input-group input-group-sm feed-input-group mb-2">
                                <input type="text" id="fbXmlFeedUrl" class="form-control" readonly value="{{ route('catalog.facebook.xml') }}">
                                <button type="button" class="btn btn-primary" onclick="copyFeedLink('{{ route('catalog.facebook.xml') }}', 'Facebook XML Feed', this)" title="Copy Link">
                                    <i class="fas fa-copy me-1"></i> Copy
                                </button>
                                <a href="{{ route('catalog.facebook.xml') }}" target="_blank" class="btn btn-outline-secondary feed-dynamic-link" data-path="/feed/facebook-catalog.xml" title="View XML in new tab">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="fas fa-check text-success me-1"></i> ভ্যারিয়েশন ইমেজ ও স্টক সহ ফুল সাপোর্ট</small>
                                <a href="{{ route('catalog.facebook.xml') }}?refresh=1" target="_blank" class="small text-decoration-none text-muted feed-dynamic-link" data-path="/feed/facebook-catalog.xml?refresh=1" title="Force Refresh Cache">
                                    <i class="fas fa-sync-alt fa-xs me-1"></i> Refresh Cache
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Google XML Feed --}}
                <div class="col-12 col-lg-6">
                    <div class="catalog-box h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fab fa-google text-warning fs-5"></i>
                                    <span class="fw-bold text-dark">Google Merchant Feed (XML)</span>
                                </div>
                                <span class="badge-channel badge-google">Google Shopping / PMax</span>
                            </div>
                            <p class="text-muted small mb-3">
                                Google Merchant Center, Performance Max (PMax) ও Google Shopping Ads-এর জন্য ফুল স্পেসিফিকেশন সমর্থিত XML ফিড।
                            </p>
                        </div>
                        <div>
                            <div class="input-group input-group-sm feed-input-group mb-2">
                                <input type="text" id="googleXmlFeedUrl" class="form-control" readonly value="{{ route('catalog.google.xml') }}">
                                <button type="button" class="btn btn-warning text-dark fw-semibold" onclick="copyFeedLink('{{ route('catalog.google.xml') }}', 'Google Merchant Feed', this)" title="Copy Link">
                                    <i class="fas fa-copy me-1"></i> Copy
                                </button>
                                <a href="{{ route('catalog.google.xml') }}" target="_blank" class="btn btn-outline-secondary feed-dynamic-link" data-path="/feed/google-catalog.xml" title="View XML in new tab">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="fas fa-check text-success me-1"></i> Google Merchant Center 100% কমপ্লায়েন্ট</small>
                                <a href="{{ route('catalog.google.xml') }}?refresh=1" target="_blank" class="small text-decoration-none text-muted feed-dynamic-link" data-path="/feed/google-catalog.xml?refresh=1" title="Force Refresh Cache">
                                    <i class="fas fa-sync-alt fa-xs me-1"></i> Refresh Cache
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Facebook CSV Feed --}}
                <div class="col-12">
                    <div class="catalog-box">
                        <div class="row align-items-center g-3">
                            <div class="col-12 col-md-5">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-file-csv text-success fs-5"></i>
                                    <span class="fw-bold text-dark">Facebook Catalog Feed (CSV Format)</span>
                                    <span class="badge-channel badge-csv">Alternative</span>
                                </div>
                                <p class="text-muted small mb-0">
                                    যারা Facebook Commerce Manager-এ Spreadsheet / CSV লিংক হিসেবে ক্যাটালগ যোগ করতে চান তাদের জন্য।
                                </p>
                            </div>
                            <div class="col-12 col-md-7">
                                <div class="input-group input-group-sm feed-input-group">
                                    <input type="text" id="fbCsvFeedUrl" class="form-control" readonly value="{{ route('catalog.facebook.csv') }}">
                                    <button type="button" class="btn btn-outline-dark" onclick="copyFeedLink('{{ route('catalog.facebook.csv') }}', 'Facebook CSV Feed', this)">
                                        <i class="fas fa-copy me-1"></i> Copy CSV Link
                                    </button>
                                    <a href="{{ route('catalog.facebook.csv') }}" download="facebook-catalog.csv" class="btn btn-outline-secondary feed-dynamic-link" data-path="/feed/facebook-catalog.csv">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Collapsible Step-by-Step Guide --}}
            <div class="collapse mt-4" id="howToConnectGuide">
                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-book-open me-2 text-primary"></i>সহজে ক্যাটালগ কানেক্ট করার নিয়মাবলী:</h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="bg-white p-3 rounded-3 border h-100">
                                <h6 class="fw-bold text-primary mb-2"><i class="fab fa-facebook me-1"></i> Facebook Commerce Manager এ কানেক্ট করার নিয়ম:</h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li><a href="https://business.facebook.com/commerce" target="_blank" class="fw-semibold text-primary">business.facebook.com/commerce</a> এ লগইন করুন।</li>
                                    <li>আপনার ক্যাটালগ সিলেক্ট করে বামপাশের মেনু থেকে <strong>Catalog > Data Sources</strong> এ যান।</li>
                                    <li><strong>Data Feed</strong> সিলেক্ট করে Next চাপুন।</li>
                                    <li><strong>"Use a URL"</strong> (Scheduled Feed) সিলেক্ট করে উপরের <strong>Facebook Catalog (XML)</strong> লিংকটি পেস্ট করুন।</li>
                                    <li>Automatic daily upload সময় সিলেক্ট করে <strong>Save & Upload</strong> করুন। ব্যাস, আপনার ক্যাটালগ কানেক্ট হয়ে যাবে!</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="bg-white p-3 rounded-3 border h-100">
                                <h6 class="fw-bold text-warning mb-2"><i class="fab fa-google me-1"></i> Google Merchant Center এ কানেক্ট করার নিয়ম:</h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li><a href="https://merchants.google.com" target="_blank" class="fw-semibold text-warning">merchants.google.com</a> এ লগইন করুন।</li>
                                    <li>বামপাশের মেনু থেকে <strong>Products > Feeds</strong> এ যান।</li>
                                    <li><strong>Primary Feeds</strong> এর নিচে <strong>Add products (Plus icon)</strong> এ ক্লিক করুন।</li>
                                    <li>দেশ ও ভাষা সিলেক্ট করে <strong>Scheduled fetch</strong> অপশন নির্বাচন করুন।</li>
                                    <li>ফিডের একটি নাম দিন এবং উপরের <strong>Google Merchant Feed (XML)</strong> লিংকটি পেস্ট করে সেভ করুন।</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('pixels.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    {{-- Keyword Search --}}
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 14px;"></i></span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control border-start-0" placeholder="Search Pixel ID...">
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
                            <a href="{{ route('pixels.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i data-feather="rotate-ccw" style="width: 14px;"></i></a>
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
                        <th>Pixel ID / Tracking Code</th>
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
                                <div class="d-flex align-items-center gap-2">
                                    <span class="pixel-badge" onclick="copyPixelCode('{{ $value->code }}')" title="Click to copy">
                                        <i data-feather="code" style="width: 14px; height: 14px;"></i>
                                        {{ $value->code }}
                                        <i data-feather="copy" style="width: 12px; height: 12px;" class="text-muted ms-1"></i>
                                    </span>
                                    @if(!empty($value->access_token))
                                        <span class="badge bg-soft-info text-info border border-info border-opacity-25" title="Server-side CAPI enabled">
                                            <i class="fas fa-server me-1"></i> CAPI Active
                                        </span>
                                    @endif
                                </div>
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
                                        <form action="{{ route('pixels.inactive') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn-action-icon text-warning" title="Deactivate Pixel">
                                                <i data-feather="pause-circle" style="width:14px; height:14px;"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('pixels.active') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn-action-icon text-success" title="Activate Pixel">
                                                <i data-feather="check-circle" style="width:14px; height:14px;"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('pixels.edit', $value->id) }}" class="btn-action-icon text-primary" title="Edit Pixel">
                                        <i data-feather="edit-2" style="width:14px; height:14px;"></i>
                                    </a>
                                    
                                    <form action="{{ route('pixels.destroy') }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this pixel?');">
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
                                    <i data-feather="facebook" style="width: 48px; height: 48px; opacity: 0.35;" class="mb-2"></i>
                                    <p class="fw-bold mb-1">No Pixels Found</p>
                                    <small class="text-muted">Add your Meta/Facebook Pixel ID to track customer conversions.</small>
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
    function copyPixelCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            toastr.success('Pixel ID "' + code + '" copied to clipboard!');
        });
    }

    // Dynamic Domain Sync on Page Load
    document.addEventListener('DOMContentLoaded', function () {
        const origin = window.location.origin;

        const fbXmlInput = document.getElementById('fbXmlFeedUrl');
        const googleXmlInput = document.getElementById('googleXmlFeedUrl');
        const fbCsvInput = document.getElementById('fbCsvFeedUrl');

        if (fbXmlInput) fbXmlInput.value = origin + '/feed/facebook-catalog.xml';
        if (googleXmlInput) googleXmlInput.value = origin + '/feed/google-catalog.xml';
        if (fbCsvInput) fbCsvInput.value = origin + '/feed/facebook-catalog.csv';

        // Update all feed view & download links to current dynamic domain
        document.querySelectorAll('.feed-dynamic-link').forEach(function(el) {
            const path = el.getAttribute('data-path');
            if (path) {
                el.href = origin + path;
            }
        });
    });

    function copyFeedLink(url, label, btn) {
        // Guarantee current browser domain is used
        try {
            const parsed = new URL(url, window.location.origin);
            url = window.location.origin + parsed.pathname + parsed.search;
        } catch(e) {}

        function showSuccess() {
            toastr.success(label + ' কপি করা হয়েছে!');
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-white me-1"></i> Copied!';
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                }, 2000);
            }
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(showSuccess).catch(() => fallbackCopy(url, showSuccess));
        } else {
            fallbackCopy(url, showSuccess);
        }
    }

    function fallbackCopy(text, callback) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.opacity = "0";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            if (callback) callback();
        } catch (err) {
            toastr.error('কপি করতে ব্যর্থ হয়েছে, দয়া করে ম্যানুয়ালি কপি করুন।');
        }
        document.body.removeChild(textArea);
    }
</script>
@endpush