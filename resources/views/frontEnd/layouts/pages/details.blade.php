@extends('frontEnd.layouts.master')
@section('title', $details->name) 
@push('seo')
@php
    $metaTitle = $details->meta_title ?? $details->name;
    $metaDescription = $details->meta_description ?? Str::limit(strip_tags($details->description), 160);
    $metaKeywords = $details->meta_keywords ?? $details->name;
    $metaImage = $details->meta_image ? asset($details->meta_image) : asset(optional($details->image)->image);

    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'হোম',
            'item' => url('/'),
        ]
    ];
    $pos = 2;
    if (!empty($details->category)) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'name' => $details->category->name,
            'item' => url('/category/' . $details->category->slug),
        ];
    }
    if (!empty($details->subcategory)) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'name' => $details->subcategory->subcategoryName,
            'item' => url('/category/' . $details->category->slug),
        ];
    }
    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => $pos,
        'name' => $details->name,
        'item' => route('product', $details->slug),
    ];
    $schemaJson = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@endphp

<meta name="app-url" content="{{ route('product', $details->slug) }}" />
<meta name="robots" content="index, follow" />

<meta name="title" content="{{ $metaTitle }}" />
<meta name="description" content="{{ $metaDescription }}" />
<meta name="keywords" content="{{ $metaKeywords }}" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="@gomobd" />
<meta name="twitter:title" content="{{ $metaTitle }}" />
<meta name="twitter:description" content="{{ $metaDescription }}" />
<meta name="twitter:image" content="{{ $metaImage }}" />

<!-- Open Graph data -->
<meta property="og:title" content="{{ $metaTitle }}" />
<meta property="og:type" content="product" />
<meta property="og:url" content="{{ route('product', $details->slug) }}" />
<meta property="og:image" content="{{ $metaImage }}" />
<meta property="og:description" content="{{ $metaDescription }}" />
<meta property="og:site_name" content="{{ $generalsetting->name ?? 'gomobd.com' }}" />

<script type="application/ld+json">
{!! $schemaJson !!}
</script>
@endpush

@push('css')
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/zoomsl.css') }}">
<style>
/* 🎯 Attention-Grabbing Shake / Vibration for "অর্ডার করুন" Button */
@keyframes orderBtnShake {
    0%, 100% {
        transform: translateX(0) scale(1);
    }
    5%, 15% {
        transform: translateX(-4px) rotate(-1.5deg) scale(1.02);
    }
    10%, 20% {
        transform: translateX(4px) rotate(1.5deg) scale(1.02);
    }
    25% {
        transform: translateX(-2px) scale(1.01);
    }
    30% {
        transform: translateX(2px) scale(1.01);
    }
    35% {
        transform: translateX(0) scale(1);
    }
}

.order_now_btn, 
.order_now_btn_m,
.order-btn {
    animation: orderBtnShake 2.5s infinite ease-in-out !important;
    box-shadow: 0 4px 14px {{ optional($generalsetting)->primary_color ?? '#e11d48' }}66 !important;
    position: relative !important;
    background-color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    border-color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    color: #fff !important;
    font-size: 18px !important;
    font-weight: 800 !important;
    padding: 10px 18px !important;
}

.order_now_btn:hover, 
.order_now_btn_m:hover,
.order-btn:hover {
    animation: none !important;
    transform: scale(1.03) !important;
    box-shadow: 0 6px 20px {{ optional($generalsetting)->primary_color ?? '#e11d48' }}99 !important;
    filter: brightness(0.9) !important;
}

/* Cart button – border style matching grid card */
.add_cart_btn {
    background: transparent !important;
    border: 2px solid {{ optional($generalsetting)->secodery_color ?? '#198754' }} !important;
    color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    font-size: 17px !important;
    font-weight: 700 !important;
    padding: 10px 18px !important;
}
.add_cart_btn i {
    color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    font-size: 18px !important;
}
.add_cart_btn:hover,
.add_cart_btn:active {
    background: {{ optional($generalsetting)->secodery_color ?? '#198754' }} !important;
    border-color: {{ optional($generalsetting)->secodery_color ?? '#198754' }} !important;
    color: #fff !important;
}
.add_cart_btn:hover i,
.add_cart_btn:active i {
    color: #fff !important;
}

/* WhatsApp Integration Button */
.product-whatsapp-btn {
    background-color: #25D366 !important;
    border: 1px solid #1ebe57 !important;
    color: #ffffff !important;
    font-size: 15.5px !important;
    font-weight: 700 !important;
    padding: 11px 16px !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
    transition: all 0.25s ease !important;
    box-shadow: 0 3px 10px rgba(37, 211, 102, 0.25) !important;
}
.product-whatsapp-btn:hover {
    background-color: #20ba59 !important;
    border-color: #1a9e4b !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 5px 14px rgba(37, 211, 102, 0.4) !important;
}
.product-whatsapp-btn i {
    font-size: 20px !important;
    color: #ffffff !important;
}

/* 🚀 Floating Sticky Order Bar (when main button is off-screen) */
.product-floating-order-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #ffffff;
    box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.15);
    border-top: 1px solid #e2e8f0;
    z-index: 10005;
    padding: 10px 0;
    transform: translateY(115%);
    opacity: 0;
    pointer-events: none;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
}
.product-floating-order-bar.is-visible {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
}
.product-floating-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.product-floating-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex-shrink: 0;
}
.product-floating-thumb {
    width: 46px;
    height: 46px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}
.product-floating-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.product-floating-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
}
.product-floating-title {
    font-size: 13.5px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
    margin-bottom: 2px;
}
.product-floating-price-wrap {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}
.floating-primary-price .floating-new-price {
    font-size: 18px;
    font-weight: 800;
    color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }};
    letter-spacing: -0.3px;
}
.floating-discount-line {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 1px;
}
.floating-discount-line .floating-old-price {
    font-size: 12px;
    font-weight: 500;
    color: #94a3b8;
    text-decoration: line-through;
}
.floating-discount-badge {
    background: #fee2e2;
    color: #dc2626;
    font-size: 10.5px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 4px;
    line-height: 1.2;
}
.product-floating-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    justify-content: flex-end;
}
.product-floating-cart-btn {
    height: 44px;
    min-width: 44px;
    padding: 0 14px;
    border-radius: 8px;
    border: 2px solid {{ optional($generalsetting)->secodery_color ?? '#198754' }} !important;
    background: #f8fafc !important;
    color: {{ optional($generalsetting)->secodery_color ?? '#198754' }} !important;
    font-weight: 700;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
.product-floating-cart-btn i {
    font-size: 18px !important;
    color: {{ optional($generalsetting)->secodery_color ?? '#198754' }} !important;
}
.product-floating-cart-btn:hover {
    background: {{ optional($generalsetting)->secodery_color ?? '#198754' }} !important;
    color: #ffffff !important;
}
.product-floating-cart-btn:hover i {
    color: #ffffff !important;
}
.product-floating-order-btn {
    height: 44px;
    padding: 0 20px;
    border-radius: 8px;
    border: none !important;
    background-color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    color: #ffffff !important;
    font-weight: 800 !important;
    font-size: 16px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 15px {{ optional($generalsetting)->primary_color ?? '#e11d48' }}80 !important;
    animation: orderBtnShake 2.2s infinite ease-in-out !important;
    flex: 1;
    max-width: 240px;
}
.product-floating-order-btn:hover {
    filter: brightness(0.92);
    color: #ffffff !important;
}

@media (max-width: 768px) {
    .product-floating-order-bar {
        padding: 7px 10px;
    }
    .product-floating-inner {
        gap: 8px;
    }
    .product-floating-thumb {
        width: 40px;
        height: 40px;
        border-radius: 6px;
    }
    .floating-primary-price .floating-new-price {
        font-size: 16px;
    }
    .product-floating-cart-btn {
        width: 42px;
        min-width: 42px;
        height: 42px;
        padding: 0;
    }
    .product-floating-cart-btn i {
        font-size: 17px !important;
    }
    .product-floating-order-btn {
        height: 42px;
        padding: 0 12px;
        font-size: 15px !important;
        max-width: none;
    }
}

/* 🎨 Matching Border for Color Swatches (same as Size & Variant) */
.pro-color .selector-item_label {
    min-width: 38px !important;
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    border: 2px solid #cbd5e1 !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
    position: relative !important;
}
.pro-color .selector-item_label:hover {
    border-color: #64748b !important;
    transform: scale(1.06) !important;
}
.pro-color .selector-item_radio:checked + .selector-item_label {
    border-color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    outline: 2px solid {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    outline-offset: 2px !important;
    transform: scale(1.08) !important;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15) !important;
}
.pro-color .selector-item_label span,
.quick-color-swatch span {
    display: none !important;
}

/* 🏷️ Size / Variant Selected — matching color double-ring / outline-offset style */
.pro-size .selector-item_radio:checked + .selector-item_label,
.selector-item_radio:checked + .selector-item_label:not(.pro-color .selector-item_label) {
    border-color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    outline: 2px solid {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    outline-offset: 2px !important;
    background: {{ optional($generalsetting)->primary_color ?? '#e11d48' }}15 !important;
    color: {{ optional($generalsetting)->primary_color ?? '#e11d48' }} !important;
    font-weight: 700 !important;
    transform: scale(1.06) !important;
    box-shadow: 0 3px 8px rgba(0,0,0,0.16) !important;
}

/* 🔢 Single Product Page Quantity Selector (Identical to Quickview Popup) */
.quick-qty-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 8px 0 12px 0;
}
.quick-qty-label {
    font-size: 13.5px;
    font-weight: 600;
    color: #334155;
}
.qty-cart .quick-qty-box,
.qty-cart .quantity,
.quick-qty-box {
    display: inline-flex !important;
    align-items: center !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    height: 38px !important;
    width: auto !important;
    background: #ffffff !important;
    box-shadow: none !important;
    padding: 0 !important;
    position: static !important;
}
.qty-cart .quick-qty-btn,
.qty-cart .quantity .minus,
.qty-cart .quantity .plus,
.quick-qty-btn {
    width: 36px !important;
    height: 38px !important;
    border: none !important;
    background: #f8fafc !important;
    color: #1e293b !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    transition: background 0.2s !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    line-height: normal !important;
    position: static !important;
    padding: 0 !important;
    margin: 0 !important;
    user-select: none !important;
    outline: none !important;
}
.qty-cart .quick-qty-btn:hover,
.qty-cart .quantity .minus:hover,
.qty-cart .quantity .plus:hover,
.quick-qty-btn:hover {
    background: #e2e8f0 !important;
}
.qty-cart .quick-qty-box input,
.qty-cart .quantity input,
.quick-qty-box input {
    width: 44px !important;
    height: 38px !important;
    border: none !important;
    text-align: center !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    color: #0f172a !important;
    outline: none !important;
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
    line-height: 38px !important;
}

/* ✅ Scoped Review Section */
.gomobd-review-section {
    font-family: 'Poppins', sans-serif;
}

/* Title */
.gomobd-review-section .gomobd-review-title {
    font-size: 20px;
    color: #222;
}

/* Review Card */
.gomobd-review-section .gomobd-review-card {
    background: #fff;
    border: 1px solid #e6e6e6;
    border-radius: 10px;
    padding: 16px 20px;
    transition: all 0.3s ease-in-out;
}
.gomobd-review-section .gomobd-review-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Header */
.gomobd-review-section .gomobd-review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

/* Avatar */
.gomobd-review-section .gomobd-review-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #198754;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 600;
    margin-right: 12px;
}

/* Name + Date */
.gomobd-review-section .gomobd-review-meta {
    flex-grow: 1;
}
.gomobd-review-section .gomobd-review-name {
    font-size: 16px;
    margin: 0;
    color: #222;
}
.gomobd-review-section .gomobd-review-date {
    font-size: 13px;
    color: #888;
}

/* Stars */
.gomobd-review-section .gomobd-review-stars {
    color: #f8b400;
    font-size: 15px;
}

/* Review Text */
.gomobd-review-section .gomobd-review-body {
    margin-top: 10px;
    color: #555;
    font-size: 15px;
    line-height: 1.6;
}

/* Empty state */
.gomobd-review-section .gomobd-review-empty {
    background: #f9f9f9;
    border-radius: 10px;
    color: #777;
}

/* ✅ Simple Wholesale Pricing Styles */
.wholesale-tier-row:hover {
    background: #f0f8f0 !important;
}

.wholesale-tier-row.active-tier {
    background: #d4edda !important;
    border-left: 3px solid #28a745 !important;
}

/* Product buy box — remove extra bottom gap */
.details_right .product-cart form h4 {
    margin-bottom: 0;
}

.details_right .product-cart form > div:last-of-type {
    margin-bottom: 0 !important;
}

/* Sticky video — keep ancestors from breaking position:sticky */
#content .pro_details_area,
#content .pro_details_area .container,
#content .pro_details_area .product-details-content-row,
#content .pro_details_area .product-video-col,
#content .pro_details_area .product-video-sticky-wrap {
    overflow: visible !important;
}

#content .pro_details_area .product-video-sticky-wrap {
    z-index: 20;
}
</style>
@endpush

@section('content')
<div class="homeproduct main-details-page">
    <section class="product-section">
        <div class="container">
            <div class="row align-items-start">

                {{-- LEFT: Image Gallery --}}
                <div class="col-sm-6 col-12 position-relative mb-4 mb-sm-0">
                    @if($details->old_price)
                    <div class="product-details-discount-badge">
                        <div class="sale-badge">
                            <div class="sale-badge-inner">
                                <div class="sale-badge-box">
                                    <span class="sale-badge-text">
                                        <p>@php $discount=(((($details->old_price)-($details->new_price))*100) / ($details->old_price)) @endphp {{ number_format($discount, 0) }}%</p>
                                        ছাড়
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="details_slider owl-carousel" id="details_slider_main">
                        @foreach ($details->images as $k => $value)
                            <div class="dimage_item" data-color-id="{{ $value->color_id ?? '' }}">
                                <img src="{{ asset($value->image) }}" class="block__pic" @if($k === 0) fetchpriority="high" loading="eager" decoding="async" @else loading="lazy" decoding="async" @endif />
                            </div>
                        @endforeach
                    </div>

                    <div class="indicator_thumb @if ($details->images->count() > 4) thumb_slider owl-carousel @endif" id="indicator_thumb_wrapper">
                        @foreach ($details->images as $key => $image)
                            <div class="indicator-item" data-id="{{ $key }}" data-color-id="{{ $image->color_id ?? '' }}">
                                <img src="{{ asset($image->image) }}" loading="lazy" decoding="async" />
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- RIGHT: Product Info --}}
                <div class="col-sm-6 col-12">
                    <div class="details_right">

                        <div class="product">
                            <div class="product-cart">

                                {{-- Product Name --}}
                                <p class="name">{{ $details->name }}</p>

                                {{-- Price --}}
                                <p class="details-price">
                                    @if ($details->old_price)
                                        <del>৳{{ round($details->old_price) }}</del>
                                    @endif
                                    <span id="newPrice">৳{{ round($details->new_price) }}</span>
                                </p>

                                {{-- Rating + Brand (same row) --}}
                                <div class="details-ratting-wrapper">
                                    @php
                                        $averageRating = (float) ($productReviewsAverage ?? 0);
                                        $filledStars = floor($averageRating);
                                        $emptyStars = 5 - $filledStars;
                                    @endphp
                                    @if ($averageRating >= 0 && $averageRating <= 5)
                                        @for ($i = 1; $i <= $filledStars; $i++)<i class="fas fa-star"></i>@endfor
                                        @if ($averageRating != $filledStars)<i class="far fa-star-half-alt"></i>@endif
                                        @for ($i = 1; $i <= $emptyStars; $i++)<i class="far fa-star"></i>@endfor
                                        <span>{{ number_format($averageRating, 2) }}/5</span>
                                    @endif
                                    <a class="all-reviews-button" href="#writeReview">See Reviews ({{ $productReviewsTotal }})</a>
                                    @if ($details->brand)
                                        <span class="details-brand-badge"><i class="fa fa-building"></i> {{ $details->brand->name }}</span>
                                    @endif
                                </div>

                                {{-- Wholesale Pricing --}}
                                @if($details->is_wholesale && $details->wholesalePrices && $details->wholesalePrices->count() > 0)
                                <div class="wholesale-pricing-section" style="margin: 14px 0;">
                                    <h5 style="margin-bottom: 10px; font-size: 14px; font-weight: 700; color: #1e293b;">
                                        <i class="fa fa-tag me-1"></i> Wholesale Pricing
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0" style="background:#fff; font-size:13px;">
                                            <thead style="background:#f8f9fa;">
                                                <tr>
                                                    <th style="padding:8px 12px; font-weight:600;">Quantity</th>
                                                    <th style="padding:8px 12px; font-weight:600;">Price</th>
                                                    <th style="padding:8px 12px; font-weight:600;">Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($details->wholesalePrices->sortBy('min_quantity') as $tier)
                                                <tr class="wholesale-tier-row"
                                                    data-min-qty="{{ $tier->min_quantity }}"
                                                    data-max-qty="{{ $tier->max_quantity ?? 999999 }}"
                                                    data-price="{{ $tier->wholesale_price }}"
                                                    style="cursor:pointer; transition:background 0.2s;">
                                                    <td style="padding:8px 12px;">{{ $tier->min_quantity }}{{ $tier->max_quantity ? ' - '.$tier->max_quantity : '+' }} pcs</td>
                                                    <td style="padding:8px 12px; font-weight:600; color:#28a745;">৳{{ number_format($tier->wholesale_price, 0) }}</td>
                                                    <td style="padding:8px 12px; color:{{ ($tier->stock ?? 0) > 0 ? '#28a745' : '#dc3545' }};">{{ $tier->stock ?? 0 }} pcs</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="text-muted mt-1 mb-0" style="font-size:12px;"><i class="fa fa-info-circle me-1"></i> Quantity select করলে wholesale price automatically apply হবে</p>
                                </div>
                                @endif

                                {{-- Add to Cart Form --}}
                                <form action="{{ route('cart.store') }}" method="POST" name="formName">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $details->id }}" />

                                    {{-- Color Variants --}}
                                    @if ($details->variantPrices->count() > 0)
                                        @php
                                            $productcolors = $details->variantPrices->pluck('color')->unique('id')->filter();
                                            $productsizes  = $details->variantPrices->pluck('size')->unique('id')->filter();
                                        @endphp

                                        @if ($productcolors->count() > 0)
                                        <div class="pro-color">
                                            <div class="color_inner">
                                                <p>Color -</p>
                                                <div class="size-container">
                                                    <div class="selector">
                                                        @foreach ($productcolors as $procolor)
                                                        <div class="selector-item">
                                                            <input type="radio"
                                                                id="fc-option{{ $procolor->id }}"
                                                                value="{{ $procolor->id }}"
                                                                name="product_color"
                                                                class="selector-item_radio emptyalert"
                                                                required />
                                                            <label for="fc-option{{ $procolor->id }}"
                                                                style="background-color: {{ $procolor->color ?? '#ccc' }}"
                                                                class="selector-item_label">
                                                            </label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if ($productsizes->count() > 0)
                                        <div class="pro-size">
                                            <div class="size_inner">
                                                <p>Size & Variant - <span class="attibute-name"></span></p>
                                                <div class="size-container">
                                                    <div class="selector">
                                                        @foreach ($productsizes as $prosize)
                                                        <div class="selector-item">
                                                            <input type="radio"
                                                                id="f-option{{ $prosize->id }}"
                                                                value="{{ $prosize->id }}"
                                                                name="product_size"
                                                                class="selector-item_radio emptyalert"
                                                                required />
                                                            <label for="f-option{{ $prosize->id }}" class="selector-item_label">
                                                                {{ $prosize->sizeName ?? $prosize->name }}
                                                            </label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                    {{-- Unit hidden (value kept for cart logic if needed) --}}
                                    @if ($details->pro_unit)
                                        <input type="hidden" name="pro_unit" value="{{ $details->pro_unit }}" />
                                    @endif

                                    {{-- Quantity + Buttons --}}
                                    <div class="row mt-2">
                                        <div class="qty-cart col-12">
                                            <div class="quick-qty-row">
                                                <span class="quick-qty-label">পরিমাণ:</span>
                                                <div class="quantity quick-qty-box">
                                                    <button type="button" class="quick-qty-btn minus" aria-label="Decrease quantity">-</button>
                                                    @php
                                                        $defaultQty = 1;
                                                        if ($details->is_wholesale && $details->wholesalePrices && $details->wholesalePrices->count() > 0) {
                                                            $defaultQty = max(1, (int) $details->wholesalePrices->sortBy('min_quantity')->first()->min_quantity);
                                                        }
                                                    @endphp
                                                    <input type="number" name="qty" class="product-qty-input"
                                                        value="{{ $defaultQty }}" min="1" step="1" readonly />
                                                    <button type="button" class="quick-qty-btn plus" aria-label="Increase quantity">+</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="single_product col-12">
                                            <button type="submit"
                                                id="main_add_cart_btn"
                                                class="btn add_cart_btn cart_store"
                                                data-id="{{ $details->id }}"
                                                name="add_cart"
                                                value="1">
                                                <i class="fa-solid fa-cart-shopping me-1"></i> কার্টে যোগ করুন
                                            </button>
                                            <button type="submit"
                                                id="main_order_now_btn"
                                                class="btn order_now_btn order_now_btn_m cart_store"
                                                data-id="{{ $details->id }}"
                                                name="order_now"
                                                value="1">
                                                <i class="fa-solid fa-bolt me-1"></i> অর্ডার করুন
                                            </button>
                                        </div>
                                    </div>

                                    {{-- WhatsApp Query Button --}}
                                    @php
                                        $rawWa = $contact->whatsapp ?? $contact->phone ?? $contact->hotline ?? '';
                                        $cleanWa = preg_replace('/[^\d]/', '', (string)$rawWa);
                                        if (!empty($cleanWa)) {
                                            if (str_starts_with($cleanWa, '0')) {
                                                $cleanWa = '88' . $cleanWa;
                                            } elseif (!str_starts_with($cleanWa, '880') && strlen($cleanWa) == 10) {
                                                $cleanWa = '880' . $cleanWa;
                                            }
                                        }
                                        $waMessage = 'হ্যালো, আমি "' . ($details->name ?? 'এই পণ্যটি') . '" সম্পর্কে জানতে চাই। লিংক: ' . Request::url();
                                    @endphp
                                    @if(!empty($cleanWa))
                                    <div class="mt-2">
                                        <a class="btn w-100 product-whatsapp-btn"
                                            href="https://wa.me/{{ $cleanWa }}?text={{ rawurlencode($waMessage) }}"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fa-brands fa-whatsapp me-2"></i>এই পণ্যটি সম্পর্কে WhatsApp-এ জিজ্ঞাসা করুন
                                        </a>
                                    </div>
                                    @endif

                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                {{-- END RIGHT --}}

            </div>
        </div>
    </section>
</div>

<section class="pro_details_area">
    <div class="container">
        <div class="row product-details-content-row">
            <div class="col-sm-8">
                <div class="description-nav-wrapper description-nav-in-content">
                    <div class="description-nav">
                        <ul class="desc-nav-ul">
                            {{-- <li class="active">
                                <a href="#specification" target="_self">Specification</a>
                            </li> --}}
                            <li>
                                <a href="#description" target="_self">Description</a>
                            </li>
                            {{-- <li>
                                <a href="#question" target="_self">Questions (0)</a>
                            </li> --}}
                            <li>
                                <a href="#writeReview" target="_self">Reviews ({{ $productReviewsTotal }}) </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="description tab-content details-action-box" id="description">
                    <h2>বিস্তারিত</h2>
                    <p>{!! $details->description !!}</p>
                </div>
                <div class="tab-content details-action-box" id="writeReview">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                
							  
							  
							  
							<section class="gomobd-review-section mt-5" id="writeReview">
    <div class="gomobd-review-header d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h3 class="gomobd-review-title fw-bold mb-2 mb-md-0">
            Customer Reviews ({{ $productReviewsTotal }})
        </h3>
        <button type="button" class="gomobd-review-btn btn btn-success btn-sm"
            data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="fa fa-edit me-1"></i> Write a Review
        </button>
    </div>

    @if ($productReviewsTotal > 0)
    <div class="gomobd-review-list row g-3" id="productReviewList">
        @include('frontEnd.layouts.ajax.product-reviews', ['reviews' => $productReviews])
    </div>
    @if ($productReviewsTotal > $productReviews->count())
    <div class="text-center mt-3">
        <button type="button" class="btn btn-outline-success btn-sm px-4" id="loadMoreProductReviews"
            data-product-id="{{ $details->id }}"
            data-offset="{{ $productReviews->count() }}"
            data-limit="3">
            More Review
        </button>
    </div>
    @endif
    @else
    <div class="gomobd-review-empty text-center py-5">
        <i class="fa fa-clipboard-list fs-1 text-muted mb-3"></i>
        <p>This product has no reviews yet.<br><strong>Be the first one to write a review.</strong></p>
    </div>
    @endif
</section>


							  
							  
							  
							  
							  
							  
							  
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Your review</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="insert-review">
                                                    @if (Auth::guard('customer')->user())
                                                        <form action="{{ route('customer.review') }}" id="review-form"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="product_id" value="{{ $details->id }}">
                                                            <div class="fz-12 mb-2">
                                                                <div class="rating">
                                                                    <label title="Excelent">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="5" />
                                                                    </label>
                                                                    <label title="Best">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="4" />
                                                                    </label>
                                                                    <label title="Better">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="3" />
                                                                    </label>
                                                                    <label title="Very Good">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="2" />
                                                                    </label>
                                                                    <label title="Good">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="1" />
                                                                    </label>
                                                                </div>
                                                            </div>
                
                                                            <div class="form-group">
                                                                <label for="message-text" class="col-form-label">Message:</label>
                                                                <textarea required class="form-control radius-lg" name="review" id="message-text"></textarea>
                                                                <span id="validation-message" style="color: red;"></span>
                                                            </div>
                                                            <div class="form-group">
                                                                <button class="details-review-button" type="submit">Submit
                                                                    Review</button>
                                                            </div>
                
                                                        </form>
                                                    @else
                                                        <a class="customer-login-redirect" href="{{ route('customer.login') }}">Login
                                                            to Post
                                                            Your Review</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $videoType = $details->pro_video_type ?? ($details->pro_video ? 'youtube' : null);
                $hasVideo = ($videoType === 'youtube' && $details->pro_video) || ($videoType === 'upload' && $details->pro_video_path);
            @endphp
            @if($hasVideo)
            <div class="col-sm-4 product-video-col">
                <div class="product-video-sticky-wrap">
                <div class="pro_vide product-video-sticky">
                    <h2>ভিডিও</h2>
                    @if($videoType === 'youtube' && $details->pro_video)
                    <iframe width="100%" height="315"
                        src="https://www.youtube.com/embed/{{ $details->pro_video }}" title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>
                    @elseif($videoType === 'upload' && $details->pro_video_path)
                    <video width="100%" height="315" controls style="border-radius:8px;background:#000;">
                        <source src="{{ asset($details->pro_video_path) }}" type="video/mp4">
                        <source src="{{ asset($details->pro_video_path) }}" type="video/webm">
                        <source src="{{ asset($details->pro_video_path) }}" type="video/ogg">
                        আপনার ব্রাউজার ভিডিও সাপোর্ট করে না।
                    </video>
                    @endif
                </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<section class="related-product-section">
    <div class="container">
        <div class="row">
            <div class="related-title">
                <h5>Related Product</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="product-inner owl-carousel related_slider">
                    @foreach ($products as $key => $value)
                    <div class="product_item wist_item">

                        <div class="product_item_inner">
                            <div class="pro_img">
                                <a href="{{ route('product', $value->slug) }}">
                                    <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                        alt="{{ $value->name }}"
                                        loading="lazy"
                                        decoding="async" />
                                </a>
                            </div>

                            <div class="pro_des">
                                <div class="pro_name">
                                    <a href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 35) }}</a>
                                </div>
                            </div>
                        </div>

                        @php
                            $averageRating = (float) ($value->reviews_avg_ratting ?? ($value->relationLoaded('reviews') ? $value->reviews->avg('ratting') : 0)); 
                            $filledStars = floor($averageRating);
                            $hasHalfStar = $averageRating - $filledStars >= 0.5;
                            $emptyStars = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                        @endphp

                        {{-- Stars --}}
                        @for ($i = 0; $i < $filledStars; $i++)
                            <i class="fas fa-star"></i>
                        @endfor
                        @if ($hasHalfStar)
                            <i class="fas fa-star-half-alt"></i>
                        @endif
                        @for ($i = 0; $i < $emptyStars; $i++)
                            <i class="far fa-star"></i>
                        @endfor

                        <div class="pro_price">
                            <p>
                                @if($value->old_price)
                                    <del>৳ {{ $value->old_price }}</del>
                                @endif
                                ৳ {{ $value->new_price }}
                                @if($value->old_price && $value->old_price > $value->new_price)
                                    @php
                                        $discount = round((($value->old_price - $value->new_price) * 100) / $value->old_price);
                                    @endphp
                                    <span class="pro_discount_tag">-{{ $discount }}%</span>
                                @endif
                            </p>
                        </div>

                        {{-- ⭐⭐⭐ BUTTON AREA ⭐⭐⭐ --}}
                        @if ($value->has_variants)
                        {{-- ভ্যারিয়েন্ট আছে = কুইক ভ্যারিয়েন্ট পপআপ মডাল --}}
                        <div class="pro_btn">
                            <button type="button" 
                                class="order-btn-link order-btn quick_variant_modal"
                                data-id="{{ $value->id }}"
                                data-action="order">
                                অর্ডার করুন
                            </button>

                            <button type="button" 
                                class="cart-icon-link cart-icon-btn quick_variant_modal"
                                data-id="{{ $value->id }}"
                                data-action="cart">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </button>
                        </div>
                        @else
                        {{-- ভ্যারিয়েন্ট নেই = Order Now + Add to Cart --}}
                        <div class="pro_btn">

                            {{-- Order Now --}}
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $value->id }}">
                                <input type="hidden" name="qty" value="1">
                                <input type="hidden" name="order_now" value="1">

                                <button type="submit" class="order-btn">
                                    অর্ডার করুন
                                </button>
                            </form>

                            {{-- Add to Cart --}}
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $value->id }}">
                                <input type="hidden" name="qty" value="1">

                                <button type="submit" class="cart-icon-btn cart_store" data-id="{{ $value->id }}">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </button>
                            </form>

                        </div>
                        @endif

                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 🚀 FLOATING STICKY ORDER BAR (Appears when main order button is off-screen) --}}
{{-- 🚀 Floating Sticky Order Bar (when main button is off-screen) --}}
<div id="product_floating_order_bar" class="product-floating-order-bar">
    <div class="container product-floating-inner">
        <div class="product-floating-left">
            <div class="product-floating-thumb">
                <img src="{{ asset($details->image ? $details->image->image : ($details->images->first() ? $details->images->first()->image : '')) }}" alt="{{ $details->name }}">
            </div>
            <div class="product-floating-info">
                <div class="product-floating-title d-none d-md-block">{{ Str::limit($details->name, 45) }}</div>
                <div class="product-floating-price-wrap">
                    <div class="floating-primary-price">
                        <span class="floating-new-price" id="floating_bar_price">৳{{ round($details->new_price) }}</span>
                    </div>
                    @if($details->old_price && $details->old_price > $details->new_price)
                        @php
                            $f_discount = round((($details->old_price - $details->new_price) * 100) / $details->old_price);
                        @endphp
                        <div class="floating-discount-line">
                            <del class="floating-old-price">৳{{ round($details->old_price) }}</del>
                            <span class="floating-discount-badge">-{{ $f_discount }}%</span>
                        </div>
                    @elseif($details->old_price)
                        <div class="floating-discount-line">
                            <del class="floating-old-price">৳{{ round($details->old_price) }}</del>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="product-floating-actions">
            <button type="button" class="btn product-floating-cart-btn" id="floating_add_cart_btn" title="কার্টে যোগ করুন">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="d-none d-md-inline ms-1 fw-bold">কার্ট</span>
            </button>
            <button type="button" class="btn product-floating-order-btn" id="floating_order_now_btn">
                <i class="fa-solid fa-bolt me-1"></i> অর্ডার করুন
            </button>
        </div>
    </div>
</div>

@endsection @push('script')
<script src="{{ asset('public/frontEnd/js/owl.carousel.min.js') }}"></script>

<script src="{{ asset('public/frontEnd/js/zoomsl.min.js') }}"></script>
<script>
    const variants = @json($details->variantPrices);

    @if($details->is_wholesale && $details->wholesalePrices && $details->wholesalePrices->count() > 0)
    var wholesaleTiers = [
        @foreach($details->wholesalePrices->sortBy('min_quantity') as $tier)
        {
            min_quantity: {{ $tier->min_quantity }},
            max_quantity: {{ $tier->max_quantity ?? 999999 }},
            price: {{ $tier->wholesale_price }}
        }@if(!$loop->last),@endif
        @endforeach
    ];
    var regularPrice = {{ $details->new_price }};

    function getWholesalePrice(qty) {
        var matched = null;
        for (var i = 0; i < wholesaleTiers.length; i++) {
            var t = wholesaleTiers[i];
            if (qty >= t.min_quantity && qty <= t.max_quantity) {
                matched = t.price;
            }
        }
        return matched;
    }

    function highlightWholesaleTier(qty) {
        $('.wholesale-tier-row').removeClass('active-tier');
        $('.wholesale-tier-row').each(function() {
            var minQty = parseInt($(this).data('min-qty'), 10);
            var maxQty = parseInt($(this).data('max-qty'), 10);
            if (qty >= minQty && qty <= maxQty) {
                $(this).addClass('active-tier');
            }
        });
    }
    @endif

    function getProductQty() {
        var $qty = $('form[name="formName"] input[name="qty"]');
        if (!$qty.length) {
            $qty = $('.product-qty-input').first();
        }
        if (!$qty.length) {
            $qty = $('input[name="qty"]').first();
        }
        return parseInt($qty.val(), 10) || 1;
    }

    function updateDisplayedPrice() {
        let color = $('form[name="formName"] input[name="product_color"]:checked').val() || null;
        let size  = $('form[name="formName"] input[name="product_size"]:checked').val() || null;

        let match = null;

        // ✅ color + size (both selected)
        if (color && size) {
            match = variants.find(v => {
                let vColorId = v.color_id ?? v.color;
                let vSizeId = v.size_id ?? v.size;
                return String(vColorId) == String(color) && String(vSizeId) == String(size);
            });
        }

        // ✅ only color (no size selected)
        if (!match && color && !size) {
            match = variants.find(v => {
                let vColorId = v.color_id ?? v.color;
                let vSizeId = v.size_id ?? v.size;
                return String(vColorId) == String(color) && (vSizeId === null || vSizeId === '');
            });
        }

        // ✅ only size (no color selected)
        if (!match && size && !color) {
            match = variants.find(v => {
                let vColorId = v.color_id ?? v.color;
                let vSizeId = v.size_id ?? v.size;
                return String(vSizeId) == String(size) && (vColorId === null || vColorId === '');
            });
        }

        // ✅ update UI
        let basePrice = parseFloat({{ $details->new_price }});
        if (match && match.price !== undefined && match.price !== null) {
            // Variant price is the actual price for this color/size combination
            basePrice = parseFloat(match.price);
        }

        // Apply wholesale price if applicable (wholesale price overrides variant price)
        @if($details->is_wholesale && $details->wholesalePrices && $details->wholesalePrices->count() > 0)
        let qty = getProductQty();
        let wholesalePrice = getWholesalePrice(qty);
        if (wholesalePrice !== null) {
            basePrice = parseFloat(wholesalePrice);
        }
        highlightWholesaleTier(qty);
        @endif

        $('#newPrice').text('৳' + Math.round(basePrice));
    }

    $(document).ready(function() {
        updateDisplayedPrice();

        $(document).on(
            'change',
            'form[name="formName"] input[name="product_color"], form[name="formName"] input[name="product_size"], form[name="formName"] input[name="qty"]',
            updateDisplayedPrice
        );

        $('form[name="formName"] input[name="qty"]').on('keyup input', updateDisplayedPrice);

        @if($details->is_wholesale && $details->wholesalePrices && $details->wholesalePrices->count() > 0)
        $('.wholesale-tier-row').on('click', function() {
            var minQty = parseInt($(this).data('min-qty'), 10);
            $('form[name="formName"] input[name="qty"]').val(minQty).trigger('change');
        });
        @endif
    });

    // কালার সিলেক্ট করলে ঐ কালারের ইমেজ দেখাবে
    var productImages = @json($details->images->map(function($img) {
        return ['src' => asset($img->image), 'color_id' => $img->color_id];
    }));

    function updateImagesByColor(colorId) {
        var colorIdStr = colorId ? String(colorId) : null;
        var filteredImages = [];

        if (colorIdStr) {
            var colorSpecific = productImages.filter(function(img) {
                return img.color_id && String(img.color_id) === colorIdStr;
            });
            var defaultImages = productImages.filter(function(img) { return !img.color_id; });
            filteredImages = colorSpecific.length > 0 ? colorSpecific : defaultImages;
        } else {
            filteredImages = productImages.filter(function(img) { return !img.color_id; });
            if (filteredImages.length === 0) filteredImages = productImages;
        }
        if (filteredImages.length === 0) filteredImages = productImages;

        var $slider = $(".details_slider");
        var owl = $slider.data("owl.carousel");
        if (owl) owl.destroy();

        var sliderHtml = filteredImages.map(function(img, i) {
            return '<div class="dimage_item"><img src="' + img.src + '" class="block__pic" /></div>';
        }).join('');
        $slider.html(sliderHtml);

        var thumbHtml = filteredImages.map(function(img, i) {
            return '<div class="indicator-item" data-id="' + i + '"><img src="' + img.src + '" /></div>';
        }).join('');
        var $thumbWrapper = $("#indicator_thumb_wrapper");
        var thumbOwl = $thumbWrapper.data("owl.carousel");
        if (thumbOwl) thumbOwl.destroy();
        $thumbWrapper.removeClass("thumb_slider owl-carousel").html(thumbHtml);
        if (filteredImages.length > 4) {
            $thumbWrapper.addClass("thumb_slider owl-carousel");
            $thumbWrapper.owlCarousel({ margin: 15, items: 4, loop: true, dots: false, nav: true, autoplayTimeout: 6000, autoplayHoverPause: true });
        }

        $slider.owlCarousel({
            margin: 15,
            items: 1,
            loop: filteredImages.length > 1,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
        });

        $(".indicator-item").off("click").on("click", function() {
            var slideIndex = parseInt($(this).data("id"), 10);
            $slider.trigger("to.owl.carousel", slideIndex);
        });

        initProductZoom();
    }

    function initProductZoom() {
        if (window.innerWidth >= 992 && window.matchMedia && window.matchMedia('(hover: hover)').matches) {
            $(document).off('mouseenter.zoom', '.block__pic').on('mouseenter.zoom', '.block__pic', function() {
                var $img = $(this);
                if ($img.data('zoom-initialized') || typeof $.fn.imagezoomsl !== 'function') return;
                $img.data('zoom-initialized', true);
                try {
                    $img.imagezoomsl({
                        zoomrange: [2, 2],
                        magnifierspeedanimate: 0,
                        loadopacity: 1,
                        cursorshade: true,
                        cursorshadeopacity: 0.15
                    });
                } catch(e) {}
            });
        }
    }

    $(document).on("change", "input[name='product_color']", function() {
        updateImagesByColor($(this).val() || null);
    });
</script>



<script>
    $(document).ready(function() {
        $(".details_slider").owlCarousel({
            margin: 15,
            items: 1,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
        });
        $(".indicator-item").on("click", function() {
            var slideIndex = $(this).data("id");
            $(".details_slider").trigger("to.owl.carousel", slideIndex);
        });
    });
</script>
<!--Data Layer Start-->
<script type="text/javascript">
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        ecommerce: null
    });
    dataLayer.push({
        event: "view_item",
        ecommerce: {
            items: [{
                item_name: "{{ $details->name }}",
                item_id: "{{ $details->id }}",
                price: "{{ $details->new_price }}",
                item_brand: "{{ $details->brand?$details->brand->name:'' }}",
                item_category: "{{ $details->category->name }}",
                item_variant: "{{ $details->pro_unit }}",
                currency: "BDT",
                quantity: {{ $details->stock ?? 0 }}
            }],
            impression: [
                @foreach ($products as $value)
                    {
                        item_name: "{{ $value->name }}",
                        item_id: "{{ $value->id }}",
                        price: "{{ $value->new_price }}",
                        item_brand: "{{ $details->brand?$details->brand->name:'' }}",
                        item_category: "{{ $value->category ? $value->category->name : '' }}",
                        item_variant: "{{ $value->pro_unit }}",
                        currency: "BDT",
                        quantity: {{ $value->stock ?? 0 }}
                    },
                @endforeach
            ]
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#add_to_cart').click(function() {
            gtag("event", "add_to_cart", {
                currency: "BDT",
                value: "1.5",
                items: [
                    @foreach (Cart::instance('shopping')->content() as $cartInfo)
                        {
                            item_id: "{{$details->id}}",
                            item_name: "{{$details->name}}",
                            price: "{{$details->new_price}}",
                            currency: "BDT",
                            quantity: {{ $cartInfo->qty ?? 0 }}
                        },
                    @endforeach
                ]
            });
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#order_now').click(function() {
            gtag("event", "add_to_cart", {
                currency: "BDT",
                value: "1.5",
                items: [
                    @foreach (Cart::instance('shopping')->content() as $cartInfo)
                        {
                            item_id: "{{$details->id}}",
                            item_name: "{{$details->name}}",
                            price: "{{$details->new_price}}",
                            currency: "BDT",
                            quantity: {{ $cartInfo->qty ?? 0 }}
                        },
                    @endforeach
                ]
            });
        });
    });
</script>

<!-- Data Layer End-->

{{-- 🔹 নতুন dataLayer + Facebook Pixel ইভেন্ট (আগের কিছু না কেটে শুধু যোগ করা) --}}
<script type="text/javascript">
    window.dataLayer = window.dataLayer || [];

    (function () {

        var productItem = {
            item_id: "{{ $details->id }}",
            item_name: @json($details->name),
            price: {{ (float) $details->new_price }},
            item_brand: @json(optional($details->brand)->name),
            item_category: @json(optional($details->category)->name),
            item_variant: @json($details->pro_unit),
            currency: "BDT",
            quantity: {{ $details->stock ?? 0 }}
        };

        var relatedItems = [
            @foreach ($products as $value)
            {
                item_id: "{{ $value->id }}",
                item_name: @json($value->name),
                price: {{ (float) $value->new_price }},
                item_brand: @json(optional($value->brand)->name),
                item_category: @json(optional($value->category)->name),
                item_variant: @json($value->pro_unit),
                currency: "BDT",
                quantity: {{ $value->stock ?? 0 }}
            }@if(!$loop->last),@endif
            @endforeach
        ];

        // view_item_list (Related products)
        if (relatedItems.length) {
            window.dataLayer.push({
                event: "view_item_list",
                ecommerce: {
                    item_list_name: "Related Products",
                    currency: "BDT",
                    items: relatedItems
                }
            });
        }

        // ViewContent across GA4, Facebook, TikTok
        if (typeof window.EcomTracking !== "undefined") {
            EcomTracking.viewContent({
                items: [{ id: productItem.item_id, name: productItem.item_name, price: productItem.price, qty: 1, category: productItem.item_category, brand: productItem.item_brand }],
                value: productItem.price
            });
        }

        // Helper: qty সহ item তৈরি
        function buildCurrentItem() {
            var qtyInput = document.querySelector("input[name='qty']");
            var qty = parseInt(qtyInput ? qtyInput.value : "1", 10);
            if (isNaN(qty) || qty < 1) qty = 1;

            return {
                item_id: productItem.item_id,
                item_name: productItem.item_name,
                price: productItem.price,
                item_brand: productItem.item_brand,
                item_category: productItem.item_category,
                item_variant: productItem.item_variant,
                currency: "BDT",
                quantity: qty
            };
        }

        // Expose item builder for cart_store AJAX tracking in master layout
        window.getCurrentDetailsItem = buildCurrentItem;

        // "অর্ডার করুন" -> add_to_cart (GA4, FB, TikTok) - Checkout page will handle InitiateCheckout
        $(document).on("click", ".order_now_btn", function () {
            if (typeof sendSuccess === "function" && !sendSuccess()) {
                return;
            }
            var item  = buildCurrentItem();
            var value = item.price * item.quantity;

            if (typeof window.EcomTracking !== "undefined") {
                EcomTracking.addToCart({
                    items: [{ id: item.item_id, name: item.item_name, price: item.price, qty: item.quantity, category: item.item_category, brand: item.item_brand }],
                    value: value
                });
            }
        });

    })();
</script>

<script>
    $(document).ready(function() {
        $(".related_slider").owlCarousel({
            margin: 10,
            items: 6,
            loop: true,
            dots: true,
            nav: true,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: true,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: true,
                    loop: true,
                },
            },
        });
        // $('.owl-nav').remove();
    });
</script>
<script>
    $(document).ready(function() {
        $(document).on('click', 'form[name="formName"] .minus, .qty-cart .minus', function(e) {
            e.preventDefault();
            var $input = $(this).closest('.quantity').find('input[name="qty"]');
            var count = parseInt($input.val(), 10) - 1;
            count = count < 1 ? 1 : count;
            $input.val(count).trigger('change');
            return false;
        });
        $(document).on('click', 'form[name="formName"] .plus, .qty-cart .plus', function(e) {
            e.preventDefault();
            var $input = $(this).closest('.quantity').find('input[name="qty"]');
            var count = parseInt($input.val(), 10) + 1;
            $input.val(count).trigger('change');
            return false;
        });
    });
</script>

<script>
    function sendSuccess() {
        var form = document.forms['formName'];
        if (!form) return true;

        if (form.querySelector('input[name="product_size"]')) {
            if (!form.querySelector('input[name="product_size"]:checked')) {
                toastr.warning('সাইজ সিলেক্ট করুন');
                return false;
            }
        }
        if (form.querySelector('input[name="product_color"]')) {
            if (!form.querySelector('input[name="product_color"]:checked')) {
                toastr.error('রঙ সিলেক্ট করুন');
                return false;
            }
        }

        var qtyInput = form.querySelector('input[name="qty"]');
        if (qtyInput) {
            var q = parseInt(qtyInput.value, 10);
            if (isNaN(q) || q < 1) {
                qtyInput.value = 1;
            } else {
                qtyInput.value = q;
            }
        }
        return true;
    }
</script>
<script>
    $(document).ready(function() {
        $(".rating label").click(function() {
            $(".rating label").removeClass("active");
            $(this).addClass("active");
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".thumb_slider").owlCarousel({
            margin: 15,
            items: 4,
            loop: true,
            dots: false,
            nav: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        if (typeof initProductZoom === 'function') {
            initProductZoom();
        }
    });
</script>
<script>
(function () {
    var btn = document.getElementById('loadMoreProductReviews');
    if (!btn) return;

    btn.addEventListener('click', function () {
        var productId = btn.getAttribute('data-product-id');
        var offset = parseInt(btn.getAttribute('data-offset') || '0', 10);
        var limit = parseInt(btn.getAttribute('data-limit') || '3', 10);
        var list = document.getElementById('productReviewList');
        if (!productId || !list) return;

        btn.disabled = true;
        btn.textContent = 'Loading...';

        $.ajax({
            url: '{{ route('product.reviews.load') }}',
            type: 'GET',
            dataType: 'json',
            data: {
                product_id: productId,
                offset: offset,
                limit: limit
            },
            success: function (res) {
                if (res && res.ok && res.html) {
                    list.insertAdjacentHTML('beforeend', res.html);
                    btn.setAttribute('data-offset', res.loaded);
                }
                if (!res || !res.has_more) {
                    btn.parentElement.removeChild(btn);
                } else {
                    btn.disabled = false;
                    btn.textContent = 'More Review';
                }
            },
            error: function () {
                btn.disabled = false;
                btn.textContent = 'More Review';
                if (typeof toastr !== 'undefined') {
                    toastr.error('রিভিউ লোড করা যায়নি। আবার চেষ্টা করুন।');
                }
            }
        });
    });
})();
</script>
<script>
(function () {
    var wrap = document.querySelector('.product-video-sticky-wrap');
    var col = document.querySelector('.product-video-col');
    if (!wrap || !col) return;

    var headerOffset = 110;
    var spacer = document.createElement('div');
    spacer.className = 'product-video-sticky-spacer';
    wrap.parentNode.insertBefore(spacer, wrap);
    col.style.position = 'relative';

    function resetSticky() {
        wrap.style.position = '';
        wrap.style.top = '';
        wrap.style.left = '';
        wrap.style.width = '';
        wrap.style.zIndex = '';
        spacer.style.display = 'none';
        spacer.style.height = '0';
    }

    function updateStickyVideo() {
        if (window.innerWidth <= 767) {
            resetSticky();
            return;
        }

        var colRect = col.getBoundingClientRect();
        var wrapHeight = wrap.offsetHeight;

        if (colRect.top <= headerOffset && colRect.bottom > headerOffset + wrapHeight) {
            spacer.style.display = 'block';
            spacer.style.height = wrapHeight + 'px';
            wrap.style.position = 'fixed';
            wrap.style.top = headerOffset + 'px';
            wrap.style.left = colRect.left + 'px';
            wrap.style.width = colRect.width + 'px';
            wrap.style.zIndex = '20';
        } else if (colRect.bottom <= headerOffset + wrapHeight) {
            spacer.style.display = 'block';
            spacer.style.height = wrapHeight + 'px';
            wrap.style.position = 'absolute';
            wrap.style.top = (col.offsetHeight - wrapHeight) + 'px';
            wrap.style.left = '0';
            wrap.style.width = '100%';
            wrap.style.zIndex = '20';
        } else {
            resetSticky();
        }
    }

    window.addEventListener('scroll', updateStickyVideo, { passive: true });
    window.addEventListener('resize', updateStickyVideo);
    updateStickyVideo();
})();

// 🚀 Floating Sticky Order Bar (Show when main order button is off-screen)
(function () {
    var mainOrderBtn = document.getElementById('main_order_now_btn');
    var floatingBar = document.getElementById('product_floating_order_bar');
    if (!mainOrderBtn || !floatingBar) return;

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    floatingBar.classList.add('is-visible');
                } else {
                    floatingBar.classList.remove('is-visible');
                }
            });
        }, {
            root: null,
            threshold: 0.1
        });
        observer.observe(mainOrderBtn);
    } else {
        function checkVisibility() {
            var rect = mainOrderBtn.getBoundingClientRect();
            var windowHeight = window.innerHeight || document.documentElement.clientHeight;
            var inView = (rect.bottom > 0 && rect.top < windowHeight);
            if (!inView) {
                floatingBar.classList.add('is-visible');
            } else {
                floatingBar.classList.remove('is-visible');
            }
        }
        window.addEventListener('scroll', checkVisibility, { passive: true });
        window.addEventListener('resize', checkVisibility);
        checkVisibility();
    }

    // Sync floating bar price when #newPrice changes
    var mainPriceEl = document.getElementById('newPrice');
    var floatingPriceEl = document.getElementById('floating_bar_price');
    if (mainPriceEl && floatingPriceEl && 'MutationObserver' in window) {
        var priceObserver = new MutationObserver(function () {
            floatingPriceEl.textContent = mainPriceEl.textContent;
        });
        priceObserver.observe(mainPriceEl, { childList: true, characterData: true, subtree: true });
    }

    function checkVariantsSelected() {
        var form = mainOrderBtn.closest('form');
        if (!form) return true;
        var colorRadios = form.querySelectorAll('input[name="product_color"]');
        if (colorRadios.length > 0 && !form.querySelector('input[name="product_color"]:checked')) {
            if (typeof toastr !== 'undefined') {
                toastr.error('অনুগ্রহ করে একটি কালার সিলেক্ট করুন', 'ভ্যারিয়েন্ট নির্বাচন');
            } else {
                alert('অনুগ্রহ করে একটি কালার সিলেক্ট করুন');
            }
            var colorContainer = document.querySelector('.pro-color');
            if (colorContainer) {
                colorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }

        var sizeRadios = form.querySelectorAll('input[name="product_size"]');
        if (sizeRadios.length > 0 && !form.querySelector('input[name="product_size"]:checked')) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('অনুগ্রহ করে একটি সাইজ সিলেক্ট করুন', 'ভ্যারিয়েন্ট নির্বাচন');
            } else {
                alert('অনুগ্রহ করে একটি সাইজ সিলেক্ট করুন');
            }
            var sizeContainer = document.querySelector('.pro-size');
            if (sizeContainer) {
                sizeContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
        return true;
    }

    var floatingOrderBtn = document.getElementById('floating_order_now_btn');
    if (floatingOrderBtn) {
        floatingOrderBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (checkVariantsSelected()) {
                mainOrderBtn.click();
            }
        });
    }

    var floatingCartBtn = document.getElementById('floating_add_cart_btn');
    var mainCartBtn = document.getElementById('main_add_cart_btn');
    if (floatingCartBtn && mainCartBtn) {
        floatingCartBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (checkVariantsSelected()) {
                mainCartBtn.click();
            }
        });
    }
})();
</script>
@endpush
