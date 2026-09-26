@extends('backEnd.layouts.master')
@section('title', 'Courier API Settings & Store Management')

@section('css')
<link href="{{ asset('public/backEnd/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700&display=swap');

    .courier-hub {
        --ch-surface: #ffffff;
        --ch-border: #e8ecf1;
        --ch-muted: #64748b;
        --ch-text: #0f172a;
        --ch-radius: 14px;
        --ch-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 10px 28px rgba(15, 23, 42, 0.06);
        font-family: 'DM Sans', system-ui, sans-serif;
        color: var(--ch-text);
        letter-spacing: -0.01em;
    }

    .courier-hub-hero {
        background: var(--ch-surface);
        border: 1px solid var(--ch-border);
        border-radius: var(--ch-radius);
        box-shadow: var(--ch-shadow);
        padding: 1.35rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }
    .courier-hub-hero::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #6366f1, #8b5cf6);
        border-radius: 4px 0 0 4px;
    }
    .courier-hub-hero h1 {
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0 0 0.35rem;
    }
    .courier-hub-hero p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--ch-muted);
        max-width: 650px;
        line-height: 1.55;
    }
    .courier-hub-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    .courier-hub-pill {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }
    .courier-hub-pill.soft {
        background: #f8fafc;
        color: var(--ch-muted);
        border-color: var(--ch-border);
    }
    .courier-hub-pill.carrybee-pill {
        background: #fffbeb;
        color: #b45309;
        border-color: #fde68a;
    }

    .courier-panel {
        background: var(--ch-surface);
        border: 1px solid var(--ch-border);
        border-radius: var(--ch-radius);
        box-shadow: var(--ch-shadow);
        height: 100%;
        overflow: hidden;
        transition: box-shadow 0.22s ease, border-color 0.22s ease;
        display: flex;
        flex-direction: column;
    }
    .courier-panel:hover {
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        border-color: #dce3ec;
    }

    .courier-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.25rem;
        border-bottom: 1px solid var(--ch-border);
    }
    .courier-panel-head.steadfast {
        background: linear-gradient(135deg, #fff5f5 0%, #fff 55%);
        border-left: 4px solid #ef4444;
    }
    .courier-panel-head.pathao {
        background: linear-gradient(135deg, #eff6ff 0%, #fff 55%);
        border-left: 4px solid #0ea5e9;
    }
    .courier-panel-head.redx {
        background: linear-gradient(135deg, #fffbeb 0%, #fff 55%);
        border-left: 4px solid #f59e0b;
    }
    .courier-panel-head.carrybee {
        background: linear-gradient(135deg, #fffdf0 0%, #fff 55%);
        border-left: 4px solid #FDB813;
    }

    .courier-panel-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--ch-text);
    }
    .courier-panel-tag {
        display: block;
        font-size: 0.75rem;
        color: var(--ch-muted);
        margin-top: 0.15rem;
        font-weight: 500;
    }

    .courier-logo {
        width: 60px;
        height: 52px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid var(--ch-border);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .courier-logo img, .courier-logo svg {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .courier-panel-body {
        padding: 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .courier-hub .form-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--ch-muted);
        margin-bottom: 0.45rem;
    }

    .courier-hub .form-control, .courier-hub .form-select {
        border: 1px solid var(--ch-border);
        border-radius: 10px;
        padding: 0.65rem 0.85rem;
        font-size: 0.875rem;
        background: #fafbfc;
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    }
    .courier-hub .form-control:focus, .courier-hub .form-select:focus {
        border-color: #6366f1;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .courier-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 10px;
        border: 1px solid var(--ch-border);
        background: linear-gradient(180deg, #fafbfd 0%, #fff 100%);
        margin-bottom: 1rem;
    }
    .courier-status-row span {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--ch-text);
    }
    .courier-hub .form-check-input {
        width: 2.65rem;
        height: 1.35rem;
        cursor: pointer;
    }

    .courier-btn-save {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 0.72rem 1rem;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .courier-btn-save:hover {
        transform: translateY(-1px);
    }
    .courier-btn-save.steadfast {
        background: linear-gradient(145deg, #ef4444, #dc2626);
        color: #fff;
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
    }
    .courier-btn-save.pathao {
        background: linear-gradient(145deg, #0ea5e9, #0284c7);
        color: #fff;
        box-shadow: 0 6px 18px rgba(14, 165, 233, 0.35);
    }
    .courier-btn-save.redx {
        background: linear-gradient(145deg, #f59e0b, #d97706);
        color: #fff;
        box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
    }
    .courier-btn-save.carrybee {
        background: linear-gradient(145deg, #FDB813, #e5a408);
        color: #111827;
        box-shadow: 0 6px 18px rgba(253, 184, 19, 0.35);
    }

    .courier-hub small.form-text,
    .courier-hub .text-muted.small-hint {
        font-size: 0.72rem;
        line-height: 1.45;
    }

    .courier-hub .input-group .btn {
        border-radius: 0 10px 10px 0;
        border-color: var(--ch-border);
    }

    .courier-hub code {
        font-size: 0.72rem;
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        background: #f1f5f9;
        color: #0f172a;
    }

    /* Store Management Styles */
    .store-sync-box {
        margin-top: 1rem;
        padding: 0.85rem;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid var(--ch-border);
    }
    .store-sync-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.6rem;
    }
    .store-sync-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--ch-muted);
        letter-spacing: 0.05em;
    }
    .store-badge {
        font-size: 0.68rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
    }
    .store-item {
        background: #fff;
        border: 1px solid var(--ch-border);
        border-radius: 8px;
        padding: 0.6rem 0.75rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.15s;
    }
    .store-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .store-item.is-default {
        border-color: #10b981;
        background: #f0fdf4;
    }
    .store-name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--ch-text);
    }
    .store-subtext {
        font-size: 0.7rem;
        color: var(--ch-muted);
    }
    .btn-sync-action {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="courier-hub">
    <div class="container-fluid py-4">

        <header class="courier-hub-hero">
            <div>
                <h1>Courier API & Store Management</h1>
                <p>
                    Steadfast, Pathao, RedX এবং Carrybee-এর API কী, সিক্রেট ও পিকআপ স্টোর ম্যানেজমেন্ট। 
                    কুরিয়ার API থেকে স্টোরগুলো সরাসরি ডাটাবেজে সিঙ্ক করে ডিফল্ট স্টোর সেট করতে পারবেন।
                </p>
            </div>
            <div class="courier-hub-meta">
                <span class="courier-hub-pill"><i class="mdi mdi-api"></i> ৪টি কুরিয়ার গেটওয়ে</span>
                <span class="courier-hub-pill carrybee-pill"><i class="mdi mdi-check-decagram"></i> Carrybee ইন্টিগ্রেটেড</span>
                <span class="courier-hub-pill soft"><i class="mdi mdi-store"></i> মাল্টি-স্টোর সাপোর্ট</span>
            </div>
        </header>

        <div class="row g-4">

            {{-- 1. Carrybee --}}
            <div class="col-12 col-md-6 col-lg-6">
                <div class="courier-panel">
                    <div class="courier-panel-head carrybee">
                        <div>
                            <h2 class="courier-panel-title">Carrybee</h2>
                            <span class="courier-panel-tag">V2 API · ওয়েবহুক · স্টোরস</span>
                        </div>
                        <div class="courier-logo" style="background: #fff; padding: 3px;">
                            <img src="{{ asset('public/uploads/default/carrybee.png') }}" alt="Carrybee"
                                 onerror="this.src='{{ asset('public/frontEnd/images/carrybee.png') }}'">
                        </div>
                    </div>
                    <div class="courier-panel-body">
                        <form action="{{ route('courierapi.update') }}" method="POST" data-parsley-validate id="carrybee_form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $carrybee->id ?? '' }}">
                            <input type="hidden" name="type" value="carrybee">

                            <div class="mb-3">
                                <label class="form-label">Environment / Base URL <span class="text-danger">*</span></label>
                                @php
                                    $cbUrl = $carrybee->url ?? 'https://developers.carrybee.com';
                                @endphp
                                <select class="form-select" name="url" required>
                                    <option value="https://developers.carrybee.com" {{ str_contains($cbUrl, 'developers.carrybee') ? 'selected' : '' }}>Production (https://developers.carrybee.com)</option>
                                    <option value="https://sandbox.carrybee.com" {{ str_contains($cbUrl, 'sandbox.carrybee') ? 'selected' : '' }}>Sandbox (https://sandbox.carrybee.com)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client-ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="client_id"
                                       value="{{ $carrybee->client_id ?? '' }}" placeholder="Carrybee Client ID" required autocomplete="off" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client-Secret <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="client_secret"
                                       value="{{ $carrybee->client_secret ?? ($carrybee->secret_key ?? '') }}" placeholder="••••••••" required autocomplete="new-password" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client-Context <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="client_context"
                                       value="{{ $carrybee->client_context ?? '' }}" placeholder="Carrybee Client Context" required autocomplete="off" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Webhook URL <small class="text-muted fw-normal">(Carrybee পোর্টালে দিন)</small></label>
                                @php
                                    $cbWebhookUrl = config('app.url') . '/webhooks/carrybee?token=' . ($carrybee->webhook_secret ?? '40489fe0-9386-4fc9-8e92-2b2fcb9d451c');
                                @endphp
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cb_webhook_display" value="{{ $cbWebhookUrl }}" readonly />
                                    <button type="button" class="btn btn-outline-secondary copy-btn" data-clipboard-target="#cb_webhook_display" title="কপি">
                                        <i class="fe-copy"></i>
                                    </button>
                                </div>
                                <small class="text-muted small-hint d-block mt-1">Header <code>X-CB-Webhook-Integration-Header</code> সাপোর্ট করে।</small>
                            </div>

                            <div class="courier-status-row">
                                <span><i class="fas fa-power-off text-muted me-1"></i> সার্ভিস চালু</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                           @if(isset($carrybee) && $carrybee->status==1) checked @endif>
                                </div>
                            </div>

                            <button type="submit" class="courier-btn-save carrybee mb-3">
                                <i class="fe-save"></i> সংরক্ষণ করুন
                            </button>
                        </form>

                        {{-- Carrybee Pickup Stores Box --}}
                        <div class="store-sync-box">
                            <div class="store-sync-header">
                                <span class="store-sync-title"><i class="fe-map-pin me-1"></i> পিকআপ স্টোর ({{ $carrybee_stores->count() }})</span>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-sync-action sync-stores-btn" data-courier="carrybee">
                                    <i class="fe-refresh-cw me-1"></i> সিঙ্ক / রিচেক
                                </button>
                            </div>

                            <div class="store-list-container" id="carrybee_store_list">
                                @forelse($carrybee_stores as $st)
                                    <div class="store-item {{ $st->is_default ? 'is-default' : '' }}">
                                        <div>
                                            <div class="store-name">
                                                {{ $st->store_name }}
                                                @if($st->is_default)
                                                    <span class="badge bg-success store-badge ms-1">ডিফল্ট স্টোর</span>
                                                @endif
                                            </div>
                                            <div class="store-subtext">{{ Str::limit($st->address ?: 'ID: '.$st->store_id, 35) }}</div>
                                        </div>
                                        <div>
                                            @if(!$st->is_default)
                                                <button type="button" class="btn btn-xs btn-outline-success set-default-store-btn" data-courier="carrybee" data-store-id="{{ $st->store_id }}" title="ডিফল্ট করুন">
                                                    ডিফল্ট
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-2 text-muted small">কোনো স্টোর নেই। "সিঙ্ক / রিচেক" বাটনে ক্লিক করুন।</div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 2. Steadfast --}}
            <div class="col-12 col-md-6 col-lg-6">
                <div class="courier-panel">
                    <div class="courier-panel-head steadfast">
                        <div>
                            <h2 class="courier-panel-title">Steadfast</h2>
                            <span class="courier-panel-tag">API · ওয়েবহুক · ব্যালান্স</span>
                        </div>
                        <div class="courier-logo">
                            <img src="{{ asset('public/frontEnd/images/stade.svg') }}" alt="Steadfast">
                        </div>
                    </div>
                    <div class="courier-panel-body">
                        <form action="{{ route('courierapi.update') }}" method="POST" data-parsley-validate>
                            @csrf
                            <input type="hidden" name="id" value="{{ $steadfast->id ?? '' }}">
                            <input type="hidden" name="type" value="steadfast">

                            <div class="mb-3">
                                <label class="form-label">API Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="api_key" value="{{ $steadfast->api_key ?? '' }}" required autocomplete="off" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="secret_key" value="{{ $steadfast->secret_key ?? '' }}" required autocomplete="off" />
                            </div>

                            @include('backEnd.apiintegration.partials.steadfast_webhook_fields')

                            <div class="courier-status-row">
                                <span><i class="fas fa-power-off text-muted me-1"></i> সার্ভিস চালু</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                           @if(isset($steadfast) && $steadfast->status==1) checked @endif>
                                </div>
                            </div>

                            <button type="submit" class="courier-btn-save steadfast mb-3">
                                <i class="fas fa-save"></i> সংরক্ষণ করুন
                            </button>
                        </form>

                        {{-- Steadfast Stores Box --}}
                        <div class="store-sync-box">
                            <div class="store-sync-header">
                                <span class="store-sync-title"><i class="fe-map-pin me-1"></i> পিকআপ লোকেশন ({{ $steadfast_stores->count() }})</span>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-danger btn-sync-action sync-stores-btn" data-courier="steadfast" title="Steadfast ব্যালান্স ও কানেকশন রিচেক">
                                        <i class="fe-refresh-cw me-1"></i> সিঙ্ক / রিচেক
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-sync-action" data-bs-toggle="modal" data-bs-target="#addCustomStoreModal" data-courier="steadfast">
                                        <i class="fe-plus me-1"></i> নতুন স্টোর
                                    </button>
                                </div>
                            </div>

                            <div class="store-list-container" id="steadfast_store_list">
                                @forelse($steadfast_stores as $st)
                                    <div class="store-item {{ $st->is_default ? 'is-default' : '' }}">
                                        <div>
                                            <div class="store-name">
                                                {{ $st->store_name }}
                                                @if($st->is_default)
                                                    <span class="badge bg-success store-badge ms-1">ডিফল্ট</span>
                                                @endif
                                            </div>
                                            <div class="store-subtext">{{ Str::limit($st->address, 35) }}</div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            @if(!$st->is_default)
                                                <button type="button" class="btn btn-xs btn-outline-success set-default-store-btn" data-courier="steadfast" data-store-id="{{ $st->id }}">
                                                    ডিফল্ট
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-xs btn-outline-danger delete-store-btn" data-id="{{ $st->id }}">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-2 text-muted small">স্টেডফাস্ট মার্চেন্ট প্রোফাইল ডিফল্ট লোকেশন ব্যবহার করে।</div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 3. Pathao --}}
            <div class="col-12 col-md-6 col-lg-6">
                <div class="courier-panel">
                    <div class="courier-panel-head pathao">
                        <div>
                            <h2 class="courier-panel-title">Pathao Courier</h2>
                            <span class="courier-panel-tag">Hermes API · OAuth 2.0 · ওয়েবহুক · স্টোরস</span>
                        </div>
                        <div class="courier-logo" style="background: #fff; padding: 4px;">
                            <img src="{{ asset('public/uploads/default/pathao.png') }}" alt="Pathao"
                                 onerror="this.src='{{ asset('public/frontEnd/images/pathao.png') }}'">
                        </div>
                    </div>
                    <div class="courier-panel-body">
                        <form action="{{ route('courierapi.update') }}" method="POST" data-parsley-validate id="pathao_form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $pathao->id ?? '' }}">
                            <input type="hidden" name="type" value="pathao">

                            <div class="mb-3">
                                <label class="form-label">Environment / Base URL <span class="text-danger">*</span></label>
                                @php
                                    $pathaoUrl = $pathao->url ?? 'https://api-hermes.pathao.com';
                                @endphp
                                <select class="form-select" name="url" id="pathao_api_url" required>
                                    <option value="https://api-hermes.pathao.com" {{ str_contains($pathaoUrl, 'api-hermes') ? 'selected' : '' }}>Production (https://api-hermes.pathao.com)</option>
                                    <option value="https://courier-api-sandbox.pathao.com" {{ str_contains($pathaoUrl, 'sandbox') ? 'selected' : '' }}>Sandbox (https://courier-api-sandbox.pathao.com)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="client_id" id="pathao_client_id" value="{{ $pathao->client_id ?? '' }}" placeholder="Ex - 100" required autocomplete="off" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client Secret <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="client_secret" id="pathao_client_secret" value="{{ $pathao->client_secret ?? '' }}" placeholder="••••••••" required autocomplete="new-password" />
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Username / Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="username" id="pathao_username" value="{{ $pathao->username ?? '' }}" placeholder="login email" required autocomplete="username" />
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" id="pathao_password" value="{{ $pathao->password ?? '' }}" placeholder="password" required autocomplete="new-password" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">Access Token <small class="text-muted fw-normal">(OAuth 2.0 Bearer)</small></label>
                                    <small class="text-primary fw-semibold" id="pathao_token_expiry_hint"></small>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="token" value="{{ $pathao->token ?? '' }}" id="pathao_token_display" placeholder="Bearer টোকেন লিখুন অথবা জেনারেট করুন" autocomplete="off" />
                                    <button type="button" class="btn btn-outline-primary" id="generate_pathao_token" title="স্বয়ংক্রিয়ভাবে নতুন টোকেন জেনারেট করুন">
                                        <i class="fe-refresh-cw me-1"></i> জেনারেট
                                    </button>
                                </div>
                                <small class="text-muted small-hint d-block mt-1">আপনি সরাসরি টোকেন লিখে সংরক্ষণ করতে পারেন অথবা "জেনারেট" বাটনে ক্লিক করে স্বয়ংক্রিয়ভাবে নিতে পারেন।</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">Callback URL <small class="text-muted fw-normal">(পাঠাও ড্যাশবোর্ডে দিন)</small></label>
                                    <span class="badge bg-light text-primary border" style="font-size: 0.72rem;"><i class="fe-link"></i> Webhook Callback</span>
                                </div>
                                @php
                                    $pathaoCallbackUrl = rtrim(config('app.url'), '/') . '/webhooks/pathao';
                                @endphp
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pathao_callback_url" value="{{ $pathaoCallbackUrl }}" readonly />
                                    <button type="button" class="btn btn-outline-secondary copy-btn" data-clipboard-target="#pathao_callback_url" title="কপি করুন">
                                        <i class="fe-copy"></i>
                                    </button>
                                </div>
                                <small class="text-muted small-hint d-block mt-1">
                                    Pathao Developer Portal-এ <code>Callback URL</code> এ এই লিংক দিন।
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Webhook Secret <small class="text-muted fw-normal">(পাঠাও সিক্রেট কি)</small></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="webhook_secret" id="pathao_webhook_secret"
                                           value="{{ $pathao->webhook_secret ?? 'f3992ecc-59da-4cbe-a049-a13da2018d51' }}"
                                           placeholder="f3992ecc-59da-4cbe-a049-a13da2018d51" autocomplete="off" />
                                    <button type="button" class="btn btn-outline-secondary" id="generate_pathao_secret" title="নতুন সিক্রেট তৈরি">
                                        <i class="fe-refresh-cw"></i>
                                    </button>
                                </div>
                                <small class="text-muted small-hint d-block mt-1">
                                    Pathao Webhook সেটাপে দেওয়া Secret Key এখানে রাখুন।
                                </small>
                            </div>

                            <div class="courier-status-row">
                                <span><i class="fas fa-power-off text-muted me-1"></i> সার্ভিস চালু</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                           @if(isset($pathao) && $pathao->status==1) checked @endif>
                                </div>
                            </div>

                            <button type="submit" class="courier-btn-save pathao mb-3">
                                <i class="fe-save"></i> সংরক্ষণ করুন
                            </button>
                        </form>

                        {{-- Pathao Pickup Stores Box --}}
                        <div class="store-sync-box">
                            <div class="store-sync-header">
                                <span class="store-sync-title"><i class="fe-map-pin me-1"></i> পাঠাও স্টোর ({{ $pathao_stores->count() }})</span>
                                <button type="button" class="btn btn-xs btn-outline-info btn-sync-action sync-stores-btn" data-courier="pathao">
                                    <i class="fe-refresh-cw me-1"></i> সিঙ্ক / রিচেক
                                </button>
                            </div>

                            <div class="store-list-container" id="pathao_store_list">
                                @forelse($pathao_stores as $st)
                                    <div class="store-item {{ $st->is_default ? 'is-default' : '' }}">
                                        <div>
                                            <div class="store-name">
                                                {{ $st->store_name }}
                                                @if($st->is_default)
                                                    <span class="badge bg-success store-badge ms-1">ডিফল্ট স্টোর</span>
                                                @endif
                                            </div>
                                            <div class="store-subtext">{{ Str::limit($st->address ?: 'ID: '.$st->store_id, 35) }}</div>
                                        </div>
                                        <div>
                                            @if(!$st->is_default)
                                                <button type="button" class="btn btn-xs btn-outline-success set-default-store-btn" data-courier="pathao" data-store-id="{{ $st->store_id }}">
                                                    ডিফল্ট
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-2 text-muted small">কোনো স্টোর নেই। "সিঙ্ক / রিচেক" বাটনে ক্লিক করুন।</div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 4. RedX --}}
            <div class="col-12 col-md-6 col-lg-6">
                <div class="courier-panel">
                    <div class="courier-panel-head redx">
                        <div>
                            <h2 class="courier-panel-title">RedX Courier</h2>
                            <span class="courier-panel-tag">OpenAPI · ওয়েবহুক · স্টোরস</span>
                        </div>
                        <div class="courier-logo">
                            <img src="https://redx.com.bd/images/logo.png" alt="RedX"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext x=%2250%22 y=%2255%22 font-size=%2236%22 text-anchor=%22middle%22 fill=%22%23f59e0b%22%3ERX%3C/text%3E%3C/svg%3E'">
                        </div>
                    </div>
                    <div class="courier-panel-body">
                        <form action="{{ route('courierapi.update') }}" method="POST" data-parsley-validate>
                            @csrf
                            <input type="hidden" name="id" value="{{ $redx->id ?? '' }}">
                            <input type="hidden" name="type" value="redx">

                            <div class="mb-3">
                                <label class="form-label">Base URL <span class="text-danger">*</span></label>
                                @php
                                    $currentUrl = $redx->url ?? '';
                                    $currentUrlNormalized = preg_replace('/^https?:\/\//', '', $currentUrl);
                                    $currentUrlNormalized = rtrim($currentUrlNormalized, '/');
                                @endphp
                                <select class="form-select" name="url" id="redx_url" required>
                                    <option value="https://openapi.redx.com.bd/v1.0.0-beta" {{ str_contains($currentUrlNormalized, 'openapi.redx.com.bd') ? 'selected' : '' }}>Production (লাইভ)</option>
                                    <option value="https://sandbox.redx.com.bd/v1.0.0-beta" {{ str_contains($currentUrlNormalized, 'sandbox.redx.com.bd') ? 'selected' : '' }}>Sandbox (টেস্টিং)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">API Access Token <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="token" value="{{ $redx->token ?? '' }}"
                                       placeholder="Bearer ছাড়া শুধু টোকেন" required autocomplete="off" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Webhook URL <small class="text-muted fw-normal">(ঐচ্ছিক)</small></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="webhook_url" id="redx_webhook_url"
                                           value="{{ $redx->webhook_url ?? (config('app.url').'/api/redx/webhook') }}" autocomplete="off" />
                                    <button type="button" class="btn btn-outline-secondary copy-btn" data-clipboard-target="#redx_webhook_url" title="কপি">
                                        <i class="fe-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="courier-status-row">
                                <span><i class="fas fa-power-off text-muted me-1"></i> সার্ভিস চালু</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                           @if(isset($redx) && $redx->status==1) checked @endif>
                                </div>
                            </div>

                            <button type="submit" class="courier-btn-save redx mb-3">
                                <i class="fas fa-save"></i> সংরক্ষণ করুন
                            </button>
                        </form>

                        {{-- RedX Pickup Stores Box --}}
                        <div class="store-sync-box">
                            <div class="store-sync-header">
                                <span class="store-sync-title"><i class="fe-map-pin me-1"></i> RedX স্টোর ({{ $redx_stores->count() }})</span>
                                <button type="button" class="btn btn-xs btn-outline-warning btn-sync-action sync-stores-btn" data-courier="redx">
                                    <i class="fe-refresh-cw me-1"></i> সিঙ্ক / রিচেক
                                </button>
                            </div>

                            <div class="store-list-container" id="redx_store_list">
                                @forelse($redx_stores as $st)
                                    <div class="store-item {{ $st->is_default ? 'is-default' : '' }}">
                                        <div>
                                            <div class="store-name">
                                                {{ $st->store_name }}
                                                @if($st->is_default)
                                                    <span class="badge bg-success store-badge ms-1">ডিফল্ট স্টোর</span>
                                                @endif
                                            </div>
                                            <div class="store-subtext">{{ Str::limit($st->address ?: 'ID: '.$st->store_id, 35) }}</div>
                                        </div>
                                        <div>
                                            @if(!$st->is_default)
                                                <button type="button" class="btn btn-xs btn-outline-success set-default-store-btn" data-courier="redx" data-store-id="{{ $st->store_id }}">
                                                    ডিফল্ট
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-2 text-muted small">কোনো স্টোর নেই। "সিঙ্ক / রিচেক" বাটনে ক্লিক করুন।</div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Add Custom / Manual Store Modal --}}
<div class="modal fade" id="addCustomStoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe-map-pin me-1"></i> নতুন পিকআপ স্টোর যোগ করুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.courierapi.save_store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">কুরিয়ার টাইপ <span class="text-danger">*</span></label>
                        <select class="form-select" name="courier_type" id="modal_courier_type" required>
                            <option value="steadfast">Steadfast</option>
                            <option value="carrybee">Carrybee</option>
                            <option value="pathao">Pathao</option>
                            <option value="redx">RedX</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">স্টোরের নাম <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="store_name" placeholder="যেমন: ধানমন্ডি হাব / মেইন ওয়্যারহাউস" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">মোবাইল নাম্বার</label>
                        <input type="text" class="form-control" name="contact_person_number" placeholder="017xxxxxxxx" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">সম্পূর্ণ ঠিকানা</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="বাড়ি, রোড, এলাকা, জেলা"></textarea>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="store_is_default">
                        <label class="form-check-label fw-semibold" for="store_is_default">এই কুরিয়ারের ডিফল্ট স্টোর হিসেবে নির্ধারণ করুন</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-primary"><i class="fe-save me-1"></i> সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('public/backEnd/assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('public/backEnd/assets/js/pages/form-validation.init.js') }}"></script>
<script src="{{ asset('public/backEnd/assets/libs/select2/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $(".select2").select2();

        // Pathao Token Generation
        $('#generate_pathao_token').on('click', function(){
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fe-loader fa-spin"></i> জেনারেট হচ্ছে...');

            var formData = {
                _token: "{{ csrf_token() }}",
                url: $('#pathao_api_url').val(),
                client_id: $('#pathao_client_id').val(),
                client_secret: $('#pathao_client_secret').val(),
                username: $('#pathao_username').val(),
                password: $('#pathao_password').val()
            };

            $.ajax({
                url: "{{ route('admin.courierapi.pathao.generate_token') }}",
                type: "POST",
                data: formData,
                success: function(res){
                    if(res.status === 'success' && res.token){
                        $('#pathao_token_display').val(res.token);
                        if(res.expiry_info){
                            $('#pathao_token_expiry_hint').text('মেয়াদ: ' + res.expiry_info);
                        }
                        toastr.success(res.message || 'টোকেন তৈরি হয়েছে!', 'Pathao');
                    } else {
                        toastr.error(res.message || 'টোকেন তৈরি ব্যর্থ');
                    }
                    $btn.prop('disabled', false).html(originalHtml);
                },
                error: function(xhr){
                    toastr.error(xhr.responseJSON?.message || 'টোকেন তৈরিতে ত্রুটি হয়েছে');
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Generate Pathao Webhook Secret
        $('#generate_pathao_secret').on('click', function(){
            var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
            $('#pathao_webhook_secret').val(uuid);
            toastr.info('নতুন Pathao Webhook Secret তৈরি হয়েছে! সেটিং সংরক্ষণ করুন।');
        });

        // Generate Random Steadfast Webhook Secret Token
        $('#generate_steadfast_webhook_token').on('click', function(){
            var randToken = 'sf_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
            $('#steadfast_webhook_token').val(randToken);
            toastr.info('নতুন সিক্রেট টোকেন তৈরি হয়েছে! সংরক্ষণ করুন এবং Steadfast পোর্টালে দিন।');
        });

        // Store Sync / Recheck Handler
        $('.sync-stores-btn').on('click', function(){
            var $btn = $(this);
            var courierType = $btn.data('courier');
            var originalHtml = $btn.html();

            $btn.prop('disabled', true).html('<i class="fe-loader fa-spin"></i> সিঙ্ক হচ্ছে...');

            $.ajax({
                url: "{{ url('admin/courierapi/sync-stores') }}/" + courierType,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res){
                    $btn.prop('disabled', false).html(originalHtml);
                    if(res.success){
                        toastr.success(res.message || 'স্টোর সফলভাবে সিঙ্ক হয়েছে!', 'সফল');
                        renderStoresList(courierType, res.stores);
                    } else {
                        toastr.error(res.message || 'স্টোর সিঙ্ক ব্যর্থ হয়েছে', 'ত্রুটি');
                    }
                },
                error: function(xhr){
                    $btn.prop('disabled', false).html(originalHtml);
                    toastr.error(xhr.responseJSON?.message || 'সার্ভার ত্রুটি হয়েছে', 'ত্রুটি');
                }
            });
        });

        // Set Default Store Handler
        $(document).on('click', '.set-default-store-btn', function(){
            var $btn = $(this);
            var courierType = $btn.data('courier');
            var storeId = $btn.data('store-id');

            $btn.prop('disabled', true).html('...');

            $.ajax({
                url: "{{ route('admin.courierapi.set_default_store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    courier_type: courierType,
                    store_id: storeId
                },
                success: function(res){
                    if(res.success){
                        toastr.success(res.message, 'ডিফল্ট স্টোর');
                        setTimeout(function(){ location.reload(); }, 600);
                    } else {
                        toastr.error(res.message || 'ব্যর্থ হয়েছে');
                        $btn.prop('disabled', false).html('ডিফল্ট');
                    }
                },
                error: function(){
                    toastr.error('সার্ভার ত্রুটি');
                    $btn.prop('disabled', false).html('ডিফল্ট');
                }
            });
        });

        // Delete Store Handler
        $(document).on('click', '.delete-store-btn', function(){
            if(!confirm('আপনি কি এই স্টোরটি ডিলিট করতে চান?')) return;
            var $btn = $(this);
            var id = $btn.data('id');

            $.ajax({
                url: "{{ route('admin.courierapi.delete_store') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", id: id },
                success: function(res){
                    if(res.success){
                        toastr.success(res.message);
                        $btn.closest('.store-item').remove();
                    } else {
                        toastr.error(res.message);
                    }
                }
            });
        });

        // Helper to render stores dynamically
        function renderStoresList(courier, stores){
            var $container = $('#' + courier + '_store_list');
            if(!stores || stores.length === 0){
                $container.html('<div class="text-center py-2 text-muted small">কোনো স্টোর পাওয়া যায়নি।</div>');
                return;
            }

            var html = '';
            stores.forEach(function(st){
                var isDef = st.is_default ? true : false;
                html += '<div class="store-item ' + (isDef ? 'is-default' : '') + '">';
                html += '  <div>';
                html += '    <div class="store-name">' + (st.store_name || 'Store') + (isDef ? ' <span class="badge bg-success store-badge ms-1">ডিফল্ট স্টোর</span>' : '') + '</div>';
                html += '    <div class="store-subtext">' + (st.address ? st.address.substring(0, 35) : 'ID: ' + st.store_id) + '</div>';
                html += '  </div>';
                html += '  <div>';
                if(!isDef){
                    html += '    <button type="button" class="btn btn-xs btn-outline-success set-default-store-btn" data-courier="' + courier + '" data-store-id="' + st.store_id + '">ডিফল্ট</button>';
                }
                html += '  </div>';
                html += '</div>';
            });
            $container.html(html);
        }

        // Copy buttons
        $('.copy-btn').on('click', function(){
            var target = $(this).data('clipboard-target');
            var val = $(target).val();
            if(val){
                navigator.clipboard.writeText(val).then(function(){
                    toastr.success('URL কপি করা হয়েছে!');
                });
            }
        });

        $('#addCustomStoreModal').on('show.bs.modal', function(e){
            var courier = $(e.relatedTarget).data('courier');
            if(courier){
                $('#modal_courier_type').val(courier);
            }
        });
    });
</script>
@endsection
