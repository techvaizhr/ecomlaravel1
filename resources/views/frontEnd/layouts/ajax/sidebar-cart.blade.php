@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal = floatval(preg_replace('/[^\d.]/', '', $subtotal));
    $count = Cart::instance('shopping')->count();
    $primaryColor = optional($generalsetting)->primary_color ?? '#007bff';
    $secondaryColor = optional($generalsetting)->secodery_color ?? '#ff6600';
@endphp

{{-- 🛍️ Mini Cart Drawer Header --}}
<div class="sidebar-cart-header">
    <div class="sidebar-cart-header-main">
        <div class="sidebar-cart-header-title">
            <div class="sidebar-cart-header-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div class="sidebar-cart-header-text">
                <h3>আপনার শপিং ব্যাগ</h3>
                <span class="sidebar-cart-item-count">মোট <strong>{{ $count }}</strong> টি পণ্য</span>
            </div>
        </div>
        <button type="button" class="sidebar-cart-close" onclick="closeSidebarCart()" aria-label="বন্ধ করুন" title="বন্ধ করুন">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @if($count > 0)
    <div class="sidebar-cart-delivery-tip">
        <i class="fa-solid fa-truck-fast"></i>
        <span>সারাদেশে দ্রুত হোম ডেলিভারি ও ক্যাশ অন ডেলিভারি সুবিধা</span>
    </div>
    @endif
</div>

{{-- 🛒 Mini Cart Drawer Items Body --}}
<div class="sidebar-cart-body">
    @if($count > 0)
        <div class="sidebar-cart-items-list">
            @foreach(Cart::instance('shopping')->content() as $value)
            <div class="sidebar-cart-item-card">
                <div class="sidebar-cart-item-img">
                    <a href="{{ route('product', $value->options->slug ?? '#') }}">
                        <img src="{{ asset($value->options->image ?? 'public/uploads/default.webp') }}" alt="{{ $value->name }}" loading="lazy">
                    </a>
                </div>
                <div class="sidebar-cart-item-info">
                    <div class="sidebar-cart-item-top">
                        <a href="{{ route('product', $value->options->slug ?? '#') }}" class="sidebar-cart-item-name" title="{{ $value->name }}">
                            {{ Str::limit($value->name, 42) }}
                        </a>
                        <button type="button" class="sidebar-cart-item-del cart_remove" data-id="{{ $value->rowId }}" title="পণ্যটি মুছে ফেলুন" aria-label="মুছে ফেলুন">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>

                    @if(!empty($value->options->product_size) || !empty($value->options->product_color))
                    <div class="sidebar-cart-item-variants">
                        @if(!empty($value->options->product_size))
                            <span class="sidebar-cart-variant-tag"><i class="fa-solid fa-ruler-combined"></i> {{ $value->options->product_size }}</span>
                        @endif
                        @if(!empty($value->options->product_color))
                            <span class="sidebar-cart-variant-tag"><i class="fa-solid fa-palette"></i> {{ $value->options->product_color }}</span>
                        @endif
                    </div>
                    @endif

                    <div class="sidebar-cart-item-bottom">
                        <div class="sidebar-cart-item-pricing">
                            <span class="sidebar-cart-current-price">৳ {{ number_format($value->price, 0) }}</span>
                            @if(!empty($value->options->old_price) && $value->options->old_price > $value->price)
                                <del class="sidebar-cart-old-price">৳ {{ number_format($value->options->old_price, 0) }}</del>
                            @endif
                        </div>

                        <div class="sidebar-cart-qty-pill">
                            <button type="button" class="sidebar-qty-action cart_decrement" data-id="{{ $value->rowId }}" title="পরিমাণ কমান" aria-label="কমান">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <span class="sidebar-qty-val">{{ $value->qty }}</span>
                            <button type="button" class="sidebar-qty-action cart_increment" data-id="{{ $value->rowId }}" title="পরিমাণ বাড়ান" aria-label=" বাড়ান">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="sidebar-cart-empty-state">
            <div class="sidebar-cart-empty-icon-box">
                <div class="sidebar-cart-empty-icon-inner">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                </div>
            </div>
            <h4>আপনার কার্ট বর্তমানে খালি!</h4>
            <p>আপনার পছন্দের পণ্যগুলো কার্টে যোগ করে সহজে অর্ডার করুন।</p>
            <a href="{{ route('shop') }}" class="sidebar-cart-shop-now-btn" onclick="closeSidebarCart()">
                <i class="fa-solid fa-bag-shopping"></i> শপিং শুরু করুন
            </a>
        </div>
    @endif
</div>

{{-- 💳 Mini Cart Drawer Footer --}}
@if($count > 0)
<div class="sidebar-cart-footer">
    <div class="sidebar-cart-summary">
        <div class="sidebar-cart-subtotal-row">
            <span class="sidebar-cart-subtotal-text">সাবটোটাল</span>
            <span class="sidebar-cart-subtotal-price">৳ {{ number_format($subtotal, 0) }}</span>
        </div>
        <div class="sidebar-cart-tax-hint">ডেলিভারি চার্জ চেকআউটে হিসাব করা হবে</div>
    </div>

    <div class="sidebar-cart-actions">
        <a href="{{ route('customer.checkout') }}" class="sidebar-cart-btn-checkout">
            <span>অর্ডার করুন (চেকআউট)</span>
            <i class="fa-solid fa-arrow-right-long"></i>
        </a>
        <div class="sidebar-cart-secondary-actions">
            <a href="{{ route('cart.show') }}" class="sidebar-cart-btn-view">
                <i class="fa-solid fa-cart-shopping"></i> কার্ট পেজ
            </a>
            <button type="button" class="sidebar-cart-btn-continue" onclick="closeSidebarCart()">
                আরও কেনাকাটা <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-cart-trust-row">
        <span><i class="fa-solid fa-shield-check"></i> ১০০% নিরাপদ চেকআউট</span>
        <span><i class="fa-solid fa-rotate-left"></i> সহজ রিটার্ন পলিসি</span>
    </div>
</div>
@endif
