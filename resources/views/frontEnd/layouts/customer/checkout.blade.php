@extends('frontEnd.layouts.master')
@section('title', 'Customer Checkout')
@php
    $generalsetting = \App\Models\GeneralSetting::first();
    $primaryColor = $generalsetting->primary_color ?? '#0d6efd';
    $secondaryColor = $generalsetting->secodery_color ?? $primaryColor;
    $hasNewsTicker = $generalsetting
        && (int) ($generalsetting->news_ticker_enabled ?? 0) === 1
        && trim((string) ($generalsetting->top_headline ?? '')) !== '';
    $checkoutMobilePadTop = $hasNewsTicker ? '148px' : '118px';
    $checkoutDesktopPadTop = $hasNewsTicker ? '208px' : '178px';
@endphp
@push('css')
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/select2.min.css') }}" />
<style>
    /* ================================================================
       MODERN COMPACT CHECKOUT STYLES - BRAND THEME
    ================================================================ */
    :root {
        --primary-color: {{ $primaryColor }};
        --secondary-color: {{ $secondaryColor }};
        --brand-color: {{ $primaryColor }};
        --success-color: #28a745;
        --border-color: #e5e7eb;
        --bg-color: #f8f9fa;
        --text-dark: #1f2937;
        --text-light: #6b7280;
    }

    body.checkout-page #content {
        padding-top: calc(var(--navbar-height, {{ $hasNewsTicker ? '180px' : '150px' }}) + 24px) !important;
    }

    .checkout-section {
        background-color: var(--bg-color);
        padding: 8px 0 32px;
        font-family: 'Poppins', sans-serif;
    }

    .checkout-layout-row {
        align-items: flex-start !important;
    }

    .checkout-form-col,
    .checkout-summary-col {
        align-self: start !important;
    }

    @media (min-width: 992px) {
        .checkout-form-col,
        .checkout-summary-col {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
    }

    .checkout-summary-col .checkout-card {
        margin-top: 0;
    }

    /* Desktop: CSS Grid */
    @media (min-width: 992px) {
        .checkout-layout-row {
            display: grid !important;
            grid-template-columns: minmax(0, 1.15fr) minmax(300px, 420px);
            gap: 16px;
            align-items: stretch !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            --bs-gutter-x: 0;
        }

        .checkout-layout-row > .checkout-form-col,
        .checkout-layout-row > .checkout-summary-col {
            width: 100% !important;
            max-width: 100% !important;
            flex: none !important;
            order: unset !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .checkout-form-col {
            grid-column: 1;
            grid-row: 1;
        }

        .checkout-summary-col {
            grid-column: 2;
            grid-row: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-self: stretch !important;
        }
    }

    .checkout-summary-sticky {
        width: 100%;
    }

    @media (min-width: 992px) {
        .checkout-summary-sticky {
            z-index: 50;
            align-self: flex-start;
        }

        .checkout-summary-sticky.is-fixed {
            position: fixed !important;
            z-index: 50;
        }

        .checkout-summary-sticky.is-abs-bottom {
            position: absolute !important;
            left: 0;
            right: 0;
            bottom: 0;
            top: auto !important;
        }
    }

    /* checkout পেজে sticky কাজ করাতে overflow ঠিক */
    html.checkout-page,
    body.checkout-page {
        overflow-x: visible !important;
        overflow-y: visible !important;
    }

    body.checkout-page #content,
    body.checkout-page .checkout-section,
    body.checkout-page .checkout-section .container,
    body.checkout-page .checkout-section form,
    body.checkout-page .checkout-layout-row {
        overflow: visible !important;
    }

    /* --- Card Design (Compact) --- */
    .checkout-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        margin-bottom: 12px;
        overflow: hidden;
    }

    .checkout-header {
        background: #fff;
        padding: 10px 16px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .checkout-header i {
        color: {{ $primaryColor }};
        font-size: 16px;
    }
    .checkout-header h6 {
        margin: 0;
        font-size: 14.5px;
        font-weight: 700;
        color: var(--primary-color);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .card-body-custom {
        padding: 14px 16px;
    }

    /* --- Form Inputs (Tight) --- */
    .form-group { margin-bottom: 10px; }
    .form-label-custom {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 4px;
        display: block;
    }
    .form-control-custom {
        width: 100%;
        height: 40px;
        min-height: 40px;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 0 12px;
        font-size: 13.5px;
        color: #1e293b !important;
        transition: all 0.2s ease;
        background-color: #fff !important;
    }
    .form-control-custom:focus {
        border-color: var(--primary-color, #303d6e) !important;
        box-shadow: 0 0 0 3px rgba(48, 61, 110, 0.12) !important;
        outline: none !important;
        background-color: #fff !important;
    }
    textarea.form-control-custom {
        height: auto;
        min-height: 56px;
        padding: 8px 12px;
        line-height: 1.4;
    }

    /* --- Payment Methods (Responsive 2-Column Grid & Full Width if Single) --- */
    .payment-options-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .payment-options-list .payment-option-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 0;
        background: #fff;
        position: relative;
        min-height: 50px;
        height: 100%;
    }
    /* ১টি পেমেন্ট মেথড এক্টিভ থাকলে সম্পূর্ণ ফুল উইডথ (100% width) হবে */
    .payment-options-list .payment-option-label:only-child {
        grid-column: 1 / -1;
    }
    .payment-option-label:hover {
        border-color: #9ca3af;
        background: #f9fafb;
    }
    .payment-option-label input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    /* Selected State */
    .payment-option-label:has(input:checked) {
        border-color: var(--primary-color);
        background-color: #f0f5ff;
        box-shadow: 0 0 0 1px var(--primary-color);
    }
    .payment-content {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        min-width: 0;
    }
    .pay-logo {
        width: 28px;
        height: 28px;
        object-fit: contain;
        flex-shrink: 0;
    }
    .pay-info {
        min-width: 0;
        flex: 1;
    }
    .pay-info strong {
        display: block;
        font-size: 13px;
        color: var(--text-dark);
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pay-info small {
        font-size: 11px;
        color: var(--text-light);
        display: block;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .check-circle {
        width: 16px;
        height: 16px;
        border: 2px solid #ccc;
        border-radius: 50%;
        position: relative;
        flex-shrink: 0;
    }
    .payment-option-label input:checked ~ .check-circle {
        border-color: var(--primary-color);
        background: var(--primary-color);
    }
    .payment-option-label input:checked ~ .check-circle::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 6px;
        height: 6px;
        background: #fff;
        border-radius: 50%;
    }

    /* --- Cart Items (Scrollable & Compact) --- */
    .cart-items-scroll {
        max-height: 280px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .cart-items-scroll.is-empty {
        display: none;
        max-height: 0;
        padding: 0;
        margin: 0;
    }
    .cart-items-scroll::-webkit-scrollbar { width: 4px; }
    .cart-items-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
    .cart-items-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .checkout-item {
        display: flex;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px dashed var(--border-color);
        position: relative;
        align-items: center;
    }
    .checkout-item:last-child { border-bottom: none; }
    
    .checkout-pro-img {
        width: 50px;
        height: 50px;
        border-radius: 6px;
        border: 1px solid #eee;
        object-fit: cover;
        flex-shrink: 0;
    }
    .checkout-pro-info h6 {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0 0 3px;
        line-height: 1.35;
    }
    .checkout-pro-info .meta {
        font-size: 11.5px;
        color: var(--text-light);
    }
    .remove-item-btn {
        color: #ef4444;
        cursor: pointer;
        font-size: 14px;
        position: absolute;
        top: 8px;
        right: 0;
        transition: 0.2s;
    }
    .remove-item-btn:hover { color: #dc2626; transform: scale(1.15); }

    /* Quantity Control (Tight & Compact) */
    .qty-box {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        padding: 1px;
        margin-top: 3px;
        height: 25px;
        width: fit-content;
    }
    .qty-btn {
        width: 22px;
        height: 21px;
        border: none;
        background: #fff;
        border-radius: 3px;
        color: var(--primary-color);
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: 0.15s ease;
    }
    .qty-btn:hover { background: var(--primary-color); color: #fff; }
    .qty-val {
        width: 22px;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        line-height: 21px;
        color: #1e293b;
    }

    /* --- COUPON BOX (TIGHT & COMPACT) --- */
    .coupon-wrapper {
        background: #f8fafc;
        padding: 6px 14px;
        border-top: 1px dashed #e2e8f0;
        border-bottom: 1px dashed #e2e8f0;
        margin: 0;
    }
    .coupon-group-modern {
        display: flex;
        align-items: stretch;
        width: 100%;
        height: 32px;
        border: 1.5px solid #cbd5e1;
        border-radius: 6px;
        overflow: hidden;
        transition: all 0.2s ease;
        background: #fff;
    }
    .coupon-group-modern:focus-within {
        border-color: var(--primary-color, #0f3460);
        box-shadow: 0 0 0 2px rgba(15, 52, 96, 0.08);
    }
    .coupon-input-modern {
        flex: 1 1 0;
        min-width: 0;
        border: none;
        padding: 0 10px;
        font-size: 12.5px;
        color: #1e293b;
        background: transparent;
        outline: none !important;
    }
    .coupon-input-modern::placeholder {
        color: #94a3b8;
        font-size: 12px;
    }
    .coupon-btn-modern {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--text-dark, #0f3460);
        color: #ffffff !important;
        border: none;
        padding: 0 14px;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        cursor: pointer;
        transition: background 0.2s ease;
        white-space: nowrap;
    }
    .coupon-btn-modern:hover {
        background: var(--primary-color, #1e3a8a);
    }
    .coupon-applied-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        padding: 4px 10px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 5px;
        font-size: 12px;
        color: #065f46;
    }
    .coupon-applied-text {
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .coupon-remove-btn {
        flex-shrink: 0;
        color: #dc2626 !important;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        padding: 1px 5px;
        border-radius: 4px;
        background: #fee2e2;
        transition: background 0.2s ease;
    }
    .coupon-remove-btn:hover {
        background: #fecaca;
    }

    /* --- Totals Area (Compact) --- */
    .summary-totals {
        padding: 10px 14px;
        background: #fff;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 13.5px;
        color: var(--text-dark);
    }
    .total-row.final {
        border-top: 1.5px dashed #e5e7eb;
        margin-top: 6px;
        padding-top: 6px;
        font-size: 16px;
        font-weight: 800;
        color: var(--primary-color);
    }
    
    /* Advance/Due Alert */
    .advance-alert {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        padding: 6px 10px;
        margin-top: 6px;
        text-align: center;
    }

    /* 🎯 Attention-Grabbing Shake / Vibration for Checkout Order Button ("ঝাঁকাঝাঁকি") */
    @keyframes orderJhakajhaki {
        0%, 65%, 100% {
            transform: scale(1) translate3d(0, 0, 0);
            box-shadow: 0 3px 12px {{ $primaryColor }}55;
        }
        68%, 76%, 84% {
            transform: scale(1.02) translate3d(-2px, 0, 0) rotate(-0.8deg);
            box-shadow: 0 5px 18px {{ $primaryColor }}80;
        }
        72%, 80%, 88% {
            transform: scale(1.02) translate3d(2px, 0, 0) rotate(0.8deg);
            box-shadow: 0 5px 18px {{ $primaryColor }}80;
        }
        92% {
            transform: scale(1.01) translate3d(0, 0, 0) rotate(0deg);
        }
    }

    /* --- Submit Button (Branding Color & Compact) --- */
    .btn-place-order {
        background: {{ $primaryColor }};
        color: #fff !important;
        width: 100%;
        border: none;
        padding: 12px 18px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: 0.25s;
        box-shadow: 0 6px 18px {{ $primaryColor }}40;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        animation: orderJhakajhaki 2.8s infinite ease-in-out !important;
    }
    .btn-place-order:hover {
        background: {{ $primaryColor }};
        filter: brightness(0.9);
        transform: translateY(-2px);
        animation: none !important;
    }

    /* 🚀 Floating Checkout Order Bar */
    .checkout-floating-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #ffffff;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.12);
        border-top: 1px solid #e5e7eb;
        z-index: 10005;
        padding: 8px 16px;
        transform: translateY(115%);
        opacity: 0;
        pointer-events: none;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
    }
    .checkout-floating-bar.is-visible {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }
    .checkout-floating-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .checkout-floating-total {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .checkout-floating-label {
        font-size: 11.5px;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 0px;
    }
    .checkout-floating-amount {
        font-size: 16px;
        font-weight: 800;
        color: {{ $primaryColor }};
        white-space: nowrap;
    }
    .checkout-floating-btn {
        background: {{ $primaryColor }};
        color: #ffffff !important;
        border: none;
        padding: 9px 22px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 4px 12px {{ $primaryColor }}40;
        animation: orderJhakajhaki 2.8s infinite ease-in-out !important;
        transition: 0.2s ease;
    }
    .checkout-floating-btn:hover {
        animation: none !important;
        background: {{ $primaryColor }};
        filter: brightness(0.9);
        transform: translateY(-2px);
    }
    @media (max-width: 768px) {
        .checkout-floating-bar {
            padding: 6px 12px;
        }
        .checkout-floating-amount {
            font-size: 15px;
        }
        .checkout-floating-btn {
            padding: 8px 14px;
            font-size: 13.5px;
            flex: 1;
            max-width: 220px;
        }
    }

    /* --- OUTLINED / NOTCHED BORDER LABEL FORM STYLING (TIGHT & COMPACT) --- */
    .modern-outline-group {
        position: relative;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .modern-outline-group .modern-outline-label {
        position: absolute;
        top: -8px;
        left: 10px;
        background: #ffffff;
        padding: 0 5px;
        font-size: 11.5px;
        font-weight: 600;
        color: #374151;
        z-index: 2;
        pointer-events: none;
        line-height: 1.2;
        border-radius: 2px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    .modern-outline-group .modern-outline-input,
    .modern-outline-group .form-control-custom,
    .modern-outline-group .form-control {
        width: 100%;
        min-height: 40px;
        height: 40px;
        padding: 6px 12px;
        background: #ffffff;
        border: 1.5px solid #d1d5db !important;
        border-radius: 6px !important;
        font-size: 13.5px;
        color: #111827;
        outline: none !important;
        transition: all 0.2s ease;
        box-sizing: border-box;
        box-shadow: none !important;
    }
    .modern-outline-group .modern-outline-input:focus,
    .modern-outline-group .form-control-custom:focus,
    .modern-outline-group .form-control:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12) !important;
    }
    .modern-outline-group textarea.modern-outline-input,
    .modern-outline-group textarea.form-control-custom,
    .modern-outline-group textarea.form-control {
        min-height: 56px;
        padding-top: 8px;
        resize: vertical;
    }
    .modern-outline-group input::placeholder,
    .modern-outline-group textarea::placeholder,
    .modern-outline-group .form-control-custom::placeholder,
    .modern-outline-group .form-control::placeholder {
        color: #9ca3af !important;
        opacity: 0.55 !important;
        font-size: 13px !important;
        font-weight: 400 !important;
    }
    .modern-outline-group input:focus::placeholder,
    .modern-outline-group textarea:focus::placeholder,
    .modern-outline-group .form-control-custom:focus::placeholder,
    .modern-outline-group .form-control:focus::placeholder,
    .checkout-section input:focus::placeholder,
    .checkout-section textarea:focus::placeholder {
        color: transparent !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    /* Order note collapsible button */
    .order-note-collapse-wrapper {
        margin-top: 2px;
        margin-bottom: 4px;
    }
    .order-note-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #4f46e5;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        background: transparent;
        border: none;
        padding: 2px 0;
        transition: color 0.15s ease;
    }
    .order-note-toggle-btn:hover {
        color: #3730a3;
        text-decoration: underline;
    }
    .order-note-toggle-btn i {
        font-size: 13px;
    }

    /* --- Responsive Fixes --- */
    @media (max-width: 991.98px) {
        body.checkout-page #content {
            padding-top: calc(var(--navbar-height, {{ $checkoutMobilePadTop }}) + 12px) !important;
        }

        .checkout-section {
            padding: 4px 0 20px;
        }

        .checkout-layout-row {
            display: flex !important;
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0 !important;
            --bs-gutter-x: 0 !important;
            --bs-gutter-y: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .checkout-form-col {
            order: 0 !important;
            flex: 0 0 auto !important;
            width: 100% !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .checkout-form-col .checkout-card {
            margin-bottom: 6px !important;
        }

        .checkout-summary-col {
            order: 0 !important;
            flex: 0 0 auto !important;
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            display: block !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding-top: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .checkout-summary-col .checkout-card {
            margin-top: 0 !important;
            margin-bottom: 6px !important;
        }

        .checkout-summary-placeholder {
            display: none !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .checkout-summary-sticky.is-fixed,
        .checkout-summary-sticky.is-abs-bottom {
            position: static !important;
            top: auto !important;
            left: auto !important;
            width: 100% !important;
        }

        .mobile-submit-btn { display: none !important; }
        .desktop-submit-btn { display: block !important; }
    }
    @media (min-width: 992px) {
        .mobile-submit-btn { display: none !important; }
        .desktop-submit-btn { display: block !important; }
    }
</style>
@endpush

@section('content')
<script>document.documentElement.classList.add('checkout-page');document.body.classList.add('checkout-page');</script>
<section class="checkout-section">
    @php
        // ==============================================================
        //  PHP LOGIC: CART, SHIPPING, DISCOUNT, ADVANCE (UNCHANGED)
        // ==============================================================
        $subtotal = Cart::instance('shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $subtotal = (float) $subtotal;

        // ✅ শিপিং লজিক চেক
        $requires_shipping = false;
        foreach (Cart::instance('shopping')->content() as $item) {
            $product = \App\Models\Product::find($item->id);
            if ($product && $product->is_digital != 1) {
                $requires_shipping = true;
                break;
            }
        }

        // ✅ শিপিং চার্জ সেট
        // ⭐ Free Delivery Check - যদি সব প্রোডাক্ট free delivery eligible হয়, shipping charge 0
        $hasAllFreeDelivery = \App\Http\Controllers\Frontend\ShoppingController::hasAllFreeDeliveryProducts();
        
        if ($requires_shipping && !$hasAllFreeDelivery) {
            $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
        } else {
            $shipping = 0;
            Session::put('shipping', 0);
        }

        $discount = Session::get('discount', 0);
        // ⭐ Grand Total Calculation - Free delivery হলে shipping charge 0
        $grand_total = $subtotal + $shipping - $discount;

        // ✅ JS ডেটা অ্যারে (Eager load to avoid N+1 queries)
        $cartContent = Cart::instance('shopping')->content();
        $cartProductIds = $cartContent->pluck('id')->unique()->filter()->all();
        $cartProductsMap = !empty($cartProductIds) 
            ? \App\Models\Product::select('id', 'is_digital', 'free_delivery')->whereIn('id', $cartProductIds)->get()->keyBy('id')
            : collect();

        $cartItemsForJs = [];
        $hasDigital = false;
        foreach ($cartContent as $item) {
            $p = $cartProductsMap->get($item->id);
            if ($p && $p->is_digital == 1) { $hasDigital = true; }
            $cartItemsForJs[] = [
                'rowId'             => $item->rowId,
                'id'                => $item->id,
                'name'              => $item->name,
                'qty'               => $item->qty,
                'price'             => (float) $item->price,
                'image'             => asset($item->options->image ?? ''),
                'link'              => isset($item->options->slug) ? url('/product/'.$item->options->slug) : '#',
                'is_digital'        => (int) ($p->is_digital ?? 0),
                'free_delivery'     => (int) ($p->free_delivery ?? 0),
                'color_id'          => $item->options->color_id ?? null,
                'size_id'           => $item->options->size_id ?? null,
                'variant_price_id'  => $item->options->variant_price_id ?? null,
            ];
        }

        // ✅ Advance Logic
        $advance_amount = \App\Http\Controllers\Frontend\ShoppingController::getCartAdvanceAmount();
        $hasAdvance     = $advance_amount > 0 ? true : false;
        $payable_now    = $hasAdvance ? $advance_amount : $grand_total;
        $due_amount     = $hasAdvance ? ($grand_total - $advance_amount) : 0;

        $__gsCheckoutOtp = \App\Models\GeneralSetting::where('status', 1)->first();
        $__custCheckoutOtpPending = session('chkotp_customer_pending');
        $__checkoutOtpDraft = session('chkotp_customer_draft', []);
        $__showCheckoutOtpModal = $__gsCheckoutOtp && ($__gsCheckoutOtp->checkout_otp_enabled ?? 0) == 1 && $__custCheckoutOtpPending;
        $__otpFieldValue = session('checkout_otp_cleared') ? '' : old('checkout_otp');
    @endphp

    <div class="container">
        {{-- মেইন ফর্ম --}}
        <form id="checkout-form" action="{{ route('customer.ordersave') }}" method="POST" data-parsley-validate="">
            @csrf
            <input type="hidden" name="checkout_otp" id="checkout_otp_hidden" value="{{ $__otpFieldValue }}">
            @if(!empty($__showCheckoutOtpModal) && !empty($__checkoutOtpDraft))
                @foreach(['division_id', 'district_id', 'upazila_id', 'payment_method', 'manual_trx_id', 'manual_sender_number', 'order_note'] as $__draftKey)
                    @if(!empty($__checkoutOtpDraft[$__draftKey]))
                        <input type="hidden" name="{{ $__draftKey }}" value="{{ $__checkoutOtpDraft[$__draftKey] }}">
                    @endif
                @endforeach
            @endif
            {{-- Traffic: সার্ভার সেশন (referrer/fbclid) + ব্রাউজার sessionStorage --}}
            <input type="hidden" name="traffic_source" id="inp_ts" value="{{ old('traffic_source', session('order_traffic_source', 'direct')) }}">
            <input type="hidden" name="traffic_referrer" id="inp_tsr" value="{{ old('traffic_referrer', session('order_traffic_referrer', '')) }}">
            <script>
            try {
                var elTs = document.getElementById('inp_ts');
                var elTsr = document.getElementById('inp_tsr');
                var ts = sessionStorage.getItem('_ts');
                var tsr = sessionStorage.getItem('_tsr');
                if (ts !== null && ts !== '') {
                    elTs.value = ts;
                } else if (elTs.value && elTs.value !== 'direct') {
                    sessionStorage.setItem('_ts', elTs.value);
                }
                if (tsr !== null && tsr !== '') {
                    elTsr.value = tsr;
                } else if (elTsr.value) {
                    sessionStorage.setItem('_tsr', elTsr.value);
                }
            } catch (e) {}
            </script>
            
            <div class="row checkout-layout-row g-3" id="checkoutLayoutRow">
                
                {{-- LEFT COLUMN: Shipping & Payment --}}
                <div class="col-lg-7 col-md-12 checkout-form-col">
                    
                    {{-- 1. SHIPPING INFO CARD --}}
                    <div class="checkout-card">
                        <div class="checkout-header">
                            <i class="fas fa-truck-moving"></i>
                            <h6>শিপিং এবং বিলিং তথ্য</h6>
                        </div>
                        <div class="card-body-custom" style="padding: 10px 14px;">
                            <div class="row g-2">
                                {{-- ১. আপনার নাম * --}}
                                <div class="col-md-6 col-12">
                                    <div class="modern-outline-group">
                                        <label class="modern-outline-label">আপনার নাম <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="modern-outline-input form-control-custom" 
                                            value="{{ Auth::guard('customer')->user()->name ?? old('name') }}" placeholder="আপনার সম্পূর্ণ নাম লিখুন" required>
                                    </div>
                                </div>

                                {{-- ২. মোবাইল নাম্বার * --}}
                                <div class="col-md-6 col-12">
                                    <div class="modern-outline-group">
                                        <label class="modern-outline-label">মোবাইল নাম্বার <span class="text-danger">*</span></label>
                                        <input type="tel" name="phone" id="phone" class="modern-outline-input form-control-custom" minlength="11" maxlength="11" pattern="0[0-9]+" 
                                            value="{{ Auth::guard('customer')->user()->phone ?? old('phone') }}" placeholder="01XXXXXXXXX" required>
                                    </div>
                                </div>

                                @if($requires_shipping)
                                    {{-- ৩. ডেলিভারি এরিয়া * (3-in-1 modal) --}}
                                    <div class="col-12">
                                        @include('frontEnd.layouts.partials.delivery_location_modal', [
                                            'prefix' => 'checkout',
                                            'fieldLabel' => 'ডেলিভারি এরিয়া',
                                            'divisions' => $divisions,
                                            'selectedDivisionId' => old('division_id', Auth::guard('customer')->user()->division_id ?? null),
                                            'selectedDistrictId' => old('district_id', Auth::guard('customer')->user()->district_id ?? null),
                                            'selectedUpazilaId'  => old('upazila_id', Auth::guard('customer')->user()->upazila_id ?? null)
                                        ])
                                    </div>

                                    {{-- ৪. ডেলিভারির স্থান --}}
                                    <div class="col-12">
                                        <div class="modern-outline-group">
                                            <label class="modern-outline-label">ডেলিভারির স্থান</label>
                                            <input type="text" name="address" id="checkout_detailed_address" class="modern-outline-input form-control-custom" 
                                                value="{{ Auth::guard('customer')->user()->address ?? old('address') }}" 
                                                placeholder="বাসা নং, রোড নং, এলাকা ইত্যাদি (ঐচ্ছিক)">
                                        </div>
                                    </div>

                                    {{-- ডেলিভারি এরিয়া / চার্জ * (UI থেকে রিমুভ, ব্যাকএন্ড ও শিপিং সিঙ্কের জন্য হিডেন) --}}
                                    <div style="display: none !important;">
                                        <select name="area" id="checkout_area">
                                            @foreach(($shippingcharges ?? collect()) as $sc)
                                                <option value="{{ $sc->id }}" data-charge="{{ $sc->amount }}" {{ $loop->first ? 'selected' : '' }}>{{ $sc->name }} (৳{{ round($sc->amount) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <div class="modern-outline-group">
                                            <label class="modern-outline-label">ডেলিভারির স্থান</label>
                                            <input type="text" class="modern-outline-input form-control-custom" value="ডিজিটাল / ফ্রি শিপিং — লোকেশন লাগবে না" readonly disabled style="background:#f3f4f6;">
                                            <input type="hidden" name="division_id" value="">
                                            <input type="hidden" name="district_id" value="">
                                            <input type="hidden" name="upazila_id" value="">
                                        </div>
                                    </div>
                                @endif

                                {{-- ৫. + অর্ডার নোট (কোল্যাপসিবল) --}}
                                <div class="col-12">
                                    <div class="order-note-collapse-wrapper">
                                        <button type="button" class="order-note-toggle-btn" id="toggle_checkout_order_note">
                                            <i class="fas {{ old('order_note', $order_note ?? '') ? 'fa-minus-circle' : 'fa-plus-circle' }}" id="checkout_note_icon"></i>
                                            <span id="checkout_note_text">{{ old('order_note', $order_note ?? '') ? 'অর্ডার নোট বন্ধ করুন' : 'অর্ডার নোট' }}</span>
                                        </button>
                                        <div id="checkout_note_collapse_box" class="modern-outline-group mt-2" style="{{ old('order_note', $order_note ?? '') ? '' : 'display: none;' }}">
                                            <label class="modern-outline-label">অর্ডার নোট</label>
                                            <textarea name="order_note" id="order_note" class="modern-outline-input form-control-custom" rows="2" 
                                                placeholder="অর্ডার সম্পর্কে কোনো বিশেষ নির্দেশনা থাকলে লিখুন...">{{ old('order_note', $order_note ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT SIDE: ORDER SUMMARY (WITH INTEGRATED PAYMENT METHODS) --}}
                <div class="col-lg-5 col-md-12 checkout-summary-col" id="checkoutSummaryCol">
                    <div class="checkout-summary-sticky" id="checkoutSummarySticky">
                        <div class="checkout-card checkout-summary-card">
                            <div class="checkout-header">
                                <i class="fas fa-shopping-bag"></i>
                                <h6>অর্ডার সামারি ({{ Cart::instance('shopping')->count() }}টি পণ্য)</h6>
                            </div>
                            
                            <div class="card-body-custom p-0">
                                {{-- Products List (Scrollable) --}}
                                <div class="cart-items-scroll px-3 pt-2 cartlist {{ Cart::instance('shopping')->count() ? '' : 'is-empty' }}" style="overflow-y: auto;">
                                    @foreach (Cart::instance('shopping')->content() as $value)
                                        <div class="checkout-item" data-rowid="{{ $value->rowId }}">
                                            {{-- Remove --}}
                                            <a class="remove-item-btn cart_remove" data-id="{{ $value->rowId }}" title="Remove Item">
                                                <i class="far fa-trash-alt"></i>
                                            </a>

                                            {{-- Image --}}
                                            <a href="{{ route('product', $value->options->slug) }}">
                                                <img src="{{ asset($value->options->image) }}" class="checkout-pro-img">
                                            </a>

                                            {{-- Info --}}
                                            <div class="checkout-pro-info flex-grow-1">
                                                <a href="{{ route('product', $value->options->slug) }}" class="text-dark text-decoration-none">
                                                    <h6>{{ Str::limit($value->name, 35) }}</h6>
                                                </a>
                                                @if(!empty($value->options->product_size) || !empty($value->options->product_color))
                                                    <div class="checkout-variant-badges my-1 d-flex flex-wrap gap-1">
                                                        @if(!empty($value->options->product_size))
                                                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 11.5px; padding: 2px 7px; border-radius: 4px;">সাইজ: {{ $value->options->product_size }}</span>
                                                        @endif
                                                        @if(!empty($value->options->product_color))
                                                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 11.5px; padding: 2px 7px; border-radius: 4px;">কালার: {{ $value->options->product_color }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                {{-- Price & Qty --}}
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="qty-box checkout-qty" data-rowid="{{ $value->rowId }}">
                                                        <button type="button" class="qty-btn minus"><i class="fas fa-minus" style="font-size:10px;"></i></button>
                                                        <span class="qty-val qty-value">{{ $value->qty }}</span>
                                                        <button type="button" class="qty-btn plus"><i class="fas fa-plus" style="font-size:10px;"></i></button>
                                                    </div>
                                                    <div class="fw-bold text-dark item-total-price">৳ {{ number_format($value->price * $value->qty, 0) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- COUPON SECTION --}}
@php
    $todayDate = date('Y-m-d');
    $hasActiveCoupon = \App\Models\Coupon::where('status', 1)
        ->where(function($q) use ($todayDate) {
            $q->whereNull('valid_from')->orWhere('valid_from', '<=', $todayDate);
        })
        ->where(function($q) use ($todayDate) {
            $q->whereNull('valid_to')->orWhere('valid_to', '>=', $todayDate);
        })
        ->exists();
@endphp

@if($hasActiveCoupon || Session::has('coupon_code'))
<div class="coupon-wrapper">
    @if(!Session::has('coupon_code'))
        <div class="coupon-group-modern">
            <input type="text" id="coupon_input" class="coupon-input-modern" placeholder="কুপন কোড লিখুন..." autocomplete="off" onkeydown="if(event.key==='Enter'){event.preventDefault();submitCoupon();}">
            <button type="button" class="coupon-btn-modern" onclick="submitCoupon()">APPLY</button>
        </div>
    @else
        <div class="coupon-applied-box">
            <span class="coupon-applied-text"><i class="fas fa-check-circle text-success me-1"></i> কুপন <b>{{ Session::get('coupon_code') }}</b> যুক্ত হয়েছে</span>
            <a href="{{ route('coupon.remove') }}" class="coupon-remove-btn" title="কুপন বাতিল করুন">✕ মুছুন</a>
        </div>
    @endif
</div>
@endif

                                {{-- Calculation --}}
                                <div class="summary-totals">
                                    <div class="total-row"><span>সাবটোটাল</span> <span id="subtotalAmount">৳ {{ number_format($subtotal, 0) }}</span></div>
                                    <div class="total-row"><span>ডেলিভারি চার্জ</span> <span id="shippingAmount">৳ {{ number_format($shipping, 0) }}</span></div>
                                    @if($discount > 0)
                                        <div class="total-row text-success"><span>কুপন ছাড়</span> <span id="discountAmount">- ৳ {{ number_format($discount, 0) }}</span></div>
                                    @endif
                                    <div class="total-row final"><span>সর্বমোট</span> <span id="grandTotalAmount">৳ {{ number_format($grand_total, 0) }}</span></div>

                                    @if($hasAdvance)
                                        <div class="advance-alert">
                                            <div class="total-row text-success fw-bold"><span>অগ্রিম (পেইড):</span> <span id="advanceAmountCell">৳ {{ number_format($advance_amount, 0) }}</span></div>
                                            <div class="total-row text-danger fw-bold mb-0"><span>বাকি (ডিউ):</span> <span id="dueAmountCell">৳ {{ number_format($due_amount, 0) }}</span></div>
                                        </div>
                                    @endif
                                </div>

                                {{-- PAYMENT METHODS (Directly below summary totals without separate label) --}}
                                <div class="px-3 pb-1 border-top pt-2">
                                    @if($hasAdvance)
                                        <div class="alert alert-warning border-0 shadow-sm mb-3" style="border-left: 5px solid #ffc107 !important; background-color: #fff8e1;">
                                            <div class="d-flex gap-3 align-items-center">
                                                <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                                                <div>
                                                    <strong>অগ্রিম পেমেন্ট প্রয়োজন!</strong>
                                                    <p class="mb-0 small">এই অর্ডারে <b>৳ {{ number_format($advance_amount, 0) }}</b> অগ্রিম পেমেন্ট করতে হবে।</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="payment-options-list">
                                        {{-- COD Option --}}
                                        @if(!$hasDigital && !$hasAdvance)
                                            <label class="payment-option-label">
                                                <input type="radio" name="payment_method" value="cod" checked required>
                                                <div class="payment-content">
                                                    <div class="text-center" style="width: 36px;"><i class="fas fa-truck text-success fs-3"></i></div>
                                                    <div class="pay-info">
                                                        <strong>Cash On Delivery</strong>
                                                        <small>পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন</small>
                                                    </div>
                                                </div>
                                                <div class="check-circle"></div>
                                            </label>
                                        @endif

                                        {{-- Bkash --}}
                                        @if($bkash_gateway)
                                            <label class="payment-option-label">
                                                <input type="radio" name="payment_method" value="bkash" required> 
                                                <div class="payment-content">
                                                    <img src="{{ asset('public/frontEnd/images/bkash.svg') }}" class="pay-logo" alt="bKash">
                                                    <div class="pay-info">
                                                        <strong>bKash Payment</strong>
                                                        <small>বিকাশ অ্যাপ বা গেটওয়ে দ্বারা পেমেন্ট</small>
                                                    </div>
                                                </div>
                                                <div class="check-circle"></div>
                                            </label>
                                        @endif

                                        {{-- ShurjoPay --}}
                                        @if($shurjopay_gateway)
                                            <label class="payment-option-label">
                                                <input type="radio" name="payment_method" value="shurjopay" required>
                                                <div class="payment-content">
                                                    <img src="{{ asset('public/frontEnd/images/shurjoPay.png') }}" class="pay-logo" alt="ShurjoPay">
                                                    <div class="pay-info">
                                                        <strong>Online Payment</strong>
                                                        <small>ShurjoPay (Card/Mobile Banking)</small>
                                                    </div>
                                                </div>
                                                <div class="check-circle"></div>
                                            </label>
                                        @endif

                                        {{-- UddoktaPay --}}
                                        @if($uddoktapay_gateway)
                                            <label class="payment-option-label">
                                                <input type="radio" name="payment_method" value="uddoktapay" required>
                                                <div class="payment-content">
                                                    <img src="{{ asset('public/frontEnd/images/uddokta.png') }}" class="pay-logo" alt="UddoktaPay">
                                                    <div class="pay-info">
                                                        <strong>UddoktaPay</strong>
                                                        <small>মোবাইল ব্যাংকিং পেমেন্ট গেটওয়ে</small>
                                                    </div>
                                                </div>
                                                <div class="check-circle"></div>
                                            </label>
                                        @endif

                                        {{-- aamarPay --}}
                                        @if($aamarpay_gateway)
                                            <label class="payment-option-label">
                                                <input type="radio" name="payment_method" value="aamarpay" required>
                                                <div class="payment-content">
                                                    <img src="{{ asset('public/frontEnd/images/aamarpay.png') }}" class="pay-logo" alt="aamarPay" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="pay-info" style="display: none;">
                                                        <i class="fas fa-credit-card text-primary fs-4"></i>
                                                    </div>
                                                    <div class="pay-info">
                                                        <strong>aamarPay</strong>
                                                        <small>কার্ড ও মোবাইল ব্যাংকিং পেমেন্ট</small>
                                                    </div>
                                                </div>
                                                <div class="check-circle"></div>
                                            </label>
                                        @endif

                                        @foreach($manual_gateways ?? [] as $mg)
                                            <label class="payment-option-label">
                                                <input type="radio" name="payment_method" value="manual_{{ $mg->id }}" required>
                                                <div class="payment-content">
                                                    @if($mg->logo_asset_url)
                                                        <img src="{{ $mg->logo_asset_url }}" class="pay-logo" alt="{{ $mg->title }}">
                                                    @else
                                                        <div class="text-center" style="width: 36px;"><i class="fas fa-money-check-alt text-primary fs-3"></i></div>
                                                    @endif
                                                    <div class="pay-info">
                                                        <strong>{{ $mg->title }}</strong>
                                                        <small>ম্যানুয়াল পেমেন্ট — ট্রানজেকশন আইডি দিন</small>
                                                    </div>
                                                </div>
                                                <div class="check-circle"></div>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div id="manual-payment-fields" class="mt-3 p-3 rounded-3 border border-warning bg-light" style="display:none;">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-info-circle text-warning me-1"></i> ম্যানুয়াল পেমেন্ট নির্দেশনা</h6>
                                        <div id="manual-instructions-body" class="small text-secondary mb-3" style="white-space:pre-wrap;"></div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">ট্রানজেকশন আইডি / রেফারেন্স <span class="text-danger">*</span></label>
                                                <input type="text" name="manual_trx_id" id="manual_trx_id" class="form-control form-control-custom" value="{{ old('manual_trx_id') }}" maxlength="55" placeholder="TrxID">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">যে নম্বর থেকে পাঠিয়েছেন (ঐচ্ছিক)</label>
                                                <input type="text" name="manual_sender_number" class="form-control form-control-custom" value="{{ old('manual_sender_number') }}" maxlength="55" placeholder="01xxx">
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        window.MANUAL_GATEWAYS = @json(($manual_gateways ?? collect())->map(fn ($g) => [
                                            'code' => 'manual_'.$g->id,
                                            'instructions' => (string) ($g->instructions ?? ''),
                                        ])->values()->all());
                                    </script>

                                    <div id="payment-error" class="text-danger fw-bold mt-2 text-center" style="display:none;">
                                        <i class="fas fa-exclamation-circle"></i> অনুগ্রহ করে একটি পেমেন্ট মেথড সিলেক্ট করুন।
                                    </div>
                                </div>

                                {{-- SUBMIT BUTTON (VISIBLE ON ALL SCREENS) --}}
                                <div class="checkout-submit-wrap p-3 pt-2">
                                    <button type="submit" class="btn-place-order">
                                        অর্ডার নিশ্চিত করুন <i class="fas fa-check-circle"></i>
                                    </button>
                                    <div class="text-center text-muted small mt-3">
                                        <i class="fas fa-lock"></i> ১০০% নিরাপদ চেকআউট প্রসেস
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        @if(!empty($__showCheckoutOtpModal))
        <form id="checkout_otp_resend_form" method="POST" action="{{ route('customer.ordersave') }}" class="d-none">
            @csrf
            <input type="hidden" name="checkout_otp_resend" value="1">
        </form>
        <div class="modal fade" id="checkoutOtpModal" tabindex="-1" aria-labelledby="checkoutOtpModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg overflow-hidden">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg,#0f3460,#e94560);">
                        <h5 class="modal-title d-flex align-items-center gap-2 mb-0" id="checkoutOtpModalLabel">
                            <i class="fas fa-mobile-alt"></i> OTP ভেরিফিকেশন
                        </h5>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">আপনার মোবাইল নম্বরে একটি <strong>৬ ডিজিটের OTP</strong> এসএমএসে পাঠানো হয়েছে। কোডটি লিখে নিচের বাটনে চাপ দিন।</p>
                        @error('checkout_otp')
                            <div class="alert alert-danger py-2 small mb-3">{{ $message }}</div>
                        @enderror
                        <label class="form-label fw-semibold">OTP কোড</label>
                        <input type="text" id="checkout_otp_modal_field" class="form-control form-control-lg text-center letter-spacing-wide" maxlength="6"
                            inputmode="numeric" autocomplete="one-time-code" placeholder="● ● ● ● ● ●" style="letter-spacing: 0.35em;"
                            value="{{ $__otpFieldValue }}">
                        <div class="d-flex flex-wrap gap-2 mt-4 justify-content-between align-items-center">
                            <button type="submit" form="checkout_otp_resend_form" class="btn btn-outline-secondary btn-sm" id="checkout_otp_resend_btn">OTP আবার পাঠান</button>
                            <button type="button" class="btn btn-success px-4 fw-bold" id="checkout_otp_confirm_btn">অর্ডার সম্পূর্ণ করুন</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    {{-- 🚀 FLOATING STICKY ORDER BAR FOR CHECKOUT --}}
    <div id="checkout_floating_bar" class="checkout-floating-bar">
        <div class="checkout-floating-container">
            <div class="checkout-floating-total">
                <span class="checkout-floating-label">সর্বমোট বিল</span>
                <span class="checkout-floating-amount" id="checkoutFloatingGrandTotal">৳ {{ number_format($grand_total, 0) }}</span>
            </div>
            <button type="button" class="checkout-floating-btn" id="checkout_floating_submit_btn">
                অর্ডার নিশ্চিত করুন <i class="fas fa-arrow-right ms-1"></i>
            </button>
        </div>
    </div>

    </div>
</section>
@endsection

@push('script')
<script src="{{ asset('public/frontEnd/js/select2.min.js') }}"></script>

<script>
(function ($) {
    var stickyEl, summaryCol, layoutRow, placeholder, ticking = false;
    var stickyDocTop = 0;

    function headerOffset() {
        var header = document.querySelector('header');
        return (header ? header.offsetHeight : 120) + 12;
    }

    function scrollY() {
        return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    }

    function measure() {
        if (!stickyEl) return;
        stickyEl.classList.remove('is-fixed', 'is-abs-bottom');
        stickyEl.style.top = '';
        stickyEl.style.left = '';
        stickyEl.style.width = '';
        var rect = stickyEl.getBoundingClientRect();
        stickyDocTop = rect.top + scrollY();
    }

    function resetSticky() {
        if (!stickyEl) return;
        stickyEl.classList.remove('is-fixed', 'is-abs-bottom');
        stickyEl.style.top = '';
        stickyEl.style.left = '';
        stickyEl.style.width = '';
        if (placeholder) placeholder.style.display = 'none';
    }

    function updateSticky() {
        ticking = false;
        if (!stickyEl || !summaryCol || !layoutRow || window.innerWidth < 992) {
            resetSticky();
            return;
        }

        var top = headerOffset();
        var sy = scrollY();
        var stickyH = stickyEl.offsetHeight;
        var rowRect = layoutRow.getBoundingClientRect();
        var rowDocTop = rowRect.top + sy;
        var rowDocBottom = rowDocTop + layoutRow.offsetHeight;
        var colRect = summaryCol.getBoundingClientRect();
        var fixStart = stickyDocTop - top;
        var fixEnd = rowDocBottom - stickyH - top;

        if (sy < fixStart) {
            resetSticky();
            return;
        }

        if (placeholder) {
            placeholder.style.display = 'block';
            placeholder.style.height = stickyH + 'px';
        }

        if (sy >= fixEnd) {
            stickyEl.classList.remove('is-fixed');
            stickyEl.classList.add('is-abs-bottom');
            stickyEl.style.top = '';
            stickyEl.style.left = '';
            stickyEl.style.width = '';
            return;
        }

        stickyEl.classList.remove('is-abs-bottom');
        stickyEl.classList.add('is-fixed');
        stickyEl.style.top = top + 'px';
        stickyEl.style.left = colRect.left + 'px';
        stickyEl.style.width = colRect.width + 'px';
    }

    function onScrollOrResize() {
        if (!ticking) {
            ticking = true;
            window.requestAnimationFrame(updateSticky);
        }
    }

    function initCheckoutSticky() {
        stickyEl = document.getElementById('checkoutSummarySticky');
        summaryCol = document.getElementById('checkoutSummaryCol');
        layoutRow = document.getElementById('checkoutLayoutRow');
        if (!stickyEl || !summaryCol || !layoutRow) return;

        if (window.innerWidth >= 992 && !placeholder) {
            placeholder = document.createElement('div');
            placeholder.className = 'checkout-summary-placeholder';
            stickyEl.parentNode.insertBefore(placeholder, stickyEl.nextSibling);
        }

        measure();
        updateSticky();
    }

    function bindEvents() {
        $(window).on('scroll.checkoutSticky', onScrollOrResize);
        $(window).on('resize.checkoutSticky', function () {
            if (window.innerWidth < 992 && placeholder) {
                placeholder.style.display = 'none';
            }
            measure();
            onScrollOrResize();
        });
        $(window).on('load.checkoutSticky', function () {
            measure();
            updateSticky();
        });
    }

    $(function () {
        initCheckoutSticky();
        bindEvents();
    });
})(jQuery);
</script>

@if(!empty($__showCheckoutOtpModal))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('checkoutOtpModal');
    if (!modalEl) return;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false }).show();
    } else if (window.jQuery && jQuery.fn.modal) {
        jQuery(modalEl).modal({ backdrop: 'static', keyboard: false });
        jQuery(modalEl).modal('show');
    }
    var modalInput = document.getElementById('checkout_otp_modal_field');
    if (modalInput) {
        setTimeout(function () { modalInput.focus(); }, 400);
    }
    var checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.querySelectorAll('input[name="payment_method"]').forEach(function (el) {
            el.disabled = true;
        });
        ['checkout_division', 'checkout_district', 'checkout_upazila'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.disabled = true;
        });
    }
    document.getElementById('checkout_otp_confirm_btn').addEventListener('click', function () {
        window.__checkoutOtpSkipCancel = true;
        var raw = modalInput ? modalInput.value : '';
        var otp = raw.replace(/\D/g, '').slice(0, 6);
        if (otp.length !== 6) {
            if (typeof toastr !== 'undefined') {
                toastr.error('৬ ডিজিটের OTP কোড লিখুন।', 'Error');
            } else {
                alert('৬ ডিজিটের OTP কোড লিখুন।');
            }
            if (modalInput) modalInput.focus();
            return;
        }
        document.getElementById('checkout_otp_hidden').value = otp;
        var form = document.getElementById('checkout-form');
        if (!form) return;
        if (typeof isSubmitting !== 'undefined') isSubmitting = true;
        if (typeof checkoutOtpPending !== 'undefined') checkoutOtpPending = false;
        form.submit();
    });
});
</script>
@endif

{{-- ============================================================== --}}
{{--  JAVASCRIPT LOGIC (EXACT COPY - NO FUNCTIONALITY REMOVED)  --}}
{{-- ============================================================== --}}

        {{-- ========================================================= --}}
        {{--  🔴 এই অংশটুকু আপনার কোডে মিসিং ছিল, তাই কাজ করছিল না   --}}
        {{-- ========================================================= --}}
        
        {{-- হিডেন কুপন ফর্ম (এটি অবশ্যই মেইন ফর্মের বাইরে থাকতে হবে) --}}
        <form id="coupon-form" action="{{ route('coupon.apply') }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="coupon_code" id="hidden_coupon_code">
        </form>

        {{-- কুপন সাবমিট করার জাভাস্ক্রিপ্ট --}}
        <script>
            function submitCoupon() {
                var code = document.getElementById('coupon_input').value;
                if(code) {
                    document.getElementById('hidden_coupon_code').value = code;
                    document.getElementById('coupon-form').submit();
                } else {
                    // টোস্টার থাকলে টোস্টার, নাহলে এলার্ট
                    if(typeof toastr !== 'undefined') {
                        toastr.error('Please enter a coupon code');
                    } else {
                        alert('Please enter a coupon code');
                    }
                }
            }
        </script>
<script>
    // গ্লোবাল ভেরিয়েবল (Global Variables)
    let incompleteOrderTimer;
    let isSubmitting = false; // অর্ডার সাবমিট হচ্ছে কিনা তা চেক করার জন্য
    let checkoutOtpPending = @json((bool) ($__custCheckoutOtpPending ?? false));

    $(document).ready(function() {
        // Select2 Initialize
        $(".select2").select2({ width: '100%' });

        // ==========================================
        // 1. CART LOGIC (REMOVE, INCREASE, DECREASE)
        // ==========================================
        
        let cartUpdating = false;

        // Remove Item smoothly
        $(document).on('click', '.cart_remove', function(e) {
            e.preventDefault(); e.stopImmediatePropagation();
            if (cartUpdating) return;
            var id = $(this).data("id");
            var $itemRow = $(this).closest('.checkout-item');

            if (id) {
                cartUpdating = true;
                $itemRow.css({ 'opacity': '0.4', 'pointer-events': 'none' });
                $.ajax({
                    type: "GET",
                    url: "{{ route('cart.remove') }}",
                    data: { id: id },
                    dataType: 'json',
                    headers: { 'Accept': 'application/json' },
                    success: function(res) {
                        cartUpdating = false;
                        if (res && res.success) {
                            if (res.isEmpty || res.count === 0) {
                                window.location.reload();
                                return;
                            }
                            $itemRow.slideUp(250, function() {
                                $(this).remove();
                            });
                            cartItems = cartItems.filter(function(it) { return it.rowId !== id; });
                            baseSubtotal = parseFloat(res.subtotal) || 0;
                            $('#subtotalAmount').text('৳ ' + Math.round(baseSubtotal));
                            applyShippingToDomAndSession();
                            if (typeof cart_count === 'function') cart_count();
                            if (typeof mobile_cart === 'function') mobile_cart();
                            if (typeof toastr !== 'undefined') {
                                toastr.success('আইটেমটি কার্ট থেকে সরানো হয়েছে', 'সফল');
                            }
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function() {
                        cartUpdating = false;
                        $itemRow.css({ 'opacity': '1', 'pointer-events': 'auto' });
                        window.location.reload();
                    }
                });
            }
        });

        // Quantity Increment smoothly
        $(document).on('click', '.checkout-qty .plus', function(e) {
            e.preventDefault();
            if (cartUpdating) return;
            var $btn = $(this);
            var $qtyBox = $btn.closest('.checkout-qty');
            var $itemRow = $qtyBox.closest('.checkout-item');
            var rowId = $qtyBox.data('rowid');

            cartUpdating = true;
            $qtyBox.css('opacity', '0.5');

            $.ajax({
                type: "GET",
                url: "{{ route('cart.increment') }}",
                data: { id: rowId },
                dataType: 'json',
                headers: { 'Accept': 'application/json' },
                success: function(res) {
                    cartUpdating = false;
                    $qtyBox.css('opacity', '1');
                    if (res && res.success) {
                        $qtyBox.find('.qty-val').text(res.item_qty);
                        $itemRow.find('.item-total-price').text('৳ ' + Math.round(res.item_total).toLocaleString());
                        baseSubtotal = parseFloat(res.subtotal) || 0;
                        $('#subtotalAmount').text('৳ ' + Math.round(baseSubtotal));

                        var foundItem = cartItems.find(function(it) { return it.rowId === rowId; });
                        if (foundItem) { foundItem.qty = res.item_qty; }

                        applyShippingToDomAndSession();
                        if (typeof cart_count === 'function') cart_count();
                        if (typeof mobile_cart === 'function') mobile_cart();
                    } else {
                        window.location.reload();
                    }
                },
                error: function() {
                    cartUpdating = false;
                    $qtyBox.css('opacity', '1');
                    window.location.reload();
                }
            });
        });

        // Quantity Decrement smoothly
        $(document).on('click', '.checkout-qty .minus', function(e) {
            e.preventDefault();
            if (cartUpdating) return;
            var $btn = $(this);
            var $qtyBox = $btn.closest('.checkout-qty');
            var $itemRow = $qtyBox.closest('.checkout-item');
            var currentVal = parseInt($qtyBox.find('.qty-val').text()) || 1;
            if (currentVal <= 1) return;

            var rowId = $qtyBox.data('rowid');

            cartUpdating = true;
            $qtyBox.css('opacity', '0.5');

            $.ajax({
                type: "GET",
                url: "{{ route('cart.decrement') }}",
                data: { id: rowId },
                dataType: 'json',
                headers: { 'Accept': 'application/json' },
                success: function(res) {
                    cartUpdating = false;
                    $qtyBox.css('opacity', '1');
                    if (res && res.success) {
                        $qtyBox.find('.qty-val').text(res.item_qty);
                        $itemRow.find('.item-total-price').text('৳ ' + Math.round(res.item_total).toLocaleString());
                        baseSubtotal = parseFloat(res.subtotal) || 0;
                        $('#subtotalAmount').text('৳ ' + Math.round(baseSubtotal));

                        var foundItem = cartItems.find(function(it) { return it.rowId === rowId; });
                        if (foundItem) { foundItem.qty = res.item_qty; }

                        applyShippingToDomAndSession();
                        if (typeof cart_count === 'function') cart_count();
                        if (typeof mobile_cart === 'function') mobile_cart();
                    } else {
                        window.location.reload();
                    }
                },
                error: function() {
                    cartUpdating = false;
                    $qtyBox.css('opacity', '1');
                    window.location.reload();
                }
            });
        });

        // ==========================================
        // 2. SHIPPING & TOTAL CALCULATION
        // ==========================================
        
        let baseSubtotal = parseFloat("{{ $subtotal ?? 0 }}");
        let baseDiscount = parseFloat("{{ $discount ?? 0 }}");
        let advanceAmount = parseFloat("{{ $advance_amount ?? 0 }}");
        const hasAdvance = @json($hasAdvance ?? false);
        const requiresShipping = @json($requires_shipping ?? false);
        let cartItems = @json($cartItemsForJs ?? []);
        const hasAllFreeDelivery = @json($hasAllFreeDelivery ?? false);

        // ⭐ Free Delivery Check Function
        function checkFreeDelivery() {
            // Check if all physical products have free_delivery = 1
            let allFreeDelivery = true;
            for (let i = 0; i < cartItems.length; i++) {
                let item = cartItems[i];
                // Skip digital products
                if (item.is_digital == 1) {
                    continue;
                }
                // If any physical product doesn't have free_delivery, return false
                if (item.free_delivery != 1) {
                    allFreeDelivery = false;
                    break;
                }
            }
            return allFreeDelivery;
        }

        function shippingChargeFromSelect() {
            if ($('#checkout_area').length && $('#checkout_area').val()) {
                return parseFloat($('#checkout_area option:selected').attr('data-charge')) || 0;
            }
            if ($('#checkout_district').length && $('#checkout_district').val()) {
                var dCharge = parseFloat($('#checkout_district option:selected').attr('data-charge')) || 0;
                if (dCharge > 0) return dCharge;
            }
            return 0;
        }

        function applyShippingToDomAndSession() {
            var isFreeDelivery = checkFreeDelivery();
            var divId = $('#checkout_division_id').val() || $('#checkout_division').val() || null;
            var distId = $('#checkout_district_id').val() || $('#checkout_district').val() || null;
            var upaId = $('#checkout_upazila_id').val() || $('#checkout_upazila').val() || null;

            if (!requiresShipping || isFreeDelivery) {
                var shippingCharge = 0;
                var grandTotal = baseSubtotal + shippingCharge - baseDiscount;
                var dueAmount = hasAdvance ? (grandTotal - advanceAmount) : 0;
                $('#shippingAmount').text('৳ 0');
                $('#grandTotalAmount').text('৳ ' + Math.round(grandTotal));
                if (hasAdvance) {
                    $('#dueAmountCell').text('৳ ' + Math.round(dueAmount));
                    $('#dueAmountText').text(Math.round(dueAmount));
                }
                $.get('{{ route("shipping.charge") }}', { id: 'free_delivery' });
                return;
            }

            $.ajax({
                type: "GET",
                url: "{{ route('shipping.charge') }}",
                data: {
                    division_id: divId,
                    district_id: distId,
                    upazila_id: upaId
                },
                dataType: "json",
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    var shippingCharge = (res && typeof res.charge !== 'undefined') ? parseFloat(res.charge) : 0;
                    if (isFreeDelivery) shippingCharge = 0;

                    var grandTotal = baseSubtotal + shippingCharge - baseDiscount;
                    var dueAmount = hasAdvance ? (grandTotal - advanceAmount) : 0;

                    $('#shippingAmount').text('৳ ' + Math.round(shippingCharge));
                    $('#grandTotalAmount').text('৳ ' + Math.round(grandTotal));

                    if (hasAdvance) {
                        $('#dueAmountCell').text('৳ ' + Math.round(dueAmount));
                        $('#dueAmountText').text(Math.round(dueAmount));
                    }
                }
            });
        }

        document.addEventListener('deliveryLocationSelected', function(e) {
            applyShippingToDomAndSession();
            saveIncompleteOrder();
        });

        $('#checkout_area').on('change', function () {
            applyShippingToDomAndSession();
            saveIncompleteOrder();
        });

        $('#checkout_division').on('change', function () {
            var divId = $(this).val();
            $('#checkout_district').prop('disabled', !divId).html(divId ? '<option value="">লোড হচ্ছে...</option>' : '<option value="">আগে বিভাগ সিলেক্ট করুন</option>');
            $('#checkout_upazila').prop('disabled', true).html('<option value="">আগে জেলা সিলেক্ট করুন</option>');
            if (!divId) {
                applyShippingToDomAndSession();
                saveIncompleteOrder();
                return;
            }
            $.get('{{ url('/ajax/delivery/districts') }}/' + divId, function (res) {
                var opts = '<option value="">জেলা নির্বাচন করুন</option>';
                (res.data || []).forEach(function (r) {
                    opts += '<option value="' + r.id + '" data-charge="' + r.delivery_charge + '">' + r.name + ' (৳' + r.delivery_charge + ')</option>';
                });
                $('#checkout_district').html(opts).prop('disabled', false);
            }).fail(function () {
                $('#checkout_district').html('<option value="">লোড ব্যর্থ</option>');
            });
            applyShippingToDomAndSession();
            saveIncompleteOrder();
        });

        $('#checkout_district').on('change', function () {
            var distId = $(this).val();
            $('#checkout_upazila').prop('disabled', !distId).html(distId ? '<option value="">লোড হচ্ছে...</option>' : '<option value="">আগে জেলা সিলেক্ট করুন</option>');
            if (!distId) {
                applyShippingToDomAndSession();
                saveIncompleteOrder();
                return;
            }
            applyShippingToDomAndSession();
            $.get('{{ url('/ajax/delivery/upazilas') }}/' + distId, function (res) {
                var opts = '<option value="">উপজেলা নির্বাচন করুন</option>';
                (res.data || []).forEach(function (r) {
                    opts += '<option value="' + r.id + '">' + r.name + '</option>';
                });
                $('#checkout_upazila').html(opts).prop('disabled', false);
            }).fail(function () {
                $('#checkout_upazila').html('<option value="">লোড ব্যর্থ</option>');
            });
            saveIncompleteOrder();
        });

        $('#checkout_upazila').on('change', function () {
            saveIncompleteOrder();
        });

        // ⭐ পেজ লোড — ফ্রি ডেলিভারি / শিপিং সার্ফেস
        $(document).ready(function() {
            var isFreeDeliveryOnLoad = hasAllFreeDelivery || checkFreeDelivery();

            if (!requiresShipping) {
                return;
            }

            if (isFreeDeliveryOnLoad) {
                applyShippingToDomAndSession();
            } else {
                var currentShipping = parseFloat($('#shippingAmount').text().replace(/[৳,\s]/g, '').trim()) || 0;
                var grandTotal = baseSubtotal + currentShipping - baseDiscount;
                var dueAmount = hasAdvance ? (grandTotal - advanceAmount) : 0;

                $('#grandTotalAmount').text('৳ ' + Math.round(grandTotal));

                if (hasAdvance) {
                    $('#dueAmountCell').text('৳ ' + Math.round(dueAmount));
                    $('#dueAmountText').text(Math.round(dueAmount));
                }

                var did = $('#checkout_district').val();
                var aid = $('#checkout_area').val();
                if (did) {
                    $.get('{{ route("shipping.charge") }}', { id: did });
                } else if (aid) {
                    $.get('{{ route("shipping.charge") }}', { id: aid });
                }
            }
        });

        // ==========================================
        // 3. INCOMPLETE ORDER LOGIC (MAIN REQUEST)
        // ==========================================

        function selectedLocationText($sel) {
            if (!$sel.length || !$sel.val()) return '';
            return ($sel.find('option:selected').text() || '').replace(/\s*\(৳[^)]*\)\s*/g, '').trim();
        }

        function buildCheckoutAddress() {
            var street = ($('input[name="address"]').val() || '').trim();
            var parts = [];
            if (requiresShipping) {
                var locText = ($('#checkout_delivery_area_label').text() || '').trim();
                if (locText && locText.indexOf('>') !== -1) {
                    parts.push(locText);
                } else {
                    var div = selectedLocationText($('#checkout_division'));
                    var dist = selectedLocationText($('#checkout_district'));
                    var upa = selectedLocationText($('#checkout_upazila'));
                    if (div) parts.push(div);
                    if (dist) parts.push(dist);
                    if (upa) parts.push(upa);
                }
                var area = selectedLocationText($('#checkout_area'));
                if (area) parts.push(area);
            }
            if (street) parts.unshift(street);
            return parts.join(', ');
        }

        function buildCheckoutMeta(shippingCharge) {
            var meta = {
                subtotal: baseSubtotal,
                discount: baseDiscount,
                shipping_charge: shippingCharge,
                order_note: ($('#order_note').val() || '').trim()
            };
            if (requiresShipping) {
                meta.division_id = $('#checkout_division_id').val() || $('#checkout_division').val() || null;
                meta.district_id = $('#checkout_district_id').val() || $('#checkout_district').val() || null;
                meta.upazila_id = $('#checkout_upazila_id').val() || $('#checkout_upazila').val() || null;
                var locText = ($('#checkout_delivery_area_label').text() || '').trim();
                if (locText && locText.indexOf('>') !== -1) {
                    meta.location_label = locText;
                } else {
                    var loc = [];
                    var div = selectedLocationText($('#checkout_division'));
                    var dist = selectedLocationText($('#checkout_district'));
                    var upa = selectedLocationText($('#checkout_upazila'));
                    if (upa) loc.push(upa);
                    if (dist) loc.push(dist);
                    if (div) loc.push(div);
                    meta.location_label = loc.join(', ');
                }
            }
            return meta;
        }

        function saveIncompleteOrder() {
            if (isSubmitting || checkoutOtpPending) return;
            if (incompleteOrderTimer) clearTimeout(incompleteOrderTimer);

            incompleteOrderTimer = setTimeout(function() {
                var name = ($('input[name="name"]').val() || '').trim();
                var phone = ($('input[name="phone"]').val() || '').replace(/\D/g, '');
                var address = buildCheckoutAddress();

                if (!name || phone.length < 11) {
                    return;
                }

                if (!cartItems || !cartItems.length) {
                    return;
                }

                var isFreeDelivery = checkFreeDelivery();
                var shippingCharge = isFreeDelivery ? 0 : shippingChargeFromSelect();
                var total = Math.round(baseSubtotal + shippingCharge - baseDiscount);
                var meta = buildCheckoutMeta(shippingCharge);

                $.ajax({
                    url: '{{ route("incomplete.order.store") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    data: {
                        name: name,
                        phone: phone,
                        address: address,
                        items: cartItems,
                        checkout_meta: meta,
                        total_amount: total,
                        product_image: cartItems[0] && cartItems[0].image ? cartItems[0].image : '',
                        product_link: cartItems[0] && cartItems[0].link ? cartItems[0].link : ''
                    }
                });
            }, 2000);
        }

        // ফর্মের যেকোনো ইনপুট চেঞ্জ হলে এই ফাংশন কল হবে
        $('#checkout-form input, #checkout-form select, #checkout-form textarea').on('input change', function() {
             if($(this).attr('name') !== 'payment_method') {
                 saveIncompleteOrder();
             }
        });

        // Collapsible Order Note Toggle
        $(document).on('click', '#toggle_checkout_order_note', function(e) {
            e.preventDefault();
            var $box = $('#checkout_note_collapse_box');
            var $icon = $('#checkout_note_icon');
            var $text = $('#checkout_note_text');
            $box.slideToggle(200, function() {
                if ($box.is(':visible')) {
                    $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                    $text.text('অর্ডার নোট বন্ধ করুন');
                    $box.find('textarea').focus();
                } else {
                    $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                    $text.text('অর্ডার নোট');
                }
            });
        });

        // ==========================================
        // 4. FORM SUBMISSION & VALIDATION
        // ==========================================

        $('#checkout-form').on('submit', function(e) {
            // ১. ডেলিভারি এরিয়া (বিভাগ > জেলা > থানা) ভ্যালিডেশন
            if (requiresShipping) {
                var divVal = $('#checkout_division_id').val();
                var distVal = $('#checkout_district_id').val();
                var upaVal = $('#checkout_upazila_id').val();
                if (!divVal || !distVal || !upaVal) {
                    e.preventDefault();
                    $('#checkout_delivery_area_trigger').addClass('is-invalid');
                    toastr.error('অনুগ্রহ করে আপনার ডেলিভারি এরিয়া (বিভাগ > জেলা > থানা) নির্বাচন করুন', 'এরিয়া নির্বাচন');
                    $('#checkout_delivery_area_trigger').trigger('click');
                    $('.btn-place-order').prop('disabled', false);
                    return false;
                }
            }
            // পেমেন্ট মেথড চেক
            var paymentMethod = $('input[name="payment_method"]:checked').val();
            
            if (!paymentMethod) {
                e.preventDefault();
                toastr.error('অর্ডার সম্পন্ন করতে পেমেন্ট মেথড নির্বাচন করুন।', 'Error');
                $('#payment-error').show();
                if ($(".payment-options-list").length) {
                    $('html, body').animate({ scrollTop: $(".payment-options-list").offset().top - 150 }, 500);
                }
                $('.btn-place-order').prop('disabled', false);
                return false;
            } else {
                $('#payment-error').hide();

                var pm = $('input[name="payment_method"]:checked').val() || '';
                if (pm.indexOf('manual_') === 0) {
                    var trx = $('input[name="manual_trx_id"]').val();
                    if (!trx || !String(trx).trim()) {
                        e.preventDefault();
                        toastr.error('ম্যানুয়াল পেমেন্টের জন্য ট্রানজেকশন আইডি লিখুন।', 'Error');
                        $('#manual-payment-fields').show();
                        $('html, body').animate({ scrollTop: $('#manual-payment-fields').offset().top - 120 }, 400);
                        $('.btn-place-order').prop('disabled', false);
                        return false;
                    }
                }

                // ৩. অর্ডার সাবমিট হচ্ছে, তাই ইনকমপ্লিট টাইমার বন্ধ করে দেওয়া হলো
                window.__checkoutOtpSkipCancel = true;
                isSubmitting = true;
                checkoutOtpPending = true;
                if(incompleteOrderTimer) {
                    clearTimeout(incompleteOrderTimer);
                }
                
                // ফর্ম সাবমিট হতে দিন...
            }
        });

        // পেমেন্ট সিলেক্ট করলে এরর হাইড হবে
        function syncManualPaymentUi() {
            var v = $('input[name="payment_method"]:checked').val() || '';
            if (v.indexOf('manual_') === 0) {
                $('#manual-payment-fields').show();
                var inst = '';
                (window.MANUAL_GATEWAYS || []).forEach(function (g) {
                    if (g.code === v) {
                        inst = g.instructions || '';
                    }
                });
                $('#manual-instructions-body').html($('<div/>').text(inst).html().replace(/\n/g, '<br>'));
                $('#manual_trx_id').prop('required', true);
            } else {
                $('#manual-payment-fields').hide();
                $('#manual_trx_id').prop('required', false);
            }
        }

        $('input[name="payment_method"]').on('change', function() {
            $('#payment-error').hide();
            syncManualPaymentUi();
        });
        if (!$('input[name="payment_method"]:checked').length && $('input[name="payment_method"]').length) {
            $('input[name="payment_method"]:first').prop('checked', true);
        }
        syncManualPaymentUi();

        // চেকআউটে আগে থেকে তথ্য থাকলে একবার ইনকমপ্লিট সেভ ট্রিগার
        setTimeout(function() { saveIncompleteOrder(); }, 2500);
    });
</script>
{{-- GTM + Facebook + TikTok — checkout funnel --}}
<script type="text/javascript">
$(document).ready(function () {
    if (typeof window.EcomTracking === 'undefined') return;

    var items = @json($cartItemsForJs);
    var hasAdvance = @json($hasAdvance);
    var advanceAmount = parseFloat("{{ $advance_amount }}") || 0;
    var grandTotal = parseFloat("{{ $grand_total }}") || 0;
    var payableNow = hasAdvance ? advanceAmount : grandTotal;
    var coupon = @json(Session::get('coupon_code', null));

    function checkoutUserFromForm() {
        var districtText = ($('#checkout_district option:selected').text() || $('select[name="district_id"] option:selected').text() || '').replace(/\s*\(৳[^)]*\)\s*/g, '').trim();
        if (!districtText || districtText.indexOf('সিলেক্ট') !== -1 || districtText.indexOf('লোড') !== -1) districtText = '';
        var divisionText = ($('#checkout_division option:selected').text() || $('select[name="division_id"] option:selected').text() || '').trim();
        if (!divisionText || divisionText.indexOf('সিলেক্ট') !== -1) divisionText = '';

        return {
            name: ($('input[name="name"]').val() || '').trim(),
            phone: ($('input[name="phone"]').val() || '').trim(),
            address: ($('input[name="address"]').val() || '').trim(),
            city: districtText || divisionText || 'Dhaka',
            state: divisionText || districtText || 'Dhaka'
        };
    }

    if (items.length) {
        EcomTracking.initiateCheckout({
            items: items,
            value: payableNow,
            coupon: coupon,
            user: checkoutUserFromForm()
        });
    }

    var identifyTimer;
    $('#checkout-form input[name="name"], #checkout-form input[name="phone"], #checkout-form input[name="address"], #checkout_district, #checkout_division').on('input blur change', function () {
        clearTimeout(identifyTimer);
        identifyTimer = setTimeout(function () {
            var u = checkoutUserFromForm();
            if (u.phone && String(u.phone).replace(/\D/g, '').length >= 11) {
                EcomTracking.identify(u);
            }
        }, 600);
    });

    @auth('customer')
    EcomTracking.identify(@json(\App\Support\EcommerceTrackingUser::fromCustomer(auth('customer')->user())));
    @endauth

    var form = document.getElementById('checkout-form');
    if (form) {
        form.addEventListener('submit', function () {
            var pm = form.querySelector('input[name="payment_method"]:checked');
            EcomTracking.identify(checkoutUserFromForm());
            EcomTracking.addPaymentInfo({
                items: items,
                value: payableNow,
                coupon: coupon,
                payment_method: pm ? pm.value : ''
            });
        });
    }
});

// 🚀 Floating Sticky Order Bar for Checkout Page
(function () {
    var floatingBar = document.getElementById('checkout_floating_bar');
    if (!floatingBar) return;

    function getActiveSubmitBtn() {
        var btns = document.querySelectorAll('.btn-place-order');
        for (var i = 0; i < btns.length; i++) {
            if (btns[i].offsetParent !== null) {
                return btns[i];
            }
        }
        return btns.length ? btns[0] : null;
    }

    function checkSubmitVisibility() {
        var btn = getActiveSubmitBtn();
        if (!btn) return;
        var rect = btn.getBoundingClientRect();
        var windowHeight = window.innerHeight || document.documentElement.clientHeight;
        var inView = (rect.bottom > 0 && rect.top < windowHeight);
        if (!inView) {
            floatingBar.classList.add('is-visible');
        } else {
            floatingBar.classList.remove('is-visible');
        }
    }

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            var anyVisible = entries.some(function(entry) { return entry.isIntersecting; });
            if (!anyVisible) {
                floatingBar.classList.add('is-visible');
            } else {
                floatingBar.classList.remove('is-visible');
            }
        }, {
            root: null,
            threshold: 0.1
        });

        document.querySelectorAll('.btn-place-order').forEach(function (btn) {
            observer.observe(btn);
        });
    } else {
        window.addEventListener('scroll', checkSubmitVisibility, { passive: true });
        window.addEventListener('resize', checkSubmitVisibility);
        checkSubmitVisibility();
    }

    // Sync floating bar grand total when #grandTotalAmount changes
    var mainGrandTotal = document.getElementById('grandTotalAmount');
    var floatingGrandTotal = document.getElementById('checkoutFloatingGrandTotal');
    if (mainGrandTotal && floatingGrandTotal && 'MutationObserver' in window) {
        var totalObserver = new MutationObserver(function () {
            floatingGrandTotal.textContent = mainGrandTotal.textContent;
        });
        totalObserver.observe(mainGrandTotal, { childList: true, characterData: true, subtree: true });
    }

    // Click handler for floating checkout submit button
    var floatingSubmitBtn = document.getElementById('checkout_floating_submit_btn');
    if (floatingSubmitBtn) {
        floatingSubmitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var btn = getActiveSubmitBtn();
            if (btn) {
                btn.click();
            } else {
                var f = document.getElementById('checkout-form');
                if (f) f.submit();
            }
        });
    }
})();

// Auto-clear placeholders on focus/click to ensure caret is at position 0
(function() {
    function bindAutoClearPlaceholders() {
        var fields = document.querySelectorAll('.checkout-section input[placeholder], .checkout-section textarea[placeholder], .modern-outline-group input[placeholder], .modern-outline-group textarea[placeholder]');
        fields.forEach(function (el) {
            var ph = el.getAttribute('placeholder');
            if (ph && ph.trim() !== '') {
                el.setAttribute('data-stored-ph', ph);

                el.addEventListener('focus', function () {
                    this.setAttribute('placeholder', '');
                    if (!this.value && this.setSelectionRange) {
                        try { this.setSelectionRange(0, 0); } catch(e){}
                    }
                });

                el.addEventListener('click', function () {
                    if (!this.value) {
                        this.setAttribute('placeholder', '');
                        if (this.setSelectionRange) {
                            try { this.setSelectionRange(0, 0); } catch(e){}
                        }
                    }
                });

                el.addEventListener('blur', function () {
                    if (!this.value || !this.value.trim()) {
                        this.setAttribute('placeholder', this.getAttribute('data-stored-ph') || '');
                    }
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindAutoClearPlaceholders);
    } else {
        bindAutoClearPlaceholders();
    }
})();
</script>
@endpush