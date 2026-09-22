@extends('backEnd.layouts.master')
@section('title', 'Order Restriction Settings')

@section('css')
<style>
    :root {
        --res-primary: #4f46e5;
        --res-primary-light: #eef2ff;
        --res-border: #e2e8f0;
        --res-card-bg: #ffffff;
        --res-text: #1e293b;
        --res-muted: #64748b;
    }

    .restriction-page-shell {
        padding: 24px 16px 40px;
        background-color: #f8fafc;
        min-height: 100vh;
    }

    /* Page Header */
    .res-header-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        border-radius: 16px;
        padding: 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(49, 46, 129, 0.25);
        position: relative;
        overflow: hidden;
    }
    .res-header-card::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(165, 180, 252, 0.3) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Cards */
    .res-card {
        background: #ffffff;
        border: 1px solid var(--res-border);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .res-card-header {
        padding: 18px 24px;
        background: #ffffff;
        border-bottom: 1px solid var(--res-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .res-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--res-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .res-card-body {
        padding: 24px;
    }

    /* Input styling */
    .res-form-label {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--res-text);
        margin-bottom: 8px;
    }
    .res-input-group {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        transition: all 0.2s;
    }
    .res-input-group:focus-within {
        border-color: var(--res-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
    .res-input-group input {
        border: none !important;
        padding: 12px 16px;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--res-text);
    }
    .res-input-group input:focus {
        box-shadow: none !important;
        outline: none !important;
    }
    .res-input-group .input-group-text {
        border: none !important;
        background-color: #f1f5f9;
        font-weight: 600;
        color: #475569;
        padding: 0 18px;
    }

    /* Preset Badges */
    .preset-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s;
        border: 1px solid #e2e8f0;
    }
    .preset-chip:hover {
        background: var(--res-primary-light);
        color: var(--res-primary);
        border-color: #c7d2fe;
    }

    /* Live Preview Box */
    .live-preview-box {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
        border: 1px solid #e0e7ff;
        border-radius: 12px;
        padding: 18px 20px;
        margin-top: 20px;
    }

    /* Step Item */
    .flow-step-item {
        display: flex;
        gap: 14px;
        margin-bottom: 16px;
    }
    .flow-step-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--res-primary-light);
        color: var(--res-primary);
        font-weight: 700;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid restriction-page-shell">

    {{-- 1. Header Banner --}}
    <div class="res-header-card">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h3 fw-bold mb-2 text-white d-flex align-items-center gap-2">
                    <i class="fas fa-shield-alt text-warning"></i>
                    Order Restriction & Anti-Spam Limits
                </h1>
                <p class="text-white-50 mb-0">
                    Prevent automated spam bots, fake duplicate orders, and malicious bulk checkouts by configuring dynamic rate limits per customer IP and account.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 px-3 py-2 rounded-pill fs-6">
                    <i class="fas fa-check-circle me-1 text-success"></i> Active Protection Live
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Configuration Form --}}
        <div class="col-lg-7">
            <div class="res-card">
                <div class="res-card-header">
                    <h6 class="res-card-title">
                        <i class="fas fa-sliders-h text-primary"></i>
                        Restriction Parameters
                    </h6>
                    <span class="text-muted small">Real-time enforcement</span>
                </div>
                <div class="res-card-body">
                    <form action="{{ route('admin.order.restriction.setting.update') }}" method="POST">
                        @csrf

                        {{-- Order Limit Time --}}
                        <div class="mb-4">
                            <label class="res-form-label">
                                <i class="fas fa-hourglass-half text-primary me-1"></i>
                                Time Window Duration <span class="text-danger">*</span>
                            </label>
                            <div class="input-group res-input-group mb-2">
                                <input type="number" id="orderLimitTimeInput" name="order_limit_time" 
                                       class="form-control" 
                                       placeholder="e.g. 48"
                                       value="{{ old('order_limit_time', $data->order_limit_time ?? 48) }}"
                                       min="1" max="720" required oninput="updatePreview()">
                                <span class="input-group-text">Hours (ঘন্টা)</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="text-muted small align-self-center">দ্রুত নির্বাচন:</span>
                                <button type="button" class="preset-chip" onclick="setTimeVal(24)">24 Hours (1 Day)</button>
                                <button type="button" class="preset-chip" onclick="setTimeVal(48)">48 Hours (2 Days)</button>
                                <button type="button" class="preset-chip" onclick="setTimeVal(72)">72 Hours (3 Days)</button>
                                <button type="button" class="preset-chip" onclick="setTimeVal(168)">7 Days</button>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i> এই সময়ের মধ্যে একজন কাস্টমার বা IP অ্যাড্রেস একই পণ্য সর্বোচ্চ কতবার অর্ডার করতে পারবে তা ট্র্যাক করা হয়।
                            </small>
                        </div>

                        {{-- Order Limit Qty --}}
                        <div class="mb-4">
                            <label class="res-form-label">
                                <i class="fas fa-shopping-cart text-success me-1"></i>
                                Maximum Allowed Orders Limit <span class="text-danger">*</span>
                            </label>
                            <div class="input-group res-input-group mb-2">
                                <input type="number" id="orderLimitQtyInput" name="order_limit_qty" 
                                       class="form-control" 
                                       placeholder="e.g. 2"
                                       value="{{ old('order_limit_qty', $data->order_limit_qty ?? 2) }}"
                                       min="1" max="50" required oninput="updatePreview()">
                                <span class="input-group-text">Times / Orders (বার)</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="text-muted small align-self-center">দ্রুত নির্বাচন:</span>
                                <button type="button" class="preset-chip" onclick="setQtyVal(1)">1 Time (একক অর্ডার)</button>
                                <button type="button" class="preset-chip" onclick="setQtyVal(2)">2 Times (ডিফল্ট)</button>
                                <button type="button" class="preset-chip" onclick="setQtyVal(3)">3 Times</button>
                                <button type="button" class="preset-chip" onclick="setQtyVal(5)">5 Times</button>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i> নির্ধারিত সময়সীমার মধ্যে সর্বোচ্চ যতবার অর্ডার করার অনুমতি দেওয়া হবে। এর বেশি হলে অর্ডার আটকানো হবে।
                            </small>
                        </div>

                        {{-- Live Rule Behavior Box --}}
                        <div class="live-preview-box mb-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-magic text-primary"></i>
                                <span class="fw-bold text-dark fs-6">সিস্টেম যেভাবে কাজ করবে (Live Preview)</span>
                            </div>
                            <p class="text-muted small mb-0" id="livePreviewText">
                                একজন কাস্টমার গত <strong class="text-primary" id="previewHours">{{ $data->order_limit_time ?? 48 }}</strong> ঘন্টার মধ্যে একই প্রোডাক্ট সর্বোচ্চ <strong class="text-success" id="previewQty">{{ $data->order_limit_qty ?? 2 }}</strong> বার অর্ডার করতে পারবে। এরপর পুনরায় অর্ডার করতে চাইলে সিস্টেম তা ব্লক করবে।
                            </p>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> সেটিংস সংরক্ষণ করুন
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Information & How It Works --}}
        <div class="col-lg-5">
            {{-- Active Status Card --}}
            <div class="res-card">
                <div class="res-card-header bg-light">
                    <h6 class="res-card-title">
                        <i class="fas fa-tachometer-alt text-success"></i> বর্তমান সক্রিয় কনফিগারেশন
                    </h6>
                </div>
                <div class="res-card-body">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3 border">
                        <div>
                            <div class="text-muted small">সময়সীমা (Duration Window)</div>
                            <div class="fs-5 fw-bold text-dark">{{ $data->order_limit_time ?? 48 }} ঘন্টা</div>
                        </div>
                        <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3 border">
                        <div>
                            <div class="text-muted small">সর্বোচ্চ অর্ডার সীমা (Max Limit)</div>
                            <div class="fs-5 fw-bold text-success">{{ $data->order_limit_qty ?? 2 }} বার</div>
                        </div>
                        <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 rounded-3 small mb-0">
                        <i class="fas fa-bolt me-1 text-warning"></i> <strong>রিয়েল-টাইম ইফেক্ট:</strong> সেটিংস সংরক্ষণ করার সাথে সাথেই ফ্রন্টএন্ড চেকআউট পেজে নতুন নিয়ম কার্যকর হবে।
                    </div>
                </div>
            </div>

            {{-- Mechanism Flowchart --}}
            <div class="res-card">
                <div class="res-card-header">
                    <h6 class="res-card-title">
                        <i class="fas fa-project-diagram text-info"></i> সুরক্ষার ধাপসমূহ
                    </h6>
                </div>
                <div class="res-card-body">
                    <div class="flow-step-item">
                        <div class="flow-step-icon">১</div>
                        <div>
                            <div class="fw-bold text-dark small">অর্ডার রিকোয়েস্ট যাচাই</div>
                            <div class="text-muted small">কাস্টমার যখন কোনো পণ্য অর্ডারের জন্য সাবমিট করে, সিস্টেম তার কাস্টমার আইডি ও আইপি এড্রেস সংগ্রহ করে।</div>
                        </div>
                    </div>

                    <div class="flow-step-item">
                        <div class="flow-step-icon">২</div>
                        <div>
                            <div class="fw-bold text-dark small">টাইম উইন্ডো ফিল্টার</div>
                            <div class="text-muted small">বর্তমান সময় থেকে পূর্ববর্তী নির্ধারিত ঘন্টার মধ্যে এই আইপি বা একাউন্ট থেকে কয়টি সফল অর্ডার হয়েছে তা গণনা করে।</div>
                        </div>
                    </div>

                    <div class="flow-step-item mb-0">
                        <div class="flow-step-icon" style="background:#ecfdf5; color:#10b981;">৩</div>
                        <div>
                            <div class="fw-bold text-dark small">সীমা অতিক্রম হলে ব্লক</div>
                            <div class="text-muted small">যদি নির্ধারিত পরিমাণের চেয়ে বেশি অর্ডার দেওয়ার চেষ্টা করা হয়, সিস্টেম একটি সহায়ক বার্তা দেখিয়ে অর্ডার বাতিল করে।</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function setTimeVal(val) {
        document.getElementById('orderLimitTimeInput').value = val;
        updatePreview();
    }

    function setQtyVal(val) {
        document.getElementById('orderLimitQtyInput').value = val;
        updatePreview();
    }

    function updatePreview() {
        const hours = document.getElementById('orderLimitTimeInput').value || 48;
        const qty = document.getElementById('orderLimitQtyInput').value || 2;
        
        document.getElementById('previewHours').innerText = hours;
        document.getElementById('previewQty').innerText = qty;
    }
</script>
@endsection
