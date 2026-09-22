@extends('frontEnd.layouts.master')
@section('title','Customer Login')
@php
    $generalsetting = \App\Models\GeneralSetting::first();
    $primaryColor = $generalsetting->primary_color ?? '#764ba2';
    $secondaryColor = $generalsetting->secodery_color ?? '#667eea';
@endphp
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap');

    .auth-page-wrapper {
        min-height: calc(100vh - 220px);
        background: radial-gradient(circle at 10% 20%, rgba(118, 75, 162, 0.04) 0%, rgba(240, 243, 249, 0.7) 90%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 15px;
        font-family: 'Plus Jakarta Sans', 'Hind Siliguri', sans-serif;
    }

    .auth-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.07), 0 0 1px 1px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 460px;
        padding: 38px 32px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease;
    }

    .auth-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, {{ $primaryColor }}, {{ $secondaryColor }});
    }

    .auth-header {
        text-align: center;
        margin-bottom: 26px;
        position: relative;
    }

    .auth-logo-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        width: 100%;
        clear: both;
    }

    .auth-logo-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        max-width: 100%;
    }

    .auth-brand-logo {
        display: block;
        max-height: 52px;
        max-width: 200px;
        width: auto;
        height: auto;
        object-fit: contain;
        margin: 0 auto;
    }

    .auth-title {
        clear: both;
        display: block;
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        margin-top: 0;
        margin-bottom: 6px;
        line-height: 1.35;
    }

    .auth-subtitle {
        font-size: 13.5px;
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.4;
    }

    .form-group-custom {
        margin-bottom: 18px;
        position: relative;
    }

    .form-label-custom {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 7px;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon-left {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .form-control-custom {
        width: 100%;
        height: 48px;
        padding: 10px 14px 10px 42px;
        font-size: 14px;
        color: #1e293b;
        background-color: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
        outline: none;
    }

    .form-control-custom:focus {
        background-color: #ffffff;
        border-color: {{ $primaryColor }};
        box-shadow: 0 0 0 4px {{ $primaryColor }}1a;
    }

    .form-control-custom:focus + .input-icon-left,
    .input-wrapper:focus-within .input-icon-left {
        color: {{ $primaryColor }};
    }

    .input-btn-right {
        position: absolute;
        right: 12px;
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: color 0.2s ease;
    }

    .input-btn-right:hover {
        color: #475569;
    }

    .auth-actions-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: -4px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .forgot-link {
        color: #64748b;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .forgot-link:hover {
        color: {{ $primaryColor }};
        text-decoration: underline;
    }

    .btn-auth-primary {
        width: 100%;
        height: 48px;
        background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
        border: none;
        border-radius: 12px;
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px {{ $primaryColor }}40;
    }

    .btn-auth-primary:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 6px 20px {{ $primaryColor }}55;
        color: #ffffff;
    }

    .btn-auth-primary:active {
        transform: translateY(0);
    }

    .auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 24px 0;
        color: #94a3b8;
        font-size: 12px;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }

    .auth-divider span {
        padding: 0 12px;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .auth-footer {
        text-align: center;
        font-size: 14px;
        color: #64748b;
    }

    .auth-footer a {
        color: {{ $primaryColor }};
        font-weight: 700;
        text-decoration: none;
        margin-left: 4px;
        transition: all 0.2s ease;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    /* Demo Credentials Box */
    .demo-creds-container {
        margin-top: 20px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
    }

    .demo-creds-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .demo-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 12.5px;
    }

    .demo-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .demo-role-badge {
        font-weight: 600;
        color: #334155;
        display: inline-block;
        min-width: 90px;
    }

    .demo-btn-use {
        padding: 3px 10px;
        font-size: 11.5px;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid {{ $primaryColor }};
        background: transparent;
        color: {{ $primaryColor }};
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .demo-btn-use:hover {
        background: {{ $primaryColor }};
        color: #fff;
    }

    @media (max-width: 576px) {
        .auth-card {
            padding: 28px 20px;
            border-radius: 16px;
        }
    }
</style>

<div class="auth-page-wrapper">
    <div class="auth-card">
        
        {{-- Header Section --}}
        <div class="auth-header">
            @php
                $siteAuthLogo = !empty($generalsetting->dark_logo) ? $generalsetting->dark_logo : ($generalsetting->white_logo ?? null);
            @endphp
            @if(!empty($siteAuthLogo))
                <div class="auth-logo-wrapper">
                    <a href="{{ route('home') }}" class="auth-logo-link">
                        <img src="{{ asset($siteAuthLogo) }}" alt="{{ $generalsetting->name ?? 'Logo' }}" class="auth-brand-logo">
                    </a>
                </div>
            @endif
            <h1 class="auth-title">লগইন করুন</h1>
            <p class="auth-subtitle">আপনার একাউন্টে প্রবেশ করতে তথ্য দিন</p>
        </div>

        {{-- Login Form --}}
        <form action="{{ route('customer.signin') }}" method="POST" data-parsley-validate="">
            @csrf

            {{-- Phone / Email Input --}}
            <div class="form-group-custom">
                <label for="login" class="form-label-custom">মোবাইল নাম্বার বা ইমেইল</label>
                <div class="input-wrapper">
                    <i class="fas fa-user-circle input-icon-left"></i>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           class="form-control-custom @error('login') is-invalid @enderror" 
                           value="{{ old('login') }}" 
                           placeholder="017xxxxxxxx অথবা ইমেইল" 
                           required 
                           autofocus>
                </div>
                @error('login')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password Input --}}
            <div class="form-group-custom">
                <label for="password" class="form-label-custom">পাসওয়ার্ড</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon-left"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control-custom @error('password') is-invalid @enderror" 
                           placeholder="••••••••" 
                           required>
                    <button type="button" class="input-btn-right" onclick="togglePasswordVisibility()" aria-label="Toggle password">
                        <i class="fas fa-eye" id="password_toggle_icon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Actions Row (Forgot Password) --}}
            <div class="auth-actions-row">
                <div></div>
                <a href="{{ route('customer.forgot.password') }}" class="forgot-link">
                    পাসওয়ার্ড ভুলে গেছেন?
                </a>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn-auth-primary">
                <span>লগইন করুন</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        {{-- Demo Credentials if active --}}
        @if(isset($demoMode) && $demoMode)
        <div class="demo-creds-container">
            <div class="demo-creds-title">
                <i class="fas fa-shield-alt"></i> টেস্ট একাউন্ট ক্রেডেনশিয়াল
            </div>
            <div class="demo-row">
                <span class="demo-role-badge"><i class="fas fa-briefcase text-muted mr-1"></i> রিসেলার</span>
                <span class="text-muted font-monospace">01631843149</span>
                <button type="button" class="demo-btn-use" onclick="fillDemoCreds('01631843149','12345678')">ব্যবহার করুন</button>
            </div>
            <div class="demo-row">
                <span class="demo-role-badge"><i class="fas fa-store text-muted mr-1"></i> ভেন্ডর</span>
                <span class="text-muted font-monospace">01870829343</span>
                <button type="button" class="demo-btn-use" onclick="fillDemoCreds('01870829343','123456789')">ব্যবহার করুন</button>
            </div>
        </div>
        @endif

        {{-- Divider --}}
        <div class="auth-divider">
            <span>অথবা</span>
        </div>

        {{-- Register Link --}}
        <div class="auth-footer">
            একাউন্ট তৈরি করা নেই? 
            <a href="{{ route('customer.register') }}">রেজিস্ট্রেশন করুন</a>
        </div>

    </div>
</div>

<script>
    function togglePasswordVisibility() {
        var input = document.getElementById("password");
        var icon = document.getElementById("password_toggle_icon");
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    function fillDemoCreds(login, pass) {
        document.getElementById('login').value = login;
        document.getElementById('password').value = pass;
    }
</script>
@endsection

@push('script')
<script src="{{ asset('public/frontEnd/js/parsley.min.js') }}"></script>
<script src="{{ asset('public/frontEnd/js/form-validation.init.js') }}"></script>
@endpush