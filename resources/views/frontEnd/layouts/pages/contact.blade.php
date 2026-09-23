@extends('frontEnd.layouts.master')
@section('title', 'যোগাযোগ করুন - ' . ($generalsetting->name ?? 'Online Shop'))
@php
    $generalsetting = \App\Models\GeneralSetting::first();
@endphp

@section('content')
<style>
    .contact-page-section {
        padding: 50px 0 80px 0;
        background: #f8fafc;
        min-height: 80vh;
    }
    .contact-header-wrap {
        text-align: center;
        max-width: 650px;
        margin: 0 auto 40px auto;
    }
    .contact-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #303d6e15;
        color: #303d6e;
        font-size: 13px;
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 999px;
        margin-bottom: 12px;
    }
    .contact-heading {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }
    .contact-subtext {
        font-size: 15px;
        color: #64748b;
        line-height: 1.6;
    }
    .contact-card-box {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        padding: 36px;
        height: 100%;
    }
    @media (max-width: 767px) {
        .contact-card-box {
            padding: 22px 18px;
        }
        .contact-heading {
            font-size: 26px;
        }
    }
    .contact-info-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .contact-info-card {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 18px;
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .contact-info-card:hover {
        background: #ffffff;
        border-color: #303d6e;
        box-shadow: 0 6px 20px rgba(48, 61, 110, 0.08);
        transform: translateY(-2px);
    }
    .contact-info-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #303d6e;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(48, 61, 110, 0.25);
    }
    .contact-info-icon.whatsapp {
        background: #25D366;
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.25);
    }
    .contact-info-content h6 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .contact-info-content p, .contact-info-content a {
        font-size: 14px;
        color: #475569;
        margin: 0;
        text-decoration: none;
        transition: color 0.2s;
    }
    .contact-info-content a:hover {
        color: #303d6e;
    }
    .contact-form-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .contact-form-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 24px;
    }
    .contact-input-group {
        margin-bottom: 18px;
    }
    .contact-input-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .contact-input-field {
        width: 100%;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 14px;
        color: #0f172a;
        outline: none;
        background: #ffffff;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .contact-input-field:focus {
        border-color: #303d6e;
        box-shadow: 0 0 0 4px rgba(48, 61, 110, 0.1);
    }
    .contact-submit-btn {
        width: 100%;
        background: #303d6e;
        color: #ffffff;
        border: none;
        padding: 14px 24px;
        font-size: 15px;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 14px rgba(48, 61, 110, 0.25);
    }
    .contact-submit-btn:hover {
        background: #232d53;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(48, 61, 110, 0.35);
    }
</style>

<div class="contact-page-section">
    <div class="container">
        
        {{-- Header Title --}}
        <div class="contact-header-wrap">
            <span class="contact-badge-pill">
                <i class="fa-solid fa-headset"></i> কাস্টমার সাপোর্ট
            </span>
            <h1 class="contact-heading">আমাদের সাথে যোগাযোগ করুন</h1>
            <p class="contact-subtext">আপনার যেকোনো প্রশ্ন, অর্ডার সংক্রান্ত তথ্য বা মতামতের জন্য আমাদের জানান। আমরা দ্রুত সমাধান দিতে প্রতিশ্রুতিবদ্ধ।</p>
        </div>

        <div class="row g-4 justify-content-center">
            
            {{-- Left: Contact Information Cards --}}
            <div class="col-lg-5">
                <div class="contact-card-box">
                    <h4 class="contact-form-title">যোগাযোগের ঠিকানা</h4>
                    <p class="contact-form-subtitle">সরাসরি কল বা মেসেজ দিয়ে আমাদের সাথে যুক্ত হতে পারেন।</p>

                    <div class="contact-info-list">
                        {{-- Phone / Hotline --}}
                        @if(!empty($contact->hotline))
                        <div class="contact-info-card">
                            <div class="contact-info-icon">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div class="contact-info-content">
                                <h6>হটলাইন / মোবাইল</h6>
                                <p><a href="tel:{{ $contact->hotline }}">{{ $contact->hotline }}</a></p>
                            </div>
                        </div>
                        @endif

                        {{-- WhatsApp --}}
                        @if(!empty($contact->whatsapp))
                        <div class="contact-info-card">
                            <div class="contact-info-icon whatsapp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div class="contact-info-content">
                                <h6>হোয়াটসঅ্যাপ চ্যাট</h6>
                                <p>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->whatsapp) }}?text=Hello" target="_blank">
                                        {{ $contact->whatsapp }} (মেসেজ করুন)
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif

                        {{-- Email --}}
                        @if(!empty($contact->email))
                        <div class="contact-info-card">
                            <div class="contact-info-icon">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div class="contact-info-content">
                                <h6>ইমেইল এড্রেস</h6>
                                <p><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></p>
                            </div>
                        </div>
                        @endif

                        {{-- Address --}}
                        @if(!empty($contact->address))
                        <div class="contact-info-card">
                            <div class="contact-info-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="contact-info-content">
                                <h6>অফিস / শোরুম</h6>
                                <p>{{ $contact->address }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: Modern Contact Form --}}
            <div class="col-lg-7">
                <div class="contact-card-box">
                    <h4 class="contact-form-title">একটি বার্তা পাঠান</h4>
                    <p class="contact-form-subtitle">নিচের ফর্মটি পূরণ করে পাঠান, আমাদের প্রতিনিধি অতি দ্রুত আপনার সাথে যোগাযোগ করবে।</p>

                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px; font-weight: 600;">
                            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('frontend.contact.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="contact-input-group">
                                    <label class="contact-input-label">আপনার পূর্ণ নাম <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="contact-input-field" placeholder="যেমন: রহিম আহমেদ" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="contact-input-group">
                                    <label class="contact-input-label">মোবাইল নাম্বার <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile" class="contact-input-field" placeholder="০১xxx-xxxxxx" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="contact-input-group">
                                    <label class="contact-input-label">ইমেইল (যদি থাকে)</label>
                                    <input type="email" name="email" class="contact-input-field" placeholder="example@mail.com" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="contact-input-group">
                                    <label class="contact-input-label">বিষয়</label>
                                    <input type="text" name="subject" class="contact-input-field" placeholder="কি বিষয়ে জানতে চান?" />
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="contact-input-group">
                                    <label class="contact-input-label">আপনার বার্তা / মেসেজ <span class="text-danger">*</span></label>
                                    <textarea name="details" class="contact-input-field" rows="5" placeholder="বিস্তারিত এখানে লিখুন..." required></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="contact-submit-btn">
                                    <span>মেসেজ পাঠান</span> <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection