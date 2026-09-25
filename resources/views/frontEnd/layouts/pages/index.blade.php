@extends('frontEnd.layouts.master')

@section('title', $seo->meta_title ?? 'Home')

@push('seo')
<meta name="app-url" content="{{ url('/') }}" />
<meta name="robots" content="index, follow" />

<meta name="description" content="{{ $seo->meta_description ?? '' }}" />
<meta name="keywords" content="{{ $seo->meta_tags ?? '' }}" />

<!-- Open Graph data -->
<meta property="og:title" content="{{ $seo->meta_title ?? '' }}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:image" content="{{ asset($generalsetting->og_baner ?? 'public/logo.png') }}" />
<meta property="og:description" content="{{ $seo->meta_description ?? '' }}" />
@endpush

@section('content')
@php
    $hasSliders = isset($sliders) && $sliders->count() > 0;
    $hasMenuCategories = isset($menucategories) && $menucategories->count() > 0;
@endphp

@if($hasSliders || $hasMenuCategories)
<section class="slider-section {{ !$hasSliders ? 'd-none d-sm-block' : '' }}">
    <div class="container">
        <div class="row">

            {{-- LEFT SIDEBAR CATEGORY MENU --}}
            @if($hasMenuCategories)
            <div class="{{ $hasSliders ? 'col-sm-3 hidetosm' : 'col-sm-12' }}">
                <div class="sidebar-menu home-category-sidebar">
                    <ul class="hideshow home-cat-list">
                        @foreach ($menucategories as $key => $category)
                            <li class="home-cat-item">
                                <a href="{{ route('category', $category->slug) }}" class="home-cat-link">
                                    <div class="home-cat-link-left">
                                        @if($category->icon)
                                            <img src="{{ asset($category->icon) }}"
                                                 alt="{{ $category->name }}"
                                                 class="side_cat_img"
                                                 loading="lazy" />
                                        @elseif($category->image)
                                            <img src="{{ asset($category->image) }}"
                                                 alt="{{ $category->name }}"
                                                 class="side_cat_img"
                                                 loading="lazy"
                                                 width="28" height="28" />
                                        @else
                                            <span class="home-cat-icon-fallback"><i class="fa-solid fa-shapes"></i></span>
                                        @endif
                                        <span class="home-cat-name">{{ $category->name }}</span>
                                    </div>
                                    @if($category->subcategories && $category->subcategories->count() > 0)
                                        <i class="fa-solid fa-chevron-right home-cat-arrow"></i>
                                    @endif
                                </a>

                                @if($category->subcategories && $category->subcategories->count() > 0)
                                <ul class="sidebar-submenu home-cat-submenu">
                                    @foreach ($category->subcategories as $subcategory)
                                        <li class="home-subcat-item">
                                            <a href="{{ route('subcategory', $subcategory->slug) }}" class="home-subcat-link">
                                                <span>{{ $subcategory->subcategoryName }}</span>
                                                @if($subcategory->childcategories && $subcategory->childcategories->count() > 0)
                                                    <i class="fa-solid fa-chevron-right home-cat-arrow"></i>
                                                @endif
                                            </a>
                                            @if($subcategory->childcategories && $subcategory->childcategories->count() > 0)
                                            <ul class="sidebar-childmenu home-cat-childmenu">
                                                @foreach ($subcategory->childcategories as $childcat)
                                                    <li class="home-childcat-item">
                                                        <a href="{{ route('products', $childcat->slug) }}" class="home-childcat-link">
                                                            {{ $childcat->childcategoryName }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- MAIN SLIDER --}}
            @if($hasSliders)
            <div class="{{ $hasMenuCategories ? 'col-sm-9' : 'col-sm-12' }}">
                <div class="home-slider-container">
                    <div class="main_slider owl-carousel">
                        @foreach ($sliders as $key => $value)
                            <div class="slider-item">
                                <img src="{{ asset($value->image) }}"
                                     alt="Slider"
                                     class="img-fluid w-100"
                                     @if($key === 0) fetchpriority="high" loading="eager" decoding="async" @else loading="lazy" decoding="async" @endif />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</section>
@endif
<!-- slider end -->

{{-- BOTTOM SLIDER ADS --}}
@if(isset($sliderbottomads) && count($sliderbottomads) > 0)
<section class="bottoads_area">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="bottoads_inner">
                    @foreach ($sliderbottomads as $value)
                        <div class="ads_item">
                            <a href="{{ $value->link }}">
                                <img src="{{ asset($value->image) }}"
                                     alt="Ads"
                                     class="img-fluid"
                                     loading="lazy" />
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<style>
/* 📱 Mobile Slider Height Fix (Eliminate White Gap Under Banner) */
@media (max-width: 767px) {
    .slider-section {
        padding-top: 4px !important;
        padding-bottom: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 4px !important;
    }
    .slider-section .container,
    .slider-section .row,
    .slider-section .col-sm-9 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }
    .home-slider-container {
        border-radius: 10px !important;
        overflow: hidden !important;
        height: auto !important;
        min-height: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
    .main_slider,
    .main_slider .owl-stage-outer,
    .main_slider .owl-stage,
    .main_slider .owl-item,
    .slider-item {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        background: transparent !important;
        margin-bottom: 0 !important;
    }
    .slider-item img {
        width: 100% !important;
        height: auto !important;
        display: block !important;
        border-radius: 10px;
        object-fit: cover !important;
        margin-bottom: 0 !important;
    }
    .bottoads_area {
        display: none !important;
    }
}

/* 🏷️ Fresh White 4-Corner 2-Radius Category Design */
.homeproduct.home-category-section,
.home-category-section {
    padding: 2px 0 8px !important;
    margin-top: 0 !important;
    margin-bottom: 4px !important;
}
.home-category-section .cat-modern-header {
    padding-bottom: 2px !important;
    margin-bottom: 6px !important;
}
.cat_fresh_card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    background: #ffffff !important;
    border: 1px solid #eef2f6;
    border-radius: 14px 0 14px 0 !important;
    padding: 8px 6px 8px;
    text-decoration: none !important;
    outline: none !important;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    overflow: hidden;
    height: 100%;
}
.cat_fresh_card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    border-color: {{ optional($generalsetting)->primary_color ?? '#0f3460' }};
}
.cat_fresh_img_box {
    width: 100%;
    height: 68px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    overflow: hidden;
}
.cat_fresh_img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
    transition: transform 0.3s ease;
}
.cat_fresh_card:hover .cat_fresh_img {
    transform: scale(1.08);
}
.cat_fresh_fallback {
    width: 44px;
    height: 44px;
    border-radius: 10px 0 10px 0;
    background: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.cat_fresh_title_box {
    width: 100%;
    padding-top: 5px;
    text-align: center;
}
.cat_fresh_title {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-size: 11.5px;
    font-weight: 600;
    line-height: 1.25;
    color: #1e293b !important;
    text-align: center;
    transition: color 0.2s ease;
}
.cat_fresh_card:hover .cat_fresh_title {
    color: {{ optional($generalsetting)->primary_color ?? '#0f3460' }} !important;
}

/* 🖥️ Desktop Category Slider with Perfectly Centered <> Arrows */
.cat_desktop_slider_wrap {
    position: relative;
    padding: 0 2px;
}
.category-slider {
    position: relative !important;
}
.category-slider .owl-stage {
    display: flex;
    align-items: stretch;
}
.category-slider .owl-item {
    display: flex;
    height: auto;
}
.category-slider .cat_slider_cell {
    width: 100%;
    height: 100%;
}
.category-slider .owl-nav {
    position: static !important;
    margin: 0 !important;
    padding: 0 !important;
    height: 0 !important;
}
.cat-nav-btn {
    position: absolute !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 34px !important;
    height: 34px !important;
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #0f172a !important;
    font-size: 13px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12) !important;
    cursor: pointer !important;
    z-index: 30 !important;
    transition: all 0.2s ease !important;
    margin: 0 !important;
}
.cat-nav-btn:hover {
    background: {{ optional($generalsetting)->primary_color ?? '#0f3460' }} !important;
    color: #ffffff !important;
    border-color: {{ optional($generalsetting)->primary_color ?? '#0f3460' }} !important;
}
.cat-prev { left: -14px !important; }
.cat-next { right: -14px !important; }

/* 📱 Mobile Category 4x3 Grid (12 Items) */
@media (max-width: 767px) {
    .homeproduct.home-category-section,
    .home-category-section {
        padding: 2px 0 6px !important;
        margin-top: 0 !important;
        margin-bottom: 2px !important;
    }
    .cat_mobile_4x3_grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 5px !important;
        margin-top: 3px !important;
    }
    .cat_fresh_card {
        padding: 5px 3px 5px !important;
        border-radius: 10px 0 10px 0 !important;
    }
    .cat_fresh_img_box {
        height: 48px !important;
    }
    .cat_fresh_title {
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.15 !important;
    }
}
@media (max-width: 380px) {
    .cat_mobile_4x3_grid {
        gap: 4px !important;
    }
    .cat_fresh_card {
        padding: 4px 2px 4px !important;
        border-radius: 8px 0 8px 0 !important;
    }
    .cat_fresh_img_box {
        height: 44px !important;
    }
    .cat_fresh_title {
        font-size: 9.5px !important;
    }
}
</style>

{{-- CATEGORY SECTION (PC: 8-Item Auto-Scroll Single Line Slider | Mobile: 4x3 Fresh White 12-Item Grid) --}}
@php
    $categoriesEnabled = ($generalsetting?->homepage_categories_enabled ?? 1) == 1;
    $homeCategoriesList = isset($popular_categories) && $popular_categories->count() > 0 
        ? $popular_categories 
        : (isset($menucategories) ? $menucategories->filter(fn($c) => ($c->front_view ?? 1) == 1) : collect());
@endphp

@if($categoriesEnabled && $homeCategoriesList->count() > 0)
<section class="homeproduct home-category-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="cat-modern-header">
                    <div class="cat-modern-title-group">
                        <span class="cat-modern-badge"><i class="fa-solid fa-layer-group"></i></span>
                        <div class="cat-modern-text">
                            <h2 class="cat-modern-title">জনপ্রিয় ক্যাটাগরি</h2>
                            <p class="cat-modern-subtitle">আপনার প্রয়োজনীয় ক্যাটাগরি থেকে সহজে কেনাকাটা করুন</p>
                        </div>
                    </div>
                    <a href="{{ route('shop') }}" class="cat-modern-viewall">
                        <span>সব দেখুন</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            {{-- 🖥️ PC & Tablet Slider: 1-line, 8 visible items on PC, auto-scrolls 1 by 1 --}}
            <div class="col-sm-12 d-none d-md-block">
                <div class="cat_desktop_slider_wrap">
                    <div class="category-slider owl-carousel">
                        @foreach ($homeCategoriesList as $value)
                            <div class="cat_slider_cell">
                                <a href="{{ route('category', $value->slug) }}" class="cat_fresh_card">
                                    <div class="cat_fresh_img_box">
                                        @if($value->image)
                                            <img src="{{ asset($value->image) }}"
                                                 alt="{{ $value->name }}"
                                                 class="cat_fresh_img"
                                                 loading="lazy" />
                                        @elseif($value->icon)
                                            <img src="{{ asset($value->icon) }}"
                                                 alt="{{ $value->name }}"
                                                 class="cat_fresh_img"
                                                 loading="lazy" />
                                        @else
                                            <div class="cat_fresh_fallback">
                                                <i class="fa-solid fa-shapes"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="cat_fresh_title_box">
                                        <span class="cat_fresh_title">{{ $value->name }}</span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 📱 Mobile Grid: 4x3 (4 Columns x 3 Rows = 12 Items total), Fresh White cards, 4-corner 2-radius, full picture, zero black gradient --}}
            <div class="col-sm-12 d-block d-md-none">
                <div class="cat_mobile_4x3_grid">
                    @foreach ($homeCategoriesList->take(12) as $value)
                        <a href="{{ route('category', $value->slug) }}" class="cat_fresh_card">
                            <div class="cat_fresh_img_box">
                                @if($value->image)
                                    <img src="{{ asset($value->image) }}"
                                         alt="{{ $value->name }}"
                                         class="cat_fresh_img"
                                         loading="lazy" />
                                @elseif($value->icon)
                                    <img src="{{ asset($value->icon) }}"
                                         alt="{{ $value->name }}"
                                         class="cat_fresh_img"
                                         loading="lazy" />
                                @else
                                    <div class="cat_fresh_fallback">
                                        <i class="fa-solid fa-shapes"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="cat_fresh_title_box">
                                <span class="cat_fresh_title">{{ $value->name }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
@endif

{{-- HOT DEALS BANNER --}}
@if(isset($hitdealsbaner) && count($hitdealsbaner) > 0)
<section>
    <div class="container">
        <div class="row">
            @foreach($hitdealsbaner as $hotads)
            <div class="col-md-12">
                <a href="{{ $hotads->link }}?sold=show">
                    <img class="img-fluid w-100"
                         src="{{ asset($hotads->image) }}"
                         alt="Hot Deals Banner"
                         loading="lazy" />
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- HOT DEAL SECTION --}}
@if(isset($hotdeal_top) && $hotdeal_top->count() > 0)
<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <div class="timer_inner">
                            <div>
                                <span class="section-title-name"> Hot Deal </span>
                            </div>
                            <div>
                                <div class="offer_timer" id="simple_timer"></div>
                            </div>
                        </div>
                    </h3>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="product_slider owl-carousel">
                    @foreach ($hotdeal_top as $key => $value)
                        <div class="product_item wist_item">
                            <div class="product_item_inner">
                                @if($value->old_price)
                                <div class="sale-badge">
                                    <div class="sale-badge-inner">
                                        <div class="sale-badge-box">
                                            <span class="sale-badge-text">
                                                <p>
                                                    @php
                                                        $discount = ((($value->old_price - $value->new_price) * 100) / $value->old_price);
                                                    @endphp
                                                    {{ number_format($discount, 0) }}%
                                                </p>
                                                ছাড়
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="pro_img">
                                    <a href="{{ route('product', $value->slug) }}">
                                        <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                             alt="{{ $value->name }}"
                                             class="img-fluid"
                                             loading="lazy" />
                                    </a>
                                </div>

                                <div class="pro_des">
                                    <div class="pro_name">
                                        <a href="{{ route('product', $value->slug) }}">
                                            {{ Str::limit($value->name, 35) }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @php
                                $averageRating = (float) ($value->reviews_avg_ratting ?? 0);
                                $filledStars   = floor($averageRating);
                                $hasHalfStar   = $averageRating - $filledStars >= 0.5;
                                $emptyStars    = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                            @endphp

                            @if ($averageRating >= 0 && $averageRating <= 5)
                                @for ($i = 0; $i < $filledStars; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                                @if ($hasHalfStar)
                                    <i class="fas fa-star-half-alt"></i>
                                @endif
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="far fa-star"></i>
                                @endfor
                            @else
                                <span>Invalid rating range</span>
                            @endif

                            <div class="pro_price">
                                <p>
                                    @if($value->old_price)
                                        <del>৳ {{ $value->old_price }}</del>
                                    @endif
                                    ৳ {{ $value->new_price }}
                                </p>
                            </div>

                            {{-- দুইটা বাটন: অর্ডার + কার্ট --}}
                            @if ($value->has_variants)
                                {{-- ভ্যারিয়েন্ট প্রোডাক্ট – পপআপ মডাল --}}
                                <div class="pro_btn">
                                    <button type="button" class="order-btn-link order-btn quick_variant_modal" data-id="{{ $value->id }}" data-action="order">
                                        অর্ডার করুন
                                    </button>
                                    <button type="button" class="cart-icon-link cart-icon-btn quick_variant_modal" data-id="{{ $value->id }}" data-action="cart">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </button>
                                </div>
                            @else
                                {{-- সিম্পল প্রোডাক্ট --}}
                                <div class="pro_btn">
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $value->id }}" />
                                        <input type="hidden" name="qty" value="1" />
                                        <input type="hidden" name="order_now" value="1">
                                        <button type="submit" class="order-btn">অর্ডার করুন</button>
                                    </form>

                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $value->id }}" />
                                        <input type="hidden" name="qty" value="1" />
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
@endif



{{-- HOMEPAGE ADS --}}
@if(isset($homepageads) && count($homepageads) > 0)
<section class="homepage-ads-block">
    <div class="container">
        <div class="row">
            @foreach($homepageads as $homeads)
            <div class="col-md-12">
                <a href="{{ $homeads->link }}?sold=show">
                    <img class="img-fluid w-100"
                         src="{{ asset($homeads->image) }}"
                         alt="Homepage Ads"
                         loading="lazy" />
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CATEGORY WISE HOME PRODUCTS --}}
@if($homeproducts && $homeproducts->count() > 0)
    @foreach ($homeproducts as $homecat)
        @if(isset($homecat->products) && $homecat->products->count() > 0)
        <section class="homeproduct category-wise-product-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="sec_title">
                            <h3 class="section-title-header">
                                <span class="section-title-name">{{ $homecat->name }}</span>
                                <a href="{{ route('category', $homecat->slug) }}" class="view_more_btn">
                                    View More
                                </a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="product_slider owl-carousel">
                            @foreach ($homecat->products as $key => $value)
                                <div class="product_item wist_item">
                                    <div class="product_item_inner">
                                        @if($value->old_price)
                                        <div class="sale-badge">
                                            <div class="sale-badge-inner">
                                                <div class="sale-badge-box">
                                                    <span class="sale-badge-text">
                                                        <p>
                                                            @php
                                                                $discount = ((($value->old_price - $value->new_price) * 100) / $value->old_price);
                                                            @endphp
                                                            {{ number_format($discount, 0) }}%
                                                        </p>
                                                        ছাড়
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="pro_img">
                                            <a href="{{ route('product', $value->slug) }}">
                                                <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                                     alt="{{ $value->name }}"
                                                     class="img-fluid"
                                                     loading="lazy" />
                                            </a>
                                        </div>

                                        <div class="pro_des">
                                            <div class="pro_name">
                                                <a href="{{ route('product', $value->slug) }}">
                                                    {{ Str::limit($value->name, 35) }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $averageRating = (float) ($value->reviews_avg_ratting ?? 0);
                                        $filledStars   = floor($averageRating);
                                        $hasHalfStar   = $averageRating - $filledStars >= 0.5;
                                        $emptyStars    = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                                    @endphp

                                    @if ($averageRating >= 0 && $averageRating <= 5)
                                        @for ($i = 0; $i < $filledStars; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                        @if ($hasHalfStar)
                                            <i class="fas fa-star-half-alt"></i>
                                        @endif
                                        @for ($i = 0; $i < $emptyStars; $i++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    @else
                                        <span>Invalid rating range</span>
                                    @endif

                                    <div class="pro_price">
                                        <p>
                                            @if($value->old_price)
                                                <del>৳ {{ $value->old_price }}</del>
                                            @endif
                                            ৳ {{ $value->new_price }}
                                        </p>
                                    </div>

                                    {{-- দুইটা বাটন: অর্ডার + কার্ট --}}
                                    @if ($value->has_variants)
                                        <div class="pro_btn">
                                            <button type="button" class="order-btn-link order-btn quick_variant_modal" data-id="{{ $value->id }}" data-action="order">
                                                অর্ডার করুন
                                            </button>
                                            <button type="button" class="cart-icon-link cart-icon-btn quick_variant_modal" data-id="{{ $value->id }}" data-action="cart">
                                                <i class="fa-solid fa-cart-shopping"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="pro_btn">
                                            <form action="{{ route('cart.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $value->id }}" />
                                                <input type="hidden" name="qty" value="1" />
                                                <input type="hidden" name="order_now" value="1">
                                                <button type="submit" class="order-btn">অর্ডার করুন</button>
                                            </form>

                                            <form action="{{ route('cart.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $value->id }}" />
                                                <input type="hidden" name="qty" value="1" />
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
        @endif
    @endforeach
@endif

{{-- HOMEPAGE ADS 2 --}}
@if(isset($homepageads2) && count($homepageads2) > 0)
<section class="homepage-ads-block">
    <div class="container">
        <div class="row">
            @foreach($homepageads2 as $homeads2)
            <div class="col-md-12">
                <a href="{{ $homeads2->link }}?sold=show">
                    <img class="img-fluid w-100"
                         src="{{ asset($homeads2->image) }}"
                         alt="Homepage Ads 2"
                         loading="lazy" />
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif





{{-- BRAND SECTION --}}
@if(($generalsetting?->homepage_brands_enabled ?? 1) == 1 && isset($brands) && $brands->count() > 0)
<section class="homeproduct brand-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <span class="section-title-name">Brands</span>
                    </h3>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="row brand-grid">

                    @foreach($brands as $brand)
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                            <a href="{{ route('brand.products', $brand->slug) }}"
                               class="brand-item text-center">

                                <div class="brand-img">
                                    <img src="{{ asset($brand->image) }}"
                                         alt="{{ $brand->name }}"
                                         class="img-fluid"
                                         loading="lazy">
                                </div>

                                <div class="brand-name">
                                    {{ $brand->name }}
                                </div>

                            </a>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- VENDOR SHOPS SECTION --}}
@if(($generalsetting?->homepage_vendors_enabled ?? 1) == 1 && ($generalsetting?->vendor_enabled ?? 1) == 1 && isset($vendors) && $vendors->count() > 0)
<section class="homeproduct vendor-shops-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <span class="section-title-name">Our Featured Shops</span>
                    </h3>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="row vendor-shop-grid">
                    @foreach($vendors as $vendor)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                        <a href="{{ route('vendor.shop', $vendor->slug) }}" class="vendor-shop-item">
                            {{-- Background Banner --}}
                            <div class="shop-banner-bg" style="background-image: url('{{ $vendor->banner ? asset($vendor->banner) : asset('public/frontEnd/images/default-banner.jpg') }}');">
                            </div>
                            
                            {{-- Shop Logo & Info --}}
                            <div class="shop-content-wrapper">
                                <div class="shop-logo-container">
                                    <div class="shop-logo-circle">
                                        @if($vendor->logo)
                                            <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->shop_name }}" />
                                        @else
                                            <div class="shop-logo-initial">
                                                {{ strtoupper(substr($vendor->shop_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    @if($vendor->verification_status == 'approved')
                                    <div class="shop-verified-badge">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="shop-details">
                                    <h4 class="shop-title">{{ $vendor->shop_name }}</h4>
                                    
                                    {{-- Rating --}}
                                    <div class="shop-rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($vendor->average_rating))
                                                <i class="fas fa-star"></i>
                                            @elseif($i - 0.5 <= $vendor->average_rating)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                        <span class="shop-review-text">({{ $vendor->total_reviews }} reviews)</span>
                                    </div>
                                </div>
                                
                                {{-- Visit Store Button --}}
                                <div class="shop-visit-btn">
                                    <span class="visit-btn-icon"><i class="fas fa-arrow-right"></i></span>
                                    <span class="visit-btn-text">VISIT STORE</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if(($generalsetting?->homepage_blogs_enabled ?? 1) == 1 && isset($blogs) && $blogs->count() > 0)
<section class="homeproduct blog-home-section">
    <div class="container">

        {{-- Section Title --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <span class="section-title-name">Latest Blogs</span>
                        <a href="{{ route('blogs') }}" class="view_more_btn">
                            View All
                        </a>
                    </h3>
                </div>
            </div>
        </div>

        {{-- Blog Grid --}}
        <div class="row">

            @foreach($blogs as $blog)
            <div class="col-lg-4 col-md-6 mb-4">

                <div class="blog-home-card">

                    {{-- Image --}}
                    <div class="blog-home-img">
                        <a href="{{ route('blog.details', $blog->slug) }}">
                            @if($blog->image)
                        <img 
                            src="{{ url('public/'.$blog->image) }}"
                            alt="{{ $blog->title }}"
                            loading="lazy"
                            width="100%"
                            height="220"
                        >
                    @else
                        <img 
                            src="{{ url('public/no-image.png') }}"
                            alt="No Image"
                            loading="lazy"
                            width="100%"
                            height="220"
                        >
                    @endif
                        </a>
                    </div>

                    {{-- Content --}}
                    <div class="blog-home-content">

                        <div class="blog-home-meta">
                           {{ $blog->created_at->format('d M Y') }}
                            |{{ $blog->views }}
                        </div>

                        <h5 class="blog-home-title">
                            <a href="{{ route('blog.details', $blog->slug) }}">
                                {{ Str::limit($blog->title, 55) }}
                            </a>
                        </h5>

                        <p>
                            {{ Str::limit($blog->short_description, 110) }}
                        </p>

                        <a href="{{ route('blog.details', $blog->slug) }}"
                           class="read-more-link">
                            Read More →
                        </a>

                    </div>

                </div>

            </div>
            @endforeach

        </div>

    </div>
</section>
@endif

<div class="homepage-before-footer-gap" aria-hidden="true"></div>

<style>
.homepage-ads-block {
    padding: 10px 0 20px;
    margin-bottom: 20px;
    background: #fff;
}
.homepage-before-footer-gap {
    height: 48px;
    background: #fff;
}
.blog-home-section,
.vendor-shops-section {
    margin-bottom: 24px;
    padding-bottom: 16px;
}
@media (max-width: 767px) {
    .homepage-before-footer-gap { height: 32px; }
    .homepage-ads-block { margin-bottom: 16px; padding-bottom: 16px; }
}
</style>









<style>
/* ===== CLEAR BRAND LOGO SECTION ===== */
.brand-section {
    background: #ffffff;
    margin-bottom: 24px;
    padding-bottom: 16px;
}

/* brand card */
.brand-section .brand-item {
    display: block;
    background: #ffffff;
    border-radius: 10px;
    padding: 20px 15px;
    text-decoration: none;
    border: 1px solid #eaeaea;
    transition: all 0.3s ease;
}

.brand-section .brand-item:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transform: translateY(-4px);
}

/* logo container */
.brand-section .brand-img {
    height: 95px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff; /* white bg for clarity */
}

/* LOGO IMAGE – FULL CLEAR */
.brand-section .brand-img img {
    max-height: 80px;
    max-width: 100%;
    object-fit: contain;

    /* IMPORTANT FOR CLEAR LOGO */
    filter: none !important;
    opacity: 1 !important;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
}

/* brand name */
.brand-section .brand-name {
    margin-top: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #000;
    text-align: center;
}

/* mobile */
@media (max-width: 576px) {
    .brand-section .brand-img {
        height: 75px;
    }
    .brand-section .brand-img img {
        max-height: 55px;
    }
}

</style>


















<style>
.blog-home-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #eee;
    height: 100%;
    transition: all .3s ease;
}

.blog-home-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.08);
}

.blog-home-img img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.blog-home-content {
    padding: 16px;
}

.blog-home-meta {
    font-size: 13px;
    color: #777;
    margin-bottom: 6px;
}

.blog-home-title a {
    font-size: 17px;
    font-weight: 600;
    color: #222;
    text-decoration: none;
}

.blog-home-title a:hover {
    color: #0d6efd;
}

.read-more-link {
    display: inline-block;
    margin-top: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #0d6efd;
    text-decoration: none;
}

.read-more-link:hover {
    text-decoration: underline;
}

/* ===== VENDOR SHOPS SECTION ===== */
.vendor-shops-section {
    background: #ffffff;
}

.vendor-shop-item {
    display: block;
    position: relative;
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    text-decoration: none;
    border: 1px solid #eaeaea;
    transition: all 0.3s ease;
    height: 100%;
}

.vendor-shop-item:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transform: translateY(-4px);
    text-decoration: none;
}

/* Background Banner */
.shop-banner-bg {
    position: relative;
    width: 100%;
    height: 100px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

/* Shop Content Wrapper */
.shop-content-wrapper {
    position: relative;
    padding: 15px;
    text-align: center;
    padding-top: 50px;
}

/* Logo Container */
.shop-logo-container {
    position: relative;
    margin-top: -50px;
    margin-bottom: 12px;
    display: flex;
    justify-content: center;
}

.shop-logo-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #ffffff;
    border: 4px solid #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.shop-logo-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.shop-logo-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: bold;
    color: #fff;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Verified Badge */
.shop-verified-badge {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 24px;
    height: 24px;
    background: #0d6efd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.shop-verified-badge i {
    color: #ffffff;
    font-size: 12px;
}

/* Shop Details */
.shop-details {
    margin-bottom: 12px;
}

.shop-title {
    font-size: 15px;
    font-weight: 600;
    color: #222;
    margin: 0 0 4px 0;
    line-height: 1.3;
}

.shop-type {
    font-size: 11px;
    color: #666;
    margin: 0 0 8px 0;
}

.shop-rating-stars {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2px;
    margin-bottom: 0;
}

.shop-rating-stars i {
    font-size: 11px;
    color: #ffc107;
}

.shop-rating-stars .far.fa-star {
    color: #ddd;
}

.shop-review-text {
    font-size: 10px;
    color: #777;
    margin-left: 4px;
}

/* Visit Store Button */
.shop-visit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 8px 12px;
    background: #f0f0f0;
    border-radius: 20px;
    color: #333;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-top: 8px;
}

.vendor-shop-item:hover .shop-visit-btn {
    background: #0d6efd;
    color: #ffffff;
}

.visit-btn-icon {
    width: 24px;
    height: 24px;
    background: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.visit-btn-icon i {
    font-size: 10px;
    color: #333;
    transition: all 0.3s ease;
}

.vendor-shop-item:hover .visit-btn-icon {
    background: rgba(255,255,255,0.2);
}

.vendor-shop-item:hover .visit-btn-icon i {
    color: #ffffff;
}

/* Responsive */
@media (max-width: 768px) {
    .shop-banner-bg {
        height: 80px;
    }
    
    .shop-logo-circle {
        width: 70px;
        height: 70px;
    }
    
    .shop-content-wrapper {
        padding-top: 40px;
    }
    
    .shop-title {
        font-size: 14px;
    }
    
    .shop-type {
        font-size: 10px;
    }
}

@media (max-width: 576px) {
    .shop-banner-bg {
        height: 70px;
    }
    
    .shop-logo-circle {
        width: 60px;
        height: 60px;
    }
    
    .shop-content-wrapper {
        padding: 12px;
        padding-top: 35px;
    }
    
    .shop-logo-initial {
        font-size: 28px;
    }
}
</style>








@endsection


@push('script')
<script src="{{ asset('public/frontEnd/js/jquery.syotimer.min.js') }}"></script>
<script>
    $("#simple_timer").syotimer({
        date: new Date(2015, 0, 1),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",
        periodUnit: "d",
        periodic: true,
        periodInterval: 1,
    });

    // 🎯 Dynamically Sync PC Slider Height with Left Category Menu
    function syncHeroSliderHeight() {
        if (window.innerWidth >= 992) {
            var sidebar = document.querySelector('.home-category-sidebar');
            if (sidebar) {
                var sidebarHeight = sidebar.offsetHeight;
                if (sidebarHeight > 220) {
                    var sliderContainer = document.querySelector('.home-slider-container');
                    var sliderItems = document.querySelectorAll('.main_slider .slider-item');
                    if (sliderContainer) {
                        sliderContainer.style.height = sidebarHeight + 'px';
                    }
                    sliderItems.forEach(function(item) {
                        item.style.height = sidebarHeight + 'px';
                    });
                }
            }
        } else {
            var sliderContainer = document.querySelector('.home-slider-container');
            var sliderItems = document.querySelectorAll('.main_slider .slider-item');
            if (sliderContainer) sliderContainer.style.height = '';
            sliderItems.forEach(function(item) {
                item.style.height = '';
            });
        }
    }

    window.addEventListener('load', syncHeroSliderHeight);
    window.addEventListener('resize', syncHeroSliderHeight);
    setTimeout(syncHeroSliderHeight, 300);
    setTimeout(syncHeroSliderHeight, 1000);
</script>
@endpush
