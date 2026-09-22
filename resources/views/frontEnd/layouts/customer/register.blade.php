@extends('frontEnd.layouts.master')
@section('title','Customer Register')
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
        max-width: 620px;
        padding: 38px 36px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
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
        font-size: 24px;
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

    .form-row-custom {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .form-col-half {
        flex: 1 1 calc(50% - 8px);
        min-width: 240px;
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

    textarea.form-control-custom {
        height: auto;
        min-height: 80px;
        padding-top: 12px;
        padding-bottom: 12px;
        resize: vertical;
    }

    input[type="file"].form-control-custom {
        padding-left: 42px;
        padding-top: 9px;
        font-size: 13px;
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

    /* Role Selection Cards */
    .role-selection-box {
        margin: 16px 0 20px 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .role-card-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .role-card-label:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }

    .role-card-label.active {
        border-color: {{ $primaryColor }};
        background: {{ $primaryColor }}08;
    }

    .role-card-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .role-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: {{ $primaryColor }};
        font-size: 15px;
    }

    .role-text-title {
        font-weight: 600;
        font-size: 14px;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .role-text-desc {
        font-size: 12px;
        color: #64748b;
    }

    .role-checkbox-custom {
        width: 18px;
        height: 18px;
        accent-color: {{ $primaryColor }};
        cursor: pointer;
    }

    .upload-hint-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        color: #0284c7;
        background: #e0f2fe;
        padding: 3px 8px;
        border-radius: 6px;
        margin-top: 5px;
        font-weight: 500;
    }

    .info-alert-box {
        padding: 14px 16px;
        margin: 14px 0;
        border-radius: 12px;
        border: 1px solid rgba(118, 75, 162, 0.2);
        background: linear-gradient(135deg, #fdf4ff 0%, #f0fdf4 100%);
        color: #334155;
        font-size: 13px;
        line-height: 1.5;
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
        margin-top: 10px;
    }

    .btn-auth-primary:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 6px 20px {{ $primaryColor }}55;
        color: #ffffff;
    }

    .btn-auth-primary:active {
        transform: translateY(0);
    }

    .auth-footer {
        text-align: center;
        font-size: 14px;
        color: #64748b;
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px dashed #e2e8f0;
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

    @media (max-width: 576px) {
        .auth-card {
            padding: 26px 18px;
            border-radius: 16px;
        }
        .form-col-half {
            flex: 1 1 100%;
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
            <h1 class="auth-title">রেজিস্ট্রেশন করুন</h1>
            <p class="auth-subtitle">আপনার প্রয়োজনীয় তথ্য দিয়ে একাউন্ট তৈরি করুন</p>
        </div>

        {{-- Registration Form --}}
        <form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data" data-parsley-validate="">
            @csrf

            <div class="form-row-custom">
                {{-- Name Input --}}
                <div class="form-group-custom form-col-half">
                    <label for="name" class="form-label-custom">
                        আপনার নাম <span id="owner_name_label" style="display: none; color: {{ $primaryColor }};">(মালিকের নাম)</span> <span class="text-danger">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon-left"></i>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control-custom @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="আপনার নাম লিখুন" 
                               required>
                    </div>
                    @error('name')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone Input --}}
                <div class="form-group-custom form-col-half">
                    <label for="phone" class="form-label-custom">মোবাইল নাম্বার <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone-alt input-icon-left"></i>
                        <input type="number" 
                               id="phone" 
                               name="phone" 
                               class="form-control-custom @error('phone') is-invalid @enderror" 
                               value="{{ old('phone') }}" 
                               placeholder="017xxxxxxxx" 
                               required>
                    </div>
                    @error('phone')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Email Input (Conditional for seller/reseller) --}}
            <div class="form-group-custom" id="email_field" style="display: none;">
                <label for="email" class="form-label-custom">ইমেইল এড্রেস <span class="text-danger">*</span></label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon-left"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control-custom @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" 
                           placeholder="example@mail.com">
                </div>
                @error('email')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Shop Details for Seller --}}
            <div id="seller_specific_fields" style="display: none;">
                <div class="form-row-custom">
                    {{-- Shop Name --}}
                    <div class="form-group-custom form-col-half">
                        <label for="shop_name" class="form-label-custom">শপের নাম <span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-store input-icon-left"></i>
                            <input type="text" 
                                   id="shop_name" 
                                   name="shop_name" 
                                   class="form-control-custom @error('shop_name') is-invalid @enderror" 
                                   value="{{ old('shop_name') }}" 
                                   placeholder="যেমন: ফ্যাশন হাউজ">
                        </div>
                        @error('shop_name')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Shop Slug --}}
                    <div class="form-group-custom form-col-half">
                        <label for="slug" class="form-label-custom">শপ লিংক (Slug) <span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-link input-icon-left"></i>
                            <input type="text" 
                                   id="slug" 
                                   name="slug" 
                                   class="form-control-custom @error('slug') is-invalid @enderror" 
                                   value="{{ old('slug') }}" 
                                   placeholder="fashion-house">
                        </div>
                        @error('slug')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Address --}}
                <div class="form-group-custom">
                    <label for="address" class="form-label-custom">শপের পূর্ণ ঠিকানা</label>
                    <div class="input-wrapper">
                        <i class="fas fa-map-marker-alt input-icon-left" style="top: 16px;"></i>
                        <textarea id="address" 
                                  name="address" 
                                  class="form-control-custom @error('address') is-invalid @enderror" 
                                  rows="2" 
                                  placeholder="দোকান বা ব্যবসা প্রতিষ্ঠানের ঠিকানা">{{ old('address') }}</textarea>
                    </div>
                    @error('address')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Logo and Banner for Seller --}}
                <div class="form-row-custom">
                    {{-- Logo --}}
                    <div class="form-group-custom form-col-half">
                        <label for="logo" class="form-label-custom">শপ লোগো</label>
                        <div class="input-wrapper">
                            <i class="fas fa-image input-icon-left"></i>
                            <input type="file" 
                                   id="logo" 
                                   name="logo" 
                                   class="form-control-custom @error('logo') is-invalid @enderror" 
                                   accept="image/jpeg,image/png,image/webp,image/gif">
                        </div>
                        <div class="upload-hint-badge">
                            <i class="fas fa-shield-alt"></i> WebP ফরম্যাট • সর্বোচ্চ 100 KB
                        </div>
                        @error('logo')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Banner --}}
                    <div class="form-group-custom form-col-half">
                        <label for="banner" class="form-label-custom">শপ ব্যানার</label>
                        <div class="input-wrapper">
                            <i class="fas fa-panorama input-icon-left"></i>
                            <input type="file" 
                                   id="banner" 
                                   name="banner" 
                                   class="form-control-custom @error('banner') is-invalid @enderror" 
                                   accept="image/jpeg,image/png,image/webp,image/gif">
                        </div>
                        <div class="upload-hint-badge">
                            <i class="fas fa-shield-alt"></i> WebP ফরম্যাট • সর্বোচ্চ 300 KB
                        </div>
                        @error('banner')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="info-alert-box">
                    <strong><i class="fas fa-info-circle mr-1" style="color: {{ $primaryColor }};"></i> বিক্রেতা একাউন্ট ভেরিফিকেশন:</strong><br>
                    রেজিস্ট্রেশনের পর সেলার ড্যাশবোর্ডে লগইন করে ভেরিফিকেশন অপশন থেকে এনআইডি (NID) ও ছবি জমা দিতে পারবেন।
                </div>
            </div>

            {{-- Shop Name for Reseller --}}
            <div class="form-group-custom" id="reseller_shop_name_field" style="display: none;">
                <label for="reseller_shop_name" class="form-label-custom">রিসেলার শপ বা পেজের নাম <span class="text-danger">*</span></label>
                <div class="input-wrapper">
                    <i class="fas fa-briefcase input-icon-left"></i>
                    <input type="text" 
                           id="reseller_shop_name" 
                           name="reseller_shop_name" 
                           class="form-control-custom @error('reseller_shop_name') is-invalid @enderror" 
                           value="{{ old('reseller_shop_name') }}" 
                           placeholder="আপনার পেজ বা ব্র্যান্ডের নাম">
                </div>
                @error('reseller_shop_name')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div id="reseller_verification_note" style="display: none;">
                <div class="info-alert-box">
                    <strong><i class="fas fa-info-circle mr-1" style="color: {{ $primaryColor }};"></i> রিসেলার ভেরিফিকেশন:</strong><br>
                    সফলভাবে রেজিস্ট্রেশনের পর লগইন করে <strong>রিসেলার ড্যাশবোর্ড → ভেরিফিকেশন</strong> থেকে ভোটার আইডি ও ছবি জমা দিতে পারবেন।
                </div>
            </div>

            {{-- Passwords Row --}}
            <div class="form-row-custom">
                {{-- Password Input --}}
                <div class="form-group-custom form-col-half">
                    <label for="password" class="form-label-custom">পাসওয়ার্ড <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon-left"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control-custom @error('password') is-invalid @enderror" 
                               placeholder="••••••••" 
                               required>
                        <button type="button" class="input-btn-right" onclick="togglePassVisibility('password', 'pwd_icon')" aria-label="Toggle password">
                            <i class="fas fa-eye" id="pwd_icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password Confirmation (Required for seller/reseller) --}}
                <div class="form-group-custom form-col-half" id="password_confirmation_field" style="display: none;">
                    <label for="password_confirmation" class="form-label-custom">পাসওয়ার্ড নিশ্চিত করুন <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon-left"></i>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-control-custom @error('password_confirmation') is-invalid @enderror" 
                               placeholder="••••••••">
                        <button type="button" class="input-btn-right" onclick="togglePassVisibility('password_confirmation', 'pwd_conf_icon')" aria-label="Toggle password confirmation">
                            <i class="fas fa-eye" id="pwd_conf_icon"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Role Selection Options --}}
            <div class="role-selection-box">
                @if(($generalsetting?->reseller_enabled ?? 1) == 1)
                <label class="role-card-label" id="reseller_label" for="is_reseller">
                    <div class="role-card-info">
                        <div class="role-icon">
                            <i class="fas fa-truck-moving"></i>
                        </div>
                        <div>
                            <div class="role-text-title">রিসেলার হিসেবে যুক্ত হতে চাই</div>
                            <div class="role-text-desc">শূন্য বিনিয়োগে নিজস্ব কাস্টমারদের কাছে পণ্য রিসেল করুন</div>
                        </div>
                    </div>
                    <input type="checkbox" id="is_reseller" name="is_reseller" value="1" 
                           {{ old('is_reseller') ? 'checked' : '' }} 
                           onchange="toggleResellerFields()" 
                           class="role-checkbox-custom">
                </label>
                @endif

                @if(($generalsetting?->vendor_enabled ?? 1) == 1)
                <label class="role-card-label" id="seller_label" for="is_seller">
                    <div class="role-card-info">
                        <div class="role-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <div class="role-text-title">ভেন্ডর / সেলার হিসেবে পণ্য বিক্রি করতে চাই</div>
                            <div class="role-text-desc">মার্কেটপ্লেসে আপনার নিজস্ব শপ তৈরি করে পণ্য লিস্টিং করুন</div>
                        </div>
                    </div>
                    <input type="checkbox" id="is_seller" name="is_seller" value="1" 
                           {{ old('is_seller') ? 'checked' : '' }} 
                           onchange="toggleSellerFields()" 
                           class="role-checkbox-custom">
                </label>
                @endif
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn-auth-primary">
                <span>একাউন্ট তৈরি করুন</span>
                <i class="fas fa-check-circle"></i>
            </button>
        </form>

        {{-- Login Link --}}
        <div class="auth-footer">
            পূর্বেই রেজিস্ট্রেশন করা আছে? 
            <a href="{{ route('customer.login') }}">লগইন করুন</a>
        </div>

    </div>
</div>

<script>
    function togglePassVisibility(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        if (!input) return;
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

    function toggleResellerFields() {
        var isReseller = document.getElementById("is_reseller") ? document.getElementById("is_reseller").checked : false;
        var resellerLabel = document.getElementById("reseller_label");
        var resellerShopNameField = document.getElementById("reseller_shop_name_field");
        var resellerVerificationNote = document.getElementById("reseller_verification_note");
        var passwordConfirmationField = document.getElementById("password_confirmation_field");
        var emailField = document.getElementById("email_field");
        
        if (resellerLabel) {
            if (isReseller) {
                resellerLabel.classList.add('active');
            } else {
                resellerLabel.classList.remove('active');
            }
        }

        if (resellerShopNameField) {
            resellerShopNameField.style.display = isReseller ? 'block' : 'none';
            var shopNameInput = document.getElementById("reseller_shop_name");
            if (shopNameInput) {
                if (isReseller) {
                    shopNameInput.setAttribute('required', 'required');
                } else {
                    shopNameInput.removeAttribute('required');
                }
            }
        }

        if (resellerVerificationNote) {
            resellerVerificationNote.style.display = isReseller ? 'block' : 'none';
        }

        var isSeller = document.getElementById("is_seller") ? document.getElementById("is_seller").checked : false;

        // Toggle email
        if (emailField) {
            if (isReseller || isSeller) {
                emailField.style.display = 'block';
                var emailInput = document.getElementById("email");
                if (emailInput) emailInput.setAttribute('required', 'required');
            } else {
                emailField.style.display = 'none';
                var emailInput = document.getElementById("email");
                if (emailInput) emailInput.removeAttribute('required');
            }
        }

        // Toggle password confirmation
        if (passwordConfirmationField) {
            if (isReseller || isSeller) {
                passwordConfirmationField.style.display = 'block';
                var pwdConfInput = document.getElementById("password_confirmation");
                if (pwdConfInput) pwdConfInput.setAttribute('required', 'required');
            } else {
                passwordConfirmationField.style.display = 'none';
                var pwdConfInput = document.getElementById("password_confirmation");
                if (pwdConfInput) pwdConfInput.removeAttribute('required');
            }
        }

        // Uncheck seller if reseller selected
        if (isReseller && isSeller) {
            var sellerCheckbox = document.getElementById("is_seller");
            if (sellerCheckbox) {
                sellerCheckbox.checked = false;
                toggleSellerFields();
            }
        }
    }

    function toggleSellerFields() {
        var isSeller = document.getElementById("is_seller") ? document.getElementById("is_seller").checked : false;
        var sellerLabel = document.getElementById("seller_label");
        var sellerSpecificFields = document.getElementById("seller_specific_fields");
        var ownerLabel = document.getElementById("owner_name_label");
        var passwordConfirmationField = document.getElementById("password_confirmation_field");
        var emailField = document.getElementById("email_field");

        if (sellerLabel) {
            if (isSeller) {
                sellerLabel.classList.add('active');
            } else {
                sellerLabel.classList.remove('active');
            }
        }

        if (sellerSpecificFields) {
            sellerSpecificFields.style.display = isSeller ? 'block' : 'none';
            var shopNameInput = document.getElementById("shop_name");
            var slugInput = document.getElementById("slug");
            if (shopNameInput) {
                if (isSeller) shopNameInput.setAttribute('required', 'required');
                else shopNameInput.removeAttribute('required');
            }
            if (slugInput) {
                if (isSeller) slugInput.setAttribute('required', 'required');
                else slugInput.removeAttribute('required');
            }
        }

        if (ownerLabel) {
            ownerLabel.style.display = isSeller ? 'inline' : 'none';
        }

        var isReseller = document.getElementById("is_reseller") ? document.getElementById("is_reseller").checked : false;

        // Toggle email
        if (emailField) {
            if (isSeller || isReseller) {
                emailField.style.display = 'block';
                var emailInput = document.getElementById("email");
                if (emailInput) emailInput.setAttribute('required', 'required');
            } else {
                emailField.style.display = 'none';
                var emailInput = document.getElementById("email");
                if (emailInput) emailInput.removeAttribute('required');
            }
        }

        // Toggle password confirmation
        if (passwordConfirmationField) {
            if (isSeller || isReseller) {
                passwordConfirmationField.style.display = 'block';
                var pwdConfInput = document.getElementById("password_confirmation");
                if (pwdConfInput) pwdConfInput.setAttribute('required', 'required');
            } else {
                passwordConfirmationField.style.display = 'none';
                var pwdConfInput = document.getElementById("password_confirmation");
                if (pwdConfInput) pwdConfInput.removeAttribute('required');
            }
        }

        // Uncheck reseller if seller selected
        if (isSeller && isReseller) {
            var resellerCheckbox = document.getElementById("is_reseller");
            if (resellerCheckbox) {
                resellerCheckbox.checked = false;
                toggleResellerFields();
            }
        }
    }

    // Auto slug generator
    document.addEventListener('DOMContentLoaded', function() {
        var shopNameInput = document.getElementById('shop_name');
        var slugInput = document.getElementById('slug');
        if (shopNameInput && slugInput) {
            shopNameInput.addEventListener('input', function() {
                var slug = this.value.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                slugInput.value = slug;
            });
        }

        if (document.getElementById("is_seller") && document.getElementById("is_seller").checked) {
            toggleSellerFields();
        }
        if (document.getElementById("is_reseller") && document.getElementById("is_reseller").checked) {
            toggleResellerFields();
        }
    });
</script>
@endsection

@push('script')
<script src="{{ asset('public/frontEnd/js/parsley.min.js') }}"></script>
<script src="{{ asset('public/frontEnd/js/form-validation.init.js') }}"></script>
@endpush