<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>@yield('title')</title>
		@if(!empty($seo->search_console_verification))
{!! $seo->search_console_verification ?? '' !!}
@endif
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="Super Ecommerce Favicon" />
        <meta name="author" content="Super Ecommerce" />
        <link rel="canonical" href="" />
        @stack('seo') 
        @stack('css')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800&family=Lato:wght@300;400;700&family=Roboto:wght@300;400;500;700&display=swap">
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
        <link rel="preconnect" href="https://maxcdn.bootstrapcdn.com" crossorigin>
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/animate.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/all.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/owl.carousel.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/owl.theme.default.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/mobile-menu.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/select2.min.css')}}" />
        <!-- toastr css -->
        <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets/css/toastr.min.css" />

        <link rel="stylesheet" href="{{asset('public/frontEnd/css/wsit-menu.css')}}" />
<link rel="stylesheet" href="{{ url('/style.css') }}?v=3">
<link rel="stylesheet" href="{{ url('/responsive.css') }}?v=3">
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/main.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/news-ticker.css')}}" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
        <meta name="facebook-domain-verification" content="38f1w8335btoklo88dyfl63ba3st2e" />
        <style>
            /* 🚀 CRITICAL VIEWPORT & LAYOUT STABILIZATION (Smooth Page Load & Zero Flash) */
            html {
                overflow-x: hidden;
                background-color: #ffffff;
            }
            body {
                overflow-x: hidden;
                width: 100%;
                background-color: #ffffff;
                position: relative;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            #loading {
                display: none !important;
            }
            /* Smooth stable product display without jarring WOW zoomIn flashing */
            .product_item {
                visibility: visible !important;
                animation: none !important;
                transform: none !important;
                opacity: 1 !important;
                transition: transform 0.2s ease, box-shadow 0.2s ease !important;
            }
            .product_item:hover {
                transform: translateY(-3px) !important;
            }
            .wow {
                visibility: visible !important;
            }
            .sidebar-cart-drawer:not(.active) {
                visibility: hidden !important;
                pointer-events: none !important;
            }
            .mobile-menu:not(.active) {
                visibility: hidden !important;
                pointer-events: none !important;
            }
            @media (min-width: 768px) {
                .mobile-menu {
                    display: none !important;
                }
            }
            .owl-carousel:not(.owl-loaded) {
                display: flex;
                overflow: hidden;
                flex-wrap: nowrap;
            }
            .owl-carousel:not(.owl-loaded) > *:not(:first-child) {
                display: none;
            }

            .float{
            	position:fixed;
            	color:white;
            	width:60px;
            	height:60px;
            	bottom:40px;
            	left:40px;
            	background-color:#25d366;
            	color:#FFF;
            	border-radius:50px;
            	text-align:center;
                font-size:30px;
            	box-shadow: 2px 2px 3px #999;
                z-index:100;
            }
            
            .my-float{
            	margin-top:16px;
            }
            /* Media query to hide the .float class on screens 768px and smaller */
            @media (max-width: 767px) {
                .float {
                    display: none;
                }
            }
        </style>
		<style>
/* ═══════════════════════════════════════════════════════════════
   🌟 ULTRA-MODERN PREMIUM STORE FOOTER DESIGN
   ═══════════════════════════════════════════════════════════════ */
.footer-v2 {
    background: linear-gradient(180deg, {{ optional($generalsetting)->footer_color ?? '#111827' }} 0%, #090d16 100%);
    color: #e2e8f0;
    font-family: inherit;
    position: relative;
    overflow: hidden;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
}

/* Glowing Top Accent Line */
.footer-v2__wave {
    height: 3px;
    width: 100%;
    background: linear-gradient(90deg, transparent 0%, {{ optional($generalsetting)->primary_color ?? '#667eea' }} 25%, {{ optional($generalsetting)->secodery_color ?? '#3b82f6' }} 75%, transparent 100%);
    opacity: 0.9;
}

/* 🛡️ Store Trust Features Strip */
.footer-trust-strip {
    padding: 24px 0 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.02);
}
.footer-trust-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 16px;
}
.footer-trust-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 12px;
    transition: all 0.25s ease;
}
.footer-trust-card:hover {
    background: rgba(255, 255, 255, 0.07);
    border-color: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}
.footer-trust-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: linear-gradient(135deg, {{ optional($generalsetting)->primary_color ?? '#667eea' }}25 0%, {{ optional($generalsetting)->secodery_color ?? '#3b82f6' }}25 100%);
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.footer-trust-info h6 {
    font-size: 13.5px;
    font-weight: 700;
    color: #ffffff !important;
    margin: 0 0 2px;
    line-height: 1.2;
}
.footer-trust-info p {
    font-size: 11.5px;
    color: #94a3b8 !important;
    margin: 0;
    line-height: 1.3;
}

@media (max-width: 991px) {
    .footer-trust-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
}
@media (max-width: 540px) {
    .footer-trust-grid { grid-template-columns: 1fr; gap: 8px; }
    .footer-trust-card { padding: 10px 12px; }
}

/* Main Footer Content */
.footer-v2__main {
    padding: 3.5rem 1.25rem 2.5rem;
    box-sizing: border-box;
}
.footer-v2__grid {
    display: grid;
    grid-template-columns: 1.8fr 1fr 1fr 1.3fr;
    gap: 2.5rem;
    max-width: 1240px;
    margin: 0 auto;
}
@media (max-width: 1100px) {
    .footer-v2__grid { grid-template-columns: 1.5fr 1fr 1fr 1.2fr; gap: 2rem; }
}
@media (max-width: 890px) {
    .footer-v2__grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
}
@media (max-width: 576px) {
    .footer-v2__grid { grid-template-columns: 1fr; gap: 1.75rem; }
}

/* Brand block */
.footer-v2__logo {
    display: inline-block;
    margin-bottom: 1rem;
    transition: transform 0.2s;
}
.footer-v2__logo:hover {
    transform: scale(1.02);
}
.footer-v2__logo img {
    max-height: 48px;
    width: auto;
    object-fit: contain;
}
.footer-v2__tagline {
    font-size: 13.5px;
    line-height: 1.65;
    color: #94a3b8 !important;
    margin-bottom: 1.25rem;
    max-width: 340px;
}

/* Contact Pills */
.footer-contact-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 1.25rem;
}
.footer-contact-item {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #cbd5e1 !important;
    text-decoration: none !important;
    transition: color 0.2s;
}
.footer-contact-item:hover {
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
}
.footer-contact-item i {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}

/* App Download Badges */
.footer-v2__apps-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #cbd5e1 !important;
    margin-bottom: 0.65rem;
}
.footer-v2__app-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
}
.footer-v2__app-badges a img {
    height: 38px;
    width: auto;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.18);
    background: rgba(0, 0, 0, 0.3);
    transition: all 0.25s ease;
}
.footer-v2__app-badges a:hover img {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.35);
    border-color: rgba(255, 255, 255, 0.35);
}

/* Column Headings */
.footer-v2__title {
    font-size: 15px;
    font-weight: 700;
    color: #ffffff !important;
    margin-bottom: 1.25rem;
    position: relative;
    padding-bottom: 8px;
    display: inline-block;
    letter-spacing: 0.3px;
}
.footer-v2__title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 32px;
    height: 2.5px;
    background: linear-gradient(90deg, {{ optional($generalsetting)->primary_color ?? '#667eea' }}, transparent);
    border-radius: 2px;
}

/* Links */
.footer-v2__links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.footer-v2__links li a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    font-size: 13.5px;
    color: #94a3b8 !important;
    transition: all 0.2s ease;
    line-height: 1.4;
}
.footer-v2__links li a::before {
    content: '›';
    font-size: 15px;
    font-weight: 700;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    opacity: 0.6;
    transition: transform 0.2s ease, opacity 0.2s;
}
.footer-v2__links li a:hover {
    color: #ffffff !important;
    transform: translateX(4px);
}
.footer-v2__links li a:hover::before {
    opacity: 1;
    transform: translateX(2px);
}

/* Newsletter Box */
.footer-v2__newsletter-desc {
    font-size: 13px;
    color: #94a3b8 !important;
    line-height: 1.5;
    margin-bottom: 1rem;
}
.footer-v2__form {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 1.5rem;
    width: 100%;
}
.footer-newsletter-wrap {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.06);
    border: 1.5px solid rgba(255, 255, 255, 0.12);
    border-radius: 50px;
    padding: 4px 5px 4px 14px;
    transition: all 0.25s ease;
}
.footer-newsletter-wrap:focus-within {
    background: rgba(255, 255, 255, 0.09);
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    box-shadow: 0 0 0 3px {{ optional($generalsetting)->primary_color ?? '#667eea' }}25;
}
.footer-newsletter-wrap input {
    flex: 1;
    min-width: 0;
    background: transparent !important;
    border: none !important;
    outline: none !important;
    color: #ffffff !important;
    font-size: 13px !important;
    padding: 6px 0 !important;
}
.footer-newsletter-wrap input::placeholder {
    color: #64748b !important;
}
.footer-newsletter-wrap button {
    padding: 8px 18px;
    border-radius: 50px;
    border: none;
    background: linear-gradient(135deg, {{ optional($generalsetting)->primary_color ?? '#667eea' }} 0%, {{ optional($generalsetting)->secodery_color ?? '#3b82f6' }} 100%);
    color: #ffffff !important;
    font-weight: 700;
    font-size: 12.5px;
    white-space: nowrap;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    box-shadow: 0 3px 10px {{ optional($generalsetting)->primary_color ?? '#667eea' }}40;
}
.footer-newsletter-wrap button:hover {
    transform: scale(1.03);
    filter: brightness(1.1);
}

/* Social icons */
.footer-v2__social-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #cbd5e1 !important;
    margin-bottom: 0.75rem;
}
.footer-v2__social-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-v2__social-list a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #cbd5e1 !important;
    font-size: 15px;
    transition: all 0.25s ease;
}
.footer-v2__social-list a:hover {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    color: #ffffff !important;
    transform: translateY(-3px);
    box-shadow: 0 6px 16px {{ optional($generalsetting)->primary_color ?? '#667eea' }}50;
}

/* Bottom Copyright Bar */
.footer-v2__bottom {
    background: {{ optional($generalsetting)->copyright_color ?? '#060a12' }};
    padding: 1.25rem 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
}
.footer-v2__copy-wrap {
    max-width: 1240px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 13px;
    color: #64748b !important;
}
.footer-v2__copy-text {
    color: #94a3b8 !important;
}
.footer-v2__copy-text strong {
    color: #f1f5f9;
}
.footer-v2__designer {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #64748b;
}
.footer-v2__designer-link {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #e2e8f0 !important;
    text-decoration: none !important;
    font-weight: 600;
    font-size: 12px;
    transition: all 0.2s;
}
.footer-v2__designer-link:hover {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    color: #ffffff !important;
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}

@media (max-width: 768px) {
    .footer-v2__bottom {
        padding-bottom: 95px;
    }
    .footer-v2__copy-wrap {
        justify-content: center;
        text-align: center;
    }
}

/* ═══════════════════════════════════════════════════════════════
   ✨ MODERN & SLEEK FRONTEND HEADER (Search, Track, Cart, Categories & Menu)
   ═══════════════════════════════════════════════════════════════ */

/* Header Area & Layout */
.logo-area {
    padding: 12px 0;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}
.logo-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}
.main-logo {
    flex-shrink: 0;
    max-height: 52px;
}
.main-logo img {
    max-height: 48px;
    width: auto;
    object-fit: contain;
    transition: transform 0.2s ease;
}
.main-logo a:hover img {
    transform: scale(1.02);
}

/* 🔍 Modern E-Commerce Search Box (Sleek Rounded Pill Style) */
.main-search {
    flex: 1;
    max-width: 600px;
    margin: 0 auto;
    position: relative;
}
.main-search form.search-form-v2 {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 2px solid {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    border-radius: 50px;
    height: 44px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.25s ease;
    overflow: hidden;
    padding: 0;
}
.main-search form.search-form-v2:focus-within {
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08), 0 0 0 3px {{ optional($generalsetting)->primary_color ?? '#667eea' }}25;
}
.search-input-group {
    flex: 1;
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 16px;
}
.search-icon-left {
    color: #94a3b8;
    font-size: 14px;
    margin-right: 10px;
    flex-shrink: 0;
}
.main-search form input {
    flex: 1;
    border: none !important;
    outline: none !important;
    background: transparent !important;
    font-size: 14px !important;
    color: #1e293b;
    padding: 0 !important;
    height: 100% !important;
    width: 100% !important;
    float: none !important;
}
.main-search form input::placeholder {
    color: #94a3b8;
    font-size: 13.5px;
}
.search-submit-btn {
    flex-shrink: 0;
    height: 100% !important;
    padding: 0 22px !important;
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    cursor: pointer;
    border-radius: 0 50px 50px 0 !important;
    transition: all 0.2s ease;
    float: none !important;
}
.search-submit-btn:hover {
    filter: brightness(1.08);
}
.search-submit-btn svg,
.search-submit-btn i {
    width: 15px;
    height: 15px;
    stroke: #ffffff;
    color: #ffffff;
}

/* 🔍 Modern Auto-suggest Search Popup */
.search_product {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 100%;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.14);
    border: 1px solid #e2e8f0;
    z-index: 999999;
    overflow: hidden;
    max-height: 400px;
    overflow-y: auto;
}
.search_product ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.search_product li {
    display: flex !important;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.search_product li:last-child {
    border-bottom: none;
}
.search_product li:hover {
    background: #f8fafc;
}
.search_product img {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
    margin: 0;
}
.search_content .name {
    font-weight: 600;
    font-size: 13.5px;
    color: #1e293b;
}
.search_content .price {
    font-weight: 700;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    font-size: 13px;
}

/* 🚚 Track Order & 🛒 Shopping Cart (Header Actions) */
.header-list-items {
    margin: 0;
    text-align: end;
}
.header-list-items ul {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.header-list-items ul li {
    margin: 0 !important;
}

/* Track Order Button */
.track_btn a {
    display: inline-flex !important;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 50px;
    background: #f1f5f9;
    color: #334155 !important;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    transition: all 0.2s ease;
}
.track_btn a i {
    font-size: 14px;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    transition: transform 0.2s ease;
}
.track_btn a:hover {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }}15;
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }}50;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
    transform: translateY(-1px);
}
.track_btn a:hover i {
    transform: translateX(2px);
}

/* 🛒 Modern Cart Button */
.cart-dialog {
    position: relative;
}
.cart-dialog > a {
    display: inline-flex !important;
    align-items: center;
    text-decoration: none;
}
.cart-dialog .margin-shopping {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }}15;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
    position: relative;
    margin: 0 !important;
    transition: all 0.2s ease;
    border: 1px solid {{ optional($generalsetting)->primary_color ?? '#667eea' }}30;
}
.cart-dialog .margin-shopping i {
    font-size: 18px;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}
.cart-dialog .margin-shopping span {
    position: absolute;
    top: -4px;
    right: -4px;
    min-width: 20px;
    height: 20px;
    border-radius: 50px;
    background: {{ optional($generalsetting)->primary_color ?? '#e11d48' }};
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    border: 2px solid #ffffff;
}
.cart-dialog:hover .margin-shopping {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    transform: scale(1.05);
}
.cart-dialog:hover .margin-shopping i {
    color: #ffffff !important;
}

/* 🛒 Modern Cart Dropdown Summary */
.cshort-summary {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 340px;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.16);
    border: 1px solid #e2e8f0;
    padding: 16px;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transform: translateY(8px);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.cart-dialog:hover .cshort-summary {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.cshort-summary ul {
    list-style: none;
    padding: 0;
    margin: 0 0 12px 0;
    max-height: 250px;
    overflow-y: auto;
    display: block !important;
}
.cshort-summary ul li {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}
.cshort-summary img {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    object-fit: cover;
    padding: 0;
}
.cshort-summary .go_cart {
    display: block;
    width: 100%;
    padding: 10px 16px;
    border-radius: 10px;
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    color: #ffffff !important;
    text-align: center;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px {{ optional($generalsetting)->primary_color ?? '#667eea' }}40;
}
.cshort-summary .go_cart:hover {
    filter: brightness(1.08);
    transform: translateY(-1px);
}

/* ═══════════════════════════════════════════════════════════════
   📂 MODERN CATEGORY & NAVIGATION MENU BAR
   ═══════════════════════════════════════════════════════════════ */
.menu-area {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    border: none;
    position: relative;
    z-index: 990;
}
.catagory_menu {
    padding: 0;
}
.heder__category {
    display: flex;
    align-items: center;
    list-style: none;
    padding: 0;
    margin: 0;
    min-height: 48px;
    gap: 6px;
}
.heder__category > div {
    display: flex;
    align-items: center;
}

/* ALL CATEGORIES BUTTON */
li.all__category__list {
    background: {{ optional($generalsetting)->secodery_color ?? '#1e293b' }};
    min-width: 250px;
    height: 48px;
    display: flex !important;
    align-items: center;
    padding: 0 18px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px;
    cursor: pointer;
    position: relative;
    border-radius: 0;
    transition: background 0.2s ease;
}
li.all__category__list > a {
    color: #ffffff !important;
    text-decoration: none;
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin: 0 !important;
}
li.all__category__list > a i {
    font-size: 15px;
    padding: 0 !important;
    transition: transform 0.2s ease;
}
li.all__category__list:hover > a i {
    transform: rotate(90deg);
}

/* CATEGORY DROPDOWN SIDEBAR */
.side__bar {
    position: absolute;
    left: 0;
    top: 100%;
    width: 250px;
    background: #ffffff !important;
    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
    border-radius: 0 0 12px 12px;
    border: 1px solid #e2e8f0;
    border-top: none;
    overflow: visible;
    z-index: 1000;
}
.side__bar .hideshow {
    list-style: none;
    padding: 6px 0;
    margin: 0;
}
.side__bar .hideshow > li {
    position: relative;
    transition: all 0.2s ease;
}
.side__bar .hideshow > li > a {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    padding: 9px 16px !important;
    color: #334155 !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    text-decoration: none;
    transition: all 0.2s ease;
}
.side__bar .hideshow > li > a img {
    width: 20px;
    height: 20px;
    object-fit: contain;
    margin-right: 10px;
    border-radius: 4px;
}
.side__bar .hideshow > li > a i {
    font-size: 11px;
    color: #94a3b8;
    transition: transform 0.2s ease;
}
.side__bar .hideshow > li:hover > a {
    background: #f8fafc !important;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
    padding-left: 20px !important;
}
.side__bar .hideshow > li:hover > a i {
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
    transform: translateX(3px);
}

/* Submenu Flyout */
.side__barsub,
.side__barchild {
    position: absolute;
    left: 100%;
    top: 0;
    width: 230px;
    background: #ffffff !important;
    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    list-style: none;
    padding: 6px 0;
    margin: 0;
    display: none;
    z-index: 1001;
}
.side__bar .hideshow > li:hover > .side__barsub {
    display: block;
}
.side__barsub > li:hover > .side__barchild {
    display: block;
}
.side__barsub li a,
.side__barchild li a {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px !important;
    color: #334155 !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    text-decoration: none;
    transition: all 0.2s ease;
}
.side__barsub li:hover > a,
.side__barchild li:hover > a {
    background: #f8fafc !important;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
    padding-left: 20px !important;
}

/* Menu Nav Links (Home, Sellers, Contact) */
.catagory_menu ul.heder__category li a {
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
}
.catagory_menu ul.heder__category li a:hover {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff !important;
}

/* Right Menu Account / Login Pill */
.right__menu__top {
    margin-left: auto;
}
.right__menu__top .for_order p {
    margin: 0;
}
.right__menu__top .for_order a {
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.18) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    padding: 6px 14px !important;
    border-radius: 50px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #ffffff !important;
    transition: all 0.2s ease;
}
.right__menu__top .for_order a:hover {
    background: #ffffff !important;
    color: {{ optional($generalsetting)->primary_color ?? '#1e293b' }} !important;
    transform: translateY(-1px);
}
.right__menu__top .for_order a:hover i {
    color: {{ optional($generalsetting)->primary_color ?? '#1e293b' }} !important;
}

/* 📱 Mobile Header & Mobile Search Refinement */
.mobile-header {
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    padding: 8px 12px;
}
.mobile-search {
    padding: 8px 12px;
    background: #ffffff;
}
.mobile-search form {
    display: flex;
    align-items: center;
    background: #f1f5f9;
    border-radius: 50px;
    padding: 3px 6px 3px 14px;
    border: 1.5px solid #e2e8f0;
}
.mobile-search form:focus-within {
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    background: #ffffff;
}
.mobile-search input {
    flex: 1;
    border: none !important;
    outline: none !important;
    background: transparent !important;
    font-size: 13px !important;
    color: #1e293b;
    padding: 0 !important;
}
.mobile-search button {
    width: 34px !important;
    height: 34px;
    border-radius: 50%;
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    border: none;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
}
/* 📱 ULTRA-MODERN & BEAUTIFUL MOBILE SIDE MENU (DRAWER) */
.mobile-menu {
    position: fixed;
    top: 0;
    left: 0;
    width: 320px;
    max-width: 86vw;
    height: 100vh;
    height: 100dvh;
    background: #ffffff;
    z-index: 999999;
    box-shadow: 12px 0 45px rgba(15, 23, 42, 0.25);
    display: flex;
    flex-direction: column;
    transform: translateX(-100%);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
    font-family: inherit;
}
.mobile-menu.active {
    transform: translateX(0);
}

/* Header */
.mobile-menu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    flex-shrink: 0;
}
.mobile-menu-logo .logo-image img {
    height: 36px;
    max-width: 150px;
    width: auto;
    object-fit: contain;
}
.mobile-menu-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f8fafc;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s ease;
    border: 1px solid #e2e8f0;
    font-size: 15px;
}
.mobile-menu-close:hover,
.mobile-menu-close:active {
    background: #fee2e2;
    color: #ef4444;
    border-color: #fecaca;
    transform: rotate(90deg);
}

/* Mobile Menu Scrollable Container */
.mobile-menu-scrollable {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
.mobile-menu-scrollable::-webkit-scrollbar {
    width: 4px;
}
.mobile-menu-scrollable::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

/* Mobile Menu Section Header */
.mobile-menu-section-header {
    padding: 10px 14px 6px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mobile-menu-section-header-text {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mobile-menu-section-header-text i {
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}

/* Primary Navigation Links */
.mobile-menu-nav-links {
    padding: 8px 10px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    border-bottom: 1px solid #f1f5f9;
}
.mobile-nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 10px;
    background: #ffffff;
    color: #1e293b !important;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 600;
    transition: all 0.2s ease;
    border: 1px solid #f1f5f9;
}
.mobile-nav-link:hover,
.mobile-nav-link.active {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
}
.mobile-nav-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    flex-shrink: 0;
    transition: all 0.2s;
}
.mobile-nav-link:hover .mobile-nav-icon,
.mobile-nav-link.active .mobile-nav-icon {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    color: #ffffff;
}
.mobile-nav-text {
    flex: 1;
    min-width: 0;
}
.mobile-nav-arrow {
    font-size: 11px;
    color: #94a3b8;
    transition: transform 0.2s;
}
.mobile-nav-link:hover .mobile-nav-arrow {
    transform: translateX(2px);
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}

/* Category Title Banner */
.mobile-menu-cat-title {
    padding: 10px 16px 8px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mobile-menu-cat-title-text {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mobile-menu-cat-title-text i {
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}
.mobile-menu-cat-badge {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }}18;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
}

/* Category Navigation List (Tight & Compact) */
.first-nav {
    padding: 6px 8px 16px;
    margin: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.first-nav .parent-category {
    position: relative;
    border-radius: 8px;
    border: 1px solid #f1f5f9;
    transition: all 0.15s ease;
    background: #ffffff;
    overflow: hidden;
}
.first-nav .parent-category:hover,
.first-nav .parent-category.active {
    background: #ffffff;
    border-color: #cbd5e1;
}

.mobile-cat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 2px 6px;
    width: 100%;
}

.first-nav .parent-category a.menu-category-name {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 0;
    padding: 2px 0;
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    text-decoration: none;
    transition: color 0.15s ease;
}
.first-nav .parent-category.active > .mobile-cat-row a.menu-category-name,
.first-nav .parent-category a.menu-category-name:hover {
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}

.mobile-cat-icon-box {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
    padding: 2px;
}
.mobile-cat-icon-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.mobile-cat-icon-box i {
    font-size: 11px;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}

.mobile-cat-text {
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 13px;
    font-weight: 500;
}

/* Category Expand Toggle Button */
.menu-category-toggle {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}
.menu-category-toggle:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.menu-category-toggle.active {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    color: #ffffff;
    transform: rotate(180deg);
}
.menu-category-toggle i {
    font-size: 9px;
    transition: transform 0.2s ease;
}

/* Subcategories (2nd level) */
.second-nav {
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    margin: 0;
    padding: 3px 6px 5px 24px;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.parent-subcategory {
    position: relative;
    border-radius: 4px;
    margin-bottom: 1px;
}
.mobile-subcat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1px 2px;
}
.parent-subcategory a.menu-subcategory-name {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 0;
    padding: 3px 4px;
    font-size: 12px;
    font-weight: 500;
    color: #334155;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.15s ease;
}
.parent-subcategory a.menu-subcategory-name:hover,
.parent-subcategory.active > .mobile-subcat-row a.menu-subcategory-name {
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}
.subcat-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #cbd5e1;
    flex-shrink: 0;
    transition: background 0.15s;
}
.parent-subcategory.active .subcat-dot,
.parent-subcategory:hover .subcat-dot {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}

.menu-subcategory-toggle {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}
.menu-subcategory-toggle.active {
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }}20;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    border-color: {{ optional($generalsetting)->primary_color ?? '#667eea' }}50;
    transform: rotate(180deg);
}
.menu-subcategory-toggle i {
    font-size: 8px;
}

/* Child categories (3rd level) */
.third-nav {
    background: #f8fafc;
    border-left: 2.5px solid {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    border-radius: 0 8px 8px 0;
    margin: 4px 0 6px 14px;
    padding: 4px 6px;
    list-style: none;
}
.childcategory a.menu-childcategory-name {
    display: block;
    padding: 5px 8px;
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.15s ease;
}
.childcategory a.menu-childcategory-name:hover {
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
    background: #ffffff;
}

/* Mobile Menu Footer Quick Actions */
.mobile-menu-footer {
    padding: 10px 12px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex-shrink: 0;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.04);
}
.mobile-menu-action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #334155 !important;
    text-decoration: none !important;
    font-size: 12.5px;
    font-weight: 600;
    transition: all 0.2s ease;
}
.mobile-menu-action-btn i {
    font-size: 14px;
    color: {{ optional($generalsetting)->primary_color ?? '#667eea' }};
}
.mobile-menu-action-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.mobile-menu-support-row {
    display: flex;
    gap: 6px;
}
.mobile-menu-btn-phone,
.mobile-menu-btn-wa {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.2s ease;
}
.mobile-menu-btn-phone {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8 !important;
}
.mobile-menu-btn-phone:hover {
    background: #dbeafe;
}
.mobile-menu-btn-wa {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #047857 !important;
}
.mobile-menu-btn-wa:hover {
    background: #d1fae5;
}

/* 🔍 Mobile Search Form - Matching PC Aesthetic */
.mobile-search {
    background: #ffffff;
    padding: 6px 12px 8px !important;
    position: relative;
    border-bottom: 1px solid #f1f5f9;
}
.mobile-search form.mobile-search-form {
    display: flex !important;
    align-items: center !important;
    background: #ffffff !important;
    border: 2px solid {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
    border-radius: 50px !important;
    height: 42px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
    overflow: hidden !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
}
.mobile-search form.mobile-search-form:focus-within {
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08), 0 0 0 3px {{ optional($generalsetting)->primary_color ?? '#667eea' }}20 !important;
}
.mobile-search form.mobile-search-form .search-input-group {
    flex: 1 !important;
    display: flex !important;
    align-items: center !important;
    height: 100% !important;
    padding: 0 14px !important;
}
.mobile-search form.mobile-search-form .search-icon-left {
    color: #94a3b8;
    font-size: 13px;
    margin-right: 8px;
    flex-shrink: 0;
}
.mobile-search form.mobile-search-form input.msearch_keyword {
    flex: 1 !important;
    border: none !important;
    outline: none !important;
    background: transparent !important;
    font-size: 13.5px !important;
    color: #1e293b !important;
    padding: 0 !important;
    height: 100% !important;
    width: 100% !important;
    float: none !important;
}
.mobile-search form.mobile-search-form .search-submit-btn {
    flex-shrink: 0 !important;
    height: 100% !important;
    min-width: 90px !important;
    padding: 0 20px !important;
    background: {{ optional($generalsetting)->primary_color ?? '#667eea' }} !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 13.5px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    cursor: pointer;
    border-radius: 0 50px 50px 0 !important;
    float: none !important;
    position: static !important;
    letter-spacing: 0.3px;
    white-space: nowrap !important;
}
.mobile-search form.mobile-search-form .search-submit-btn:hover {
    filter: brightness(1.08);
}
.mobile-search form.mobile-search-form .search-submit-btn svg,
.mobile-search form.mobile-search-form .search-submit-btn i {
    width: 14px;
    height: 14px;
    stroke: #ffffff;
    color: #ffffff;
}
/* ═══════════════════════════════════════════════════════════════
   📂 HERO BANNER LEFT CATEGORY SIDEBAR MENU
   ═══════════════════════════════════════════════════════════════ */
.slider-section .sidebar-menu,
.home-category-sidebar {
    background: #ffffff !important;
    border-radius: 8px !important;
    border: 1px solid #e8ecf2 !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04) !important;
    padding: 4px 0 !important;
    margin: 0 !important;
    position: relative !important;
    z-index: 99 !important;
    overflow: visible !important;
}

.home-cat-list {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    overflow: visible !important;
}

.home-cat-item {
    position: relative !important;
    margin: 0 !important;
    list-style: none !important;
}

.home-cat-link {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    padding: 7px 12px !important;
    color: #2d3748 !important;
    text-decoration: none !important;
    transition: all 0.18s ease;
    background: transparent;
    border-bottom: 1px solid #f8fafc;
}

.home-cat-link-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1;
}

.home-cat-link .side_cat_img {
    width: 22px;
    height: 22px;
    object-fit: contain;
    border-radius: 4px;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}

.home-cat-icon-fallback {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: #64748b;
    flex-shrink: 0;
}

.home-cat-name {
    font-size: 13.5px;
    font-weight: 500;
    color: #334155;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: color 0.2s ease;
}

.home-cat-arrow {
    font-size: 11px;
    color: #94a3b8;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

/* Hover States */
.home-cat-item:hover > .home-cat-link {
    background: #f0f7ff !important;
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }} !important;
}
.home-cat-item:hover > .home-cat-link .home-cat-name {
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }} !important;
    font-weight: 600;
}
.home-cat-item:hover > .home-cat-link .home-cat-arrow {
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }} !important;
    transform: translateX(2px);
}

/* Submenu Flyout (Level 2) */
.home-cat-submenu {
    position: absolute !important;
    left: 100% !important;
    top: 0 !important;
    width: 230px !important;
    min-width: 220px !important;
    background: #ffffff !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15) !important;
    list-style: none !important;
    padding: 6px 0 !important;
    margin: 0 !important;
    display: none !important;
    z-index: 9999 !important;
}

.home-cat-item:hover > .home-cat-submenu {
    display: block !important;
}

.home-subcat-item {
    position: relative !important;
    margin: 0 !important;
    list-style: none !important;
}

.home-subcat-link {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    text-decoration: none !important;
    transition: all 0.18s ease;
}

.home-subcat-item:hover > .home-subcat-link {
    background: #f0f7ff !important;
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }} !important;
    font-weight: 600 !important;
    padding-left: 18px !important;
}

/* Childmenu Flyout (Level 3) */
.home-cat-childmenu {
    position: absolute !important;
    left: 100% !important;
    top: 0 !important;
    width: 210px !important;
    background: #ffffff !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15) !important;
    list-style: none !important;
    padding: 6px 0 !important;
    margin: 0 !important;
    display: none !important;
    z-index: 10000 !important;
}

.home-subcat-item:hover > .home-cat-childmenu {
    display: block !important;
}

.home-childcat-item {
    margin: 0 !important;
    list-style: none !important;
}

.home-childcat-link {
    display: block !important;
    padding: 7px 14px !important;
    font-size: 12.5px !important;
    color: #475569 !important;
    text-decoration: none !important;
    transition: all 0.18s ease;
}

.home-childcat-item:hover > .home-childcat-link {
    background: #f0f7ff !important;
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }} !important;
    font-weight: 600 !important;
    padding-left: 18px !important;
}
    background: #f0f7ff !important;
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }} !important;
    font-weight: 600 !important;
    transform: translateX(3px);
}

/* 📐 SPACING: Header bottom, Menu & Banner/Slider section */
#navbar_top,
.main-header,
.menu-area {
    margin-bottom: 0 !important;
}
section.slider-section {
    margin-top: 10px !important;
    margin-bottom: 12px !important;
    padding-top: 0 !important;
}
.home-slider-container {
    padding-top: 0 !important;
    margin-top: 0 !important;
}
.sidebar-menu {
    margin-top: 0 !important;
}

@media (max-width: 768px) {
    .mobile-header {
        margin-bottom: 0 !important;
    }
    .mobile-search {
        padding: 4px 10px !important;
        margin-bottom: 0 !important;
    }
    section.slider-section {
        margin-top: 8px !important;
        padding-top: 0 !important;
    }
}
</style>
        @include('frontEnd.layouts.partials.deferred-tracking')
    </head>
    <body class="gotop">
        @foreach($gtm_code ?? [] as $gtm)
        @php $gtm_noscript_id = preg_match('/^GTM-/i', trim($gtm->code)) ? trim($gtm->code) : 'GTM-'.trim($gtm->code); @endphp
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm_noscript_id }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        @endforeach
        @php $subtotal = Cart::instance('shopping')->subtotal(); @endphp
        <div class="mobile-menu">
            {{-- 1. Header (Logo & Close) --}}
            <div class="mobile-menu-header">
                <div class="mobile-menu-logo">
                    <a href="{{route('home')}}" class="logo-image">
                        <img src="{{asset($generalsetting->dark_logo)}}" alt="{{ $generalsetting->name ?? 'Logo' }}" />
                    </a>
                </div>
                <div class="mobile-menu-close" title="মেনু বন্ধ করুন" role="button" aria-label="মেনু বন্ধ করুন">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>

            {{-- 2. Scrollable Body: Clean Vertical Collapsible Categories --}}
            <div class="mobile-menu-scrollable">

                {{-- Categories Tree List --}}
                <ul class="first-nav">
                    @foreach($menucategories as $scategory)
                    <li class="parent-category">
                        <div class="mobile-cat-row">
                            <a href="{{url('category/'.$scategory->slug)}}" class="menu-category-name">
                                <div class="mobile-cat-icon-box">
                                    @if($scategory->icon)
                                        <img src="{{ asset($scategory->icon) }}" alt="{{ $scategory->name }}" loading="lazy" />
                                    @elseif($scategory->image)
                                        <img src="{{ asset($scategory->image) }}" alt="{{ $scategory->name }}" loading="lazy" />
                                    @else
                                        <i class="fa-solid fa-shapes"></i>
                                    @endif
                                </div>
                                <span class="mobile-cat-text">{{$scategory->name}}</span>
                            </a>
                            @if($scategory->subcategories && $scategory->subcategories->count() > 0)
                            <span class="menu-category-toggle" title="সাবক্যাটাগরি দেখুন">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                            @endif
                        </div>

                        @if($scategory->subcategories && $scategory->subcategories->count() > 0)
                        <ul class="second-nav" style="display: none;">
                            @foreach($scategory->subcategories as $subcategory)
                            <li class="parent-subcategory">
                                <div class="mobile-subcat-row">
                                    <a href="{{url('subcategory/'.$subcategory->slug)}}" class="menu-subcategory-name">
                                        <span class="subcat-dot"></span>
                                        <span>{{$subcategory->subcategoryName}}</span>
                                    </a>
                                    @if($subcategory->childcategories && $subcategory->childcategories->count() > 0)
                                    <span class="menu-subcategory-toggle">
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </span>
                                    @endif
                                </div>

                                @if($subcategory->childcategories && $subcategory->childcategories->count() > 0)
                                <ul class="third-nav" style="display: none;">
                                    @foreach($subcategory->childcategories as $childcat)
                                    <li class="childcategory">
                                        <a href="{{url('products/'.$childcat->slug)}}" class="menu-childcategory-name">
                                            {{$childcat->childcategoryName}}
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

            {{-- 3. Fixed Footer Actions --}}
            <div class="mobile-menu-footer">
                @if(Auth::guard('customer')->check())
                    <a href="{{ route('customer.account') }}" class="mobile-menu-action-btn">
                        <i class="fa-solid fa-circle-user"></i>
                        <span>আমার একাউন্ট ({{ Str::limit(Auth::guard('customer')->user()->name, 14) }})</span>
                    </a>
                @else
                    <a href="{{ route('customer.login') }}" class="mobile-menu-action-btn">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>লগইন / রেজিস্টার</span>
                    </a>
                @endif
                <div class="mobile-menu-support-row">
                    @if(!empty($contact->phone))
                    <a href="tel:{{ $contact->phone }}" class="mobile-menu-btn-phone">
                        <i class="fa-solid fa-phone"></i>
                        <span>{{ $contact->phone }}</span>
                    </a>
                    @endif
                    @if(!empty($contact->whatsapp))
                    <a href="https://wa.me/{{ preg_replace('/[^\d]/', '', $contact->whatsapp) }}" target="_blank" rel="noopener" class="mobile-menu-btn-wa">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <header id="navbar_top">

        @include('frontEnd.layouts.partials.breaking-news-ticker')

            <div class="mobile-header sticky">
                <div class="mobile-logo">
                    <div class="menu-logo">
                        <a href="{{route('home')}}"><img src="{{asset($generalsetting->dark_logo)}}" alt="{{ $generalsetting->name ?? 'Logo' }}" /></a>
                    </div>
                    <div class="mobile-header-actions">
                        <div class="menu-bag">
                            <a href="javascript:void(0)" onclick="openSidebarCart()" class="margin-shopping" aria-label="শপিং কার্ট">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span class="mobilecart-qty">{{ Cart::instance('shopping')->count() }}</span>
                            </a>
                        </div>
                        <div class="menu-bar">
                            <a href="javascript:void(0)" class="toggle" aria-label="মেনু খুলুন" title="মেনু খুলুন">
                                <i class="fa-solid fa-bars"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mobile-search">
                <form action="{{route('search')}}" class="search-form-v2 mobile-search-form">
                    <div class="search-input-group">
                        <i class="fa fa-search search-icon-left"></i>
                        <input type="text" placeholder="পছন্দের পণ্য খুঁজুন..." class="msearch_keyword msearch_click" name="keyword" autocomplete="off" />
                    </div>
                    <button type="submit" class="search-submit-btn">
                        <span>খুঁজুন</span> <i data-feather="search"></i>
                    </button>
                </form>
                <div class="search_result"></div>
            </div>

            <div class="main-header">
                <!-- header to end -->
                <div class="logo-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="logo-header">
                                    <div class="main-logo">
                                        <a href="{{route('home')}}"><img src="{{asset($generalsetting->dark_logo)}}" alt="" /></a>
                                    </div>
                                    <div class="main-search">
                                        <form action="{{route('search')}}" class="search-form-v2">
                                            <div class="search-input-group">
                                                <i class="fa fa-search search-icon-left"></i>
                                                <input type="text" placeholder="পছন্দের পণ্য খুঁজুন..." class="search_keyword search_click" name="keyword" autocomplete="off" />
                                            </div>
                                            <button type="submit" class="search-submit-btn">
                                                <span>খুঁজুন</span> <i data-feather="search"></i>
                                            </button>
                                        </form>
                                        <div class="search_result"></div>
                                    </div>
                                    <div class="header-list-items">
                                        <ul>
                                            <li class="track_btn">
                                                <a href="{{route('customer.order_track')}}"> <i class="fa fa-truck"></i>Track Order</a>
                                            </li>
                                           

                                            <li class="cart-dialog" id="cart-qty">
                                                <a href="{{route('customer.checkout')}}">
                                                    <p class="margin-shopping">
                                                        <i class="fa-solid fa-cart-shopping"></i>
                                                        <span>{{Cart::instance('shopping')->count()}}</span>
                                                    </p>
                                                </a>
                                                <div class="cshort-summary">
                                                    <ul>
                                                        @foreach(Cart::instance('shopping')->content() as $key=>$value)
                                                        <li>
                                                            <a href=""><img src="{{asset($value->options->image)}}" alt="" /></a>
                                                        </li>
                                                        <li><a href="">{{Str::limit($value->name, 30)}}</a></li>
                                                        <li>Qty: {{$value->qty}}</li>
                                                        <li>
                                                            <p>৳{{$value->price}}</p>
                                                            <button class="remove-cart cart_remove" data-id="{{$value->rowId}}"><i data-feather="x"></i></button>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    <p><strong>সর্বমোট : ৳{{$subtotal}}</strong></p>
                                                    <a href="{{route('customer.checkout')}}" class="go_cart"> অর্ডার করুন </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="menu-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="catagory_menu">
                                    <ul class="heder__category">
                                        <div>
                                            <li class="all__category__list">
                                                <a href="#">ALL CATEGORIES <i class="fa-solid fa-list"></i>
                                                </a>
                                                @if(Request::is('/'))
                                                <div></div>
                                                @else
                                                <div class="sidebar-menu side__bar">
                                                    <ul class="hideshow">
                                                        @foreach ($menucategories as $key => $category)
                                                            <li>
                                                                <a href="{{ route('category', $category->slug) }}">
                                                                    <img src="{{ asset($category->image) }}" alt="" />
                                                                    {{ $category->name }}
                                                                    <i class="fa-solid fa-chevron-right"></i>
                                                                </a>
                                                                <ul class="sidebar-submenu side__barsub">
                                                                    @foreach ($category->subcategories as $key => $subcategory)
                                                                        <li>
                                                                            <a href="{{ route('subcategory', $subcategory->slug) }}">
                                                                                {{ $subcategory->subcategoryName }} <i
                                                                                    class="fa-solid fa-chevron-right"></i> </a>
                                                                            <ul class="sidebar-childmenu side__barchild">
                                                                                @foreach ($subcategory->childcategories as $key => $childcat)
                                                                                    <li>
                                                                                        <a href="{{ route('products', $childcat->slug) }}">
                                                                                            {{ $childcat->childcategoryName }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @endif
                                           </li> 
                                        </div>


                                        <div> <li><a href="{{route('home')}}">Home</a></li></div>
                                        @if(($generalsetting?->vendor_enabled ?? 1) == 1)
                                        <div><li><a href="{{route('sellers')}}">Sellers</a></li></div>
                                        @endif
										

                                        <div class="contact__menu"><li><a href="{{route('contact')}}">Contact</a></li></div>
                                       <div class="right__menu__top">
                                            @if(Auth::guard('customer')->user())
                                            <li class="for_order">
                                                <p>
                                                    <a href="{{route('customer.account')}}">
                                                        <i class="fa-regular fa-user"></i>
                                                        {{Str::limit(Auth::guard('customer')->user()->name,14)}}
                                                    </a>
                                                </p>
                                            </li>
                                            @elseif(($generalsetting?->vendor_enabled ?? 1) == 1 && Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('vendor'))
                                            <li class="for_order">
                                                <p>
                                                    <a href="{{route('vendor.dashboard')}}">
                                                        <i class="fa-solid fa-store"></i>
                                                   Vendor Panel
                                                    </a>
                                                </p>
                                            </li>
                                            @elseif(($generalsetting?->reseller_enabled ?? 1) == 1 && Auth::guard('admin')->check() && (Auth::guard('admin')->user()->hasRole('reseller') || (isset(Auth::guard('admin')->user()->role) && strtolower(Auth::guard('admin')->user()->role) === 'reseller')))
                                            <li class="for_order">
                                                <p>
                                                    <a href="{{route('reseller.dashboard')}}">
                                                        <i class="fa-solid fa-handshake"></i>
                                                Dashboard
                                                    </a>
                                                </p>
                                            </li>
                                            @else
                                            <li class="for_order">
                                                <p>
                                                    <a href="{{route('customer.login')}}">
                                                        <i class="fa-regular fa-user"></i>
                                                        Login / Sign Up
                                                    </a>
                                                </p>
                                            </li>
                                            @endif
                                       </div>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- main-header end -->
        </header>
        <div id="content">
            @yield('content')
        </div>
            <!-- content end -->

<footer class="footer-v2">
    <div class="footer-v2__wave"></div>

    {{-- 🛡️ Store Trust Features Strip --}}
    <div class="footer-trust-strip">
        <div class="footer-trust-grid">
            <div class="footer-trust-card">
                <div class="footer-trust-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <div class="footer-trust-info">
                    <h6>সারা দেশে হোম ডেলিভারি</h6>
                    <p>ক্যাশ অন ডেলিভারি সুবিধা</p>
                </div>
            </div>
            <div class="footer-trust-card">
                <div class="footer-trust-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="footer-trust-info">
                    <h6>১০০% অরিজিনাল পণ্য</h6>
                    <p>গুণগত মান ও আস্থার প্রতীক</p>
                </div>
            </div>
            <div class="footer-trust-card">
                <div class="footer-trust-icon"><i class="fa-solid fa-rotate-left"></i></div>
                <div class="footer-trust-info">
                    <h6>সহজ রিটার্ন পলিসি</h6>
                    <p>ঝামেলামুক্ত পণ্য পরিবর্তনের সুবিধা</p>
                </div>
            </div>
            <div class="footer-trust-card">
                <div class="footer-trust-icon"><i class="fa-solid fa-headset"></i></div>
                <div class="footer-trust-info">
                    <h6>২৪/৭ কাস্টমার সাপোর্ট</h6>
                    <p>যে কোনো সহায়তায় পাশে আছি</p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-v2__main">
        <div class="footer-v2__grid">
            <!-- Brand & Contact -->
            <div class="footer-v2__brand">
                <a href="{{ url('/') }}" class="footer-v2__logo">
                    <img src="{{ asset(optional($generalsetting)->white_logo ?? 'public/logo.png') }}" alt="{{ optional($generalsetting)->name ?? 'Logo' }}">
                </a>
                <p class="footer-v2__tagline">
                    {{ optional($generalsetting)->footer_about_text ?? 'আপনার বিশ্বস্ত অনলাইন শপিং গন্তব্য। আমরা সেরা মানের পণ্য ও দ্রুততম ডেলিভারি সেবা প্রদানে প্রতিশ্রুতিবদ্ধ।' }}
                </p>

                <div class="footer-contact-list">
                    @if(!empty($contact->phone))
                    <a href="tel:{{ $contact->phone }}" class="footer-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>{{ $contact->phone }}</span>
                    </a>
                    @endif
                    @if(!empty($contact->email))
                    <a href="mailto:{{ $contact->email }}" class="footer-contact-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ $contact->email }}</span>
                    </a>
                    @endif
                    @if(!empty($contact->address))
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>{{ $contact->address }}</span>
                    </div>
                    @endif
                </div>

                @if(!empty($generalsetting->google_play_link) || !empty($generalsetting->app_store_link))
                <div class="footer-v2__apps">
                    <div class="footer-v2__apps-title">মোবাইল অ্যাপ ডাউনলোড করুন</div>
                    <div class="footer-v2__app-badges">
                        @if(!empty($generalsetting->google_play_link))
                        <a href="{{ $generalsetting->google_play_link }}" target="_blank" rel="noopener">
                            <img src="/public/uploads/play.svg" alt="Google Play">
                        </a>
                        @endif
                        @if(!empty($generalsetting->app_store_link))
                        <a href="{{ $generalsetting->app_store_link }}" target="_blank" rel="noopener">
                            <img src="/public/uploads/app.png" alt="App Store">
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Useful Links -->
            <div class="footer-v2__block">
                <h5 class="footer-v2__title">প্রয়োজনীয় লিংক</h5>
                <ul class="footer-v2__links">
                    <li><a href="{{ route('customer.order_track') }}">অর্ডার ট্র্যাকিং</a></li>
                    <li><a href="{{ route('complaint') }}">অভিযোগ / সমস্যা জানান</a></li>
                    @foreach($pages as $page)
                    <li><a href="{{ route('page', ['slug' => $page->slug]) }}">{{ $page->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Policy & Information -->
            <div class="footer-v2__block">
                <h5 class="footer-v2__title">তথ্য ও পলিসি</h5>
                <ul class="footer-v2__links">
                    @foreach($pagesright as $key => $value)
                    <li><a href="{{ route('page', ['slug' => $value->slug]) }}">{{ $value->name }}</a></li>
                    @endforeach
                    @if(Route::has('contact'))
                    <li><a href="{{ route('contact') }}">যোগাযোগ করুন</a></li>
                    @endif
                </ul>
            </div>

            <!-- Newsletter + Social -->
            <div class="footer-v2__newsletter">
                <h5 class="footer-v2__title">নিউজলেটার</h5>
                <p class="footer-v2__newsletter-desc">এক্সক্লুসিভ অফার ও ডিসকাউন্টের আপডেট পেতে সাবস্ক্রাইব করুন।</p>
                <form action="{{ route('frontend.newsletter.subscribe') }}" method="POST" class="footer-v2__form">
                    @csrf
                    <div class="footer-newsletter-wrap">
                        <input type="email" name="email" placeholder="আপনার ইমেইল লিখুন..." required autocomplete="email">
                        <button type="submit"><i class="fa-solid fa-paper-plane"></i> সাবস্ক্রাইব</button>
                    </div>
                </form>
                <div class="footer-v2__social-title">আমাদের সোশ্যাল মিডিয়া</div>
                <ul class="footer-v2__social-list">
                    @foreach($socialicons as $value)
                    <li>
                        <a href="{{ $value->link }}" target="_blank" rel="noopener" aria-label="Social"><i class="{{ $value->icon }}"></i></a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-v2__bottom">
        <div class="footer-v2__copy-wrap">
            <span class="footer-v2__copy-text">&copy; {{ date('Y') }} <strong>{{ optional($generalsetting)->name ?? config('app.name') }}</strong>. সর্বস্বত্ব সংরক্ষিত</span>
            <span class="footer-v2__designer">
                Designed by
                <a href="#" class="footer-v2__designer-link">
                    Tech Vai
                </a>
            </span>
        </div>
    </div>
</footer>

        {{-- Floating Cart Widget - ক্লিক করলে সাইডবার কার্ট ওপেন হবে --}}
        <a href="javascript:void(0)" class="floating-cart-widget" id="floatingCartBtn" title="শপিং কার্ট দেখুন" aria-label="কার্ট খুলুন">
            <div class="floating-cart-pill">
                <div class="floating-cart-icon-wrap">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="floating-cart-badge mobilecart-qty">{{ Cart::instance('shopping')->count() }}</span>
                </div>
                <div class="floating-cart-details">
                    <span class="floating-cart-label">কার্ট</span>
                    <span class="floating-cart-total" id="floatingCartTotal">৳ {{ number_format(floatval(preg_replace('/[^\d.]/', '', Cart::instance('shopping')->subtotal())), 0) }}</span>
                </div>
            </div>
        </a>

        {{-- Sidebar Cart Drawer - ডান দিক থেকে স্লাইড আউট --}}
        <div id="sidebarCartOverlay" class="sidebar-cart-overlay" onclick="closeSidebarCart()"></div>
        <div id="sidebarCartDrawer" class="sidebar-cart-drawer">
            <div id="sidebarCartContent">
                {{-- AJAX দিয়ে লোড হবে --}}
            </div>
        </div>

<div class="mobile_bottom_nav">
    <div class="nav_container">
        <a href="javascript:void(0)" class="nav_item toggle">
            <div class="icon_box">
                <i class="fa-solid fa-bars"></i>
            </div>
            <span class="nav_text">Category</span>
        </a>

        <a href="{{route('customer.order_track')}}" class="nav_item {{ Route::is('customer.order_track') ? 'active' : '' }}">
            <div class="icon_box">
                <i class="fa fa-truck"></i>
            </div>
            <span class="nav_text">Tracking</span>
        </a>

        <div class="nav_item home_wrapper">
            <a href="{{route('home')}}" class="home_fab {{ Route::is('home') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
            </a>
        </div>

        <a href="javascript:void(0)" onclick="openSidebarCart()" class="nav_item {{ Route::is('customer.checkout') ? 'active' : '' }}">
            <div class="icon_box">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart_badge mobilecart-qty">{{Cart::instance('shopping')->count()}}</span>
            </div>
            <span class="nav_text">Cart</span>
        </a>

        @if(Auth::guard('customer')->user())
            <a href="{{route('customer.account')}}" class="nav_item {{ Route::is('customer.account') ? 'active' : '' }}">
                <div class="icon_box">
                    <i class="fa-solid fa-user"></i>
                </div>
                <span class="nav_text">Account</span>
            </a>
        @elseif(($generalsetting?->vendor_enabled ?? 1) == 1 && Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('vendor'))
            <a href="{{route('vendor.dashboard')}}" class="nav_item">
                <div class="icon_box">
                    <i class="fa-solid fa-store"></i>
                </div>
                <span class="nav_text">Vendor</span>
            </a>
        @elseif(($generalsetting?->reseller_enabled ?? 1) == 1 && Auth::guard('admin')->check() && (Auth::guard('admin')->user()->hasRole('reseller') || (isset(Auth::guard('admin')->user()->role) && strtolower(Auth::guard('admin')->user()->role) === 'reseller')))
            <a href="{{route('reseller.dashboard')}}" class="nav_item">
                <div class="icon_box">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <span class="nav_text">Reseller</span>
            </a>
        @else
            <a href="{{route('customer.login')}}" class="nav_item {{ Route::is('customer.login') ? 'active' : '' }}">
                <div class="icon_box">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </div>
                <span class="nav_text">Login</span>
            </a>
        @endif
    </div>
</div>
<style>
/* --- Mobile Bottom Navigation Styles --- */
.mobile_bottom_nav {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #ffffff;
    box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    padding: 10px 0;
    border-radius: 20px 20px 0 0; /* উপরের কোনা গুলো একটু গোল হবে */
    display: none; /* ডেস্কটপে হাইড থাকবে */
}

/* শুধুমাত্র মোবাইলে দেখানোর জন্য */
@media (max-width: 768px) {
    .mobile_bottom_nav {
        display: block;
    }
}

.nav_container {
    display: flex;
    justify-content: space-around;
    align-items: flex-end; /* আইটেমগুলো নিচে সমান থাকবে */
    position: relative;
    padding: 0 10px;
}

/* সাধারণ মেনু আইটেম */
.nav_item {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #6c757d; /* ডিফল্ট কালার */
    font-size: 12px;
    transition: all 0.3s ease;
    width: 20%;
}

.icon_box {
    position: relative;
    font-size: 20px;
    margin-bottom: 4px;
    transition: transform 0.2s;
}

.nav_text {
    font-weight: 500;
}

/* হোভার এবং একটিভ কালার */
.nav_item:hover, .nav_item.active {
    color: #FF6600; /* আপনার ব্র্যান্ড কালার এখানে দিন */
}

.nav_item.active .icon_box {
    transform: translateY(-3px); /* একটিভ হলে একটু উপরে উঠবে */
}

/* --- Center Floating Home Button --- */
.home_wrapper {
    position: relative;
    bottom: 25px; /* স্বাভাবিকের চেয়ে উপরে থাকবে */
}

.home_fab {
    width: 60px;
    height: 60px;
    background: {{$generalsetting->primary_color}}; /* ব্র্যান্ড কালার */
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    font-size: 24px;
    box-shadow: 0 8px 15px rgba(255, 102, 0, 0.4);
    border: 4px solid #fff; /* সাদা বর্ডার */
    transition: transform 0.3s ease;
}

.home_fab:hover {
    transform: scale(1.1); /* হোভারে বড় হবে */
    color: #fff;
}

/* --- Cart Badge Style --- */
.cart_badge {
    position: absolute;
    top: -8px;
    right: -10px;
    background: #ff0000;
    color: #fff;
    font-size: 10px;
    font-weight: bold;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 2px solid #fff;
}
</style>

@include('frontEnd.layouts.partials.gemini_customer_chat')

{{-- ═══════════════════════════════════════════════════════════════
   🛍️ MODERN FLOATING CART WIDGET & SIDEBAR MINI-CART DRAWER
   ═══════════════════════════════════════════════════════════════ --}}
<style>
/* Floating Cart Widget */
.floating-cart-widget {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    z-index: 9998;
    text-decoration: none !important;
    user-select: none;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.floating-cart-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
    color: #ffffff;
    padding: 12px 10px 10px;
    border-radius: 16px 0 0 16px;
    box-shadow: -4px 6px 20px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.3);
    min-width: 60px;
    cursor: pointer;
    border-left: 1px solid rgba(255, 255, 255, 0.25);
    border-top: 1px solid rgba(255, 255, 255, 0.25);
    border-bottom: 1px solid rgba(255, 255, 255, 0.25);
    transition: all 0.25s ease;
}
.floating-cart-widget:hover {
    transform: translateY(-50%) translateX(-4px);
}
.floating-cart-widget:hover .floating-cart-pill {
    box-shadow: -6px 8px 25px rgba(0, 0, 0, 0.28);
    background: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
    filter: brightness(0.92);
}
.floating-cart-icon-wrap {
    position: relative;
    font-size: 22px;
    line-height: 1;
    margin-bottom: 6px;
}
.floating-cart-icon-wrap i {
    color: #ffffff;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}
.floating-cart-badge {
    position: absolute;
    top: -8px;
    right: -12px;
    background: #ff3366;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    padding: 0 4px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    animation: cartBadgePulse 2s infinite ease-in-out;
}
@keyframes cartBadgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.12); }
}
.floating-cart-details {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1px;
}
.floating-cart-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.3px;
    opacity: 0.95;
    text-transform: uppercase;
}
.floating-cart-total {
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
    background: rgba(255, 255, 255, 0.2);
    padding: 1px 5px;
    border-radius: 6px;
    margin-top: 2px;
}

@media (max-width: 768px) {
    .floating-cart-widget {
        display: none !important;
    }
}

/* Sidebar Mini Cart Overlay & Drawer */
.sidebar-cart-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 100010;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s;
}
.sidebar-cart-overlay.active {
    opacity: 1;
    visibility: visible;
}
.sidebar-cart-drawer {
    position: fixed;
    top: 0;
    right: 0;
    width: 420px;
    max-width: 95vw;
    height: 100%;
    height: 100dvh;
    background: #f8fafc;
    z-index: 100011;
    transform: translateX(100%);
    visibility: hidden;
    pointer-events: none;
    transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.38s;
    box-shadow: -10px 0 40px rgba(0, 0, 0, 0.22);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    font-family: inherit;
}
.sidebar-cart-drawer.active {
    transform: translateX(0);
    visibility: visible;
    pointer-events: auto;
}
#sidebarCartContent {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Header */
.sidebar-cart-header {
    background: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
    color: #ffffff;
    flex-shrink: 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}
.sidebar-cart-header-main {
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.sidebar-cart-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sidebar-cart-header-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: #ffffff;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3);
}
.sidebar-cart-header-text h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}
.sidebar-cart-close {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: #ffffff;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}
.sidebar-cart-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

/* Body */
.sidebar-cart-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 10px 12px;
    overscroll-behavior: contain;
}
.sidebar-cart-items-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.sidebar-cart-item-card {
    display: flex;
    gap: 10px;
    padding: 8px 10px;
    background: #ffffff;
    border-radius: 10px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    transition: all 0.2s ease;
    position: relative;
}
.sidebar-cart-item-card:hover {
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
    border-color: #d6dce5;
}
.sidebar-cart-item-img {
    width: 56px;
    height: 62px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    background: #f1f3f5;
    border: 1px solid #eaedf0;
}
.sidebar-cart-item-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.sidebar-cart-item-card:hover .sidebar-cart-item-img img {
    transform: scale(1.05);
}
.sidebar-cart-item-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.sidebar-cart-item-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 6px;
}
.sidebar-cart-item-name {
    font-size: 12.5px;
    font-weight: 600;
    color: #1e293b;
    text-decoration: none;
    line-height: 1.25;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s;
}
.sidebar-cart-item-name:hover {
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
}
.sidebar-cart-item-del {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 2px 4px;
    font-size: 13px;
    border-radius: 4px;
    transition: all 0.2s ease;
    flex-shrink: 0;
    line-height: 1;
}
.sidebar-cart-item-del:hover {
    color: #ef4444;
    background: #fee2e2;
}
.sidebar-cart-item-variants {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin: 2px 0;
}
.sidebar-cart-variant-tag {
    font-size: 10px;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 500;
}
.sidebar-cart-item-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 4px;
    padding-top: 4px;
    border-top: 1px dashed #f1f5f9;
}
.sidebar-cart-item-pricing {
    display: flex;
    align-items: baseline;
    gap: 5px;
}
.sidebar-cart-current-price {
    font-size: 13.5px;
    font-weight: 700;
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
}
.sidebar-cart-old-price {
    font-size: 11px;
    color: #94a3b8;
    text-decoration: line-through;
}

/* Quantity selector */
.sidebar-cart-qty-pill {
    display: inline-flex;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 1px;
    gap: 1px;
}
.sidebar-qty-action {
    width: 22px;
    height: 22px;
    border: none;
    background: #ffffff;
    color: #1e293b;
    font-size: 10px;
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    transition: all 0.15s ease;
}
.sidebar-qty-action:hover {
    background: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
    color: #ffffff;
}
.sidebar-qty-val {
    min-width: 22px;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
}

/* Empty State */
.sidebar-cart-empty-state {
    text-align: center;
    padding: 40px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.sidebar-cart-empty-icon-box {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
}
.sidebar-cart-empty-icon-inner {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
    box-shadow: 0 3px 10px rgba(0,0,0,0.06);
}
.sidebar-cart-empty-state h4 {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 4px;
}
.sidebar-cart-empty-state p {
    font-size: 12px;
    color: #64748b;
    margin: 0 0 16px;
    max-width: 240px;
    line-height: 1.4;
}
.sidebar-cart-shop-now-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 24px;
    background: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
    color: #ffffff !important;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.25s ease;
}
.sidebar-cart-shop-now-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.22);
    filter: brightness(0.92);
}

/* Footer (Compact & Tight) */
.sidebar-cart-footer {
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 12px 14px;
    flex-shrink: 0;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.04);
}
.sidebar-cart-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.sidebar-cart-subtotal-text {
    font-size: 13.5px;
    font-weight: 600;
    color: #475569;
}
.sidebar-cart-subtotal-price {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}
.sidebar-cart-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.sidebar-cart-btn-checkout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 11px 16px;
    background: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
    color: #ffffff !important;
    text-decoration: none;
    font-size: 14.5px;
    font-weight: 700;
    border-radius: 10px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    transition: all 0.25s ease;
    letter-spacing: 0.2px;
}
.sidebar-cart-btn-checkout:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.22);
    filter: brightness(0.92);
}
.sidebar-cart-btn-continue {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    text-align: center;
    padding: 4px;
    transition: color 0.2s;
    text-decoration: none;
}
.sidebar-cart-btn-continue:hover {
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
}

/* Fly to cart animation */
.fly-to-cart-img {
    position: fixed;
    z-index: 99999;
    pointer-events: none;
    border-radius: 8px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.35);
    object-fit: cover;
    border: 2px solid #fff;
}
@keyframes cartBump {
    0% { transform: scale(1); }
    40% { transform: scale(1.25); }
    70% { transform: scale(0.95); }
    100% { transform: scale(1); }
}
.cart-bump-animate {
    animation: cartBump 0.45s ease-out;
}
.floating-cart-widget, .menu-bag a, .mobile_bottom_nav .nav_item .icon_box {
    transform-origin: center center;
}
</style>

<script>
/* Sidebar Cart - খোলা/বন্ধ ও রিফ্রেশ */
function openSidebarCart() {
    document.getElementById("sidebarCartOverlay").classList.add("active");
    document.getElementById("sidebarCartDrawer").classList.add("active");
    document.body.style.overflow = "hidden";
    sidebarCartRefresh();
}
function closeSidebarCart() {
    document.getElementById("sidebarCartOverlay").classList.remove("active");
    document.getElementById("sidebarCartDrawer").classList.remove("active");
    document.body.style.overflow = "";
}
function sidebarCartRefresh() {
    $.get("{{ route('cart.sidebar') }}", function(html) {
        $("#sidebarCartContent").html(html);
        var newSubtotal = $("#sidebarCartContent").find(".sidebar-cart-subtotal-price").text();
        if (newSubtotal) {
            $("#floatingCartTotal").text(newSubtotal);
        } else {
            $("#floatingCartTotal").text("৳ 0");
        }
        if (typeof feather !== "undefined") feather.replace();
    });
}
document.getElementById("floatingCartBtn")?.addEventListener("click", function(e) {
    e.preventDefault();
    openSidebarCart();
});
document.getElementById("sidebarCartOverlay")?.addEventListener("click", closeSidebarCart);
</script>


        <!-- /. fixed sidebar -->

        <div id="custom-modal"></div>
        <div id="page-overlay"></div>
        <div id="loading" style="display: none;"><div class="custom-loader"></div></div>
        <script>document.addEventListener('DOMContentLoaded',function(){var e=document.getElementById('loading');if(e)e.style.display='none';});</script>

        <script src="{{asset('public/frontEnd/js/jquery-3.6.3.min.js')}}"></script>
        <script>
            $(function() {
                $("#loading").hide();
                $(window).on("load", function() { $("#loading").hide(); });
                setTimeout(function() { $("#loading").hide(); }, 3000);
            });
        </script>
        <script src="{{asset('public/frontEnd/js/bootstrap.min.js')}}" defer></script>
        <script src="{{asset('public/frontEnd/js/owl.carousel.min.js')}}" defer></script>
        <script src="{{asset('public/frontEnd/js/mobile-menu.js')}}" defer></script>
        <script src="{{asset('public/frontEnd/js/wsit-menu.js')}}" defer></script>
        <script src="{{asset('public/frontEnd/js/mobile-menu-init.js')}}" defer></script>
        <script src="{{asset('public/frontEnd/js/wow.min.js')}}" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof WOW !== 'undefined') { new WOW().init(); }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>

        <!-- feather icon -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof feather !== 'undefined') { feather.replace(); }
            });
        </script>
        <script src="{{asset('public/backEnd/')}}/assets/js/toastr.min.js" defer></script>
        {!! Toastr::message() !!} @stack('script')
		
		
		<script>
    $(document).ready(function() {
        $(".main_slider").owlCarousel({
            items: 1,
            loop: true,
            dots: false,
            autoplay: true,
            nav: true,
            autoplayHoverPause: true,
            margin: 0,
            mouseDrag: true,
            smartSpeed: 600,
            autoplayTimeout: 5000,
            navText: ["<i class='fa-solid fa-angle-left'></i>",
                "<i class='fa-solid fa-angle-right'></i>"
            ],
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".hotdeals-slider").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: true,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: true,
                    loop: false,
                },
            },
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".category-slider").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: true,
                },
                600: {
                    items: 5,
                    nav: false,
                },
                1000: {
                    items: 8,
                    nav: true,
                    loop: false,
                },
            },
        });

        $(".product_slider").owlCarousel({
            margin: 15,
            items: 6,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                600: {
                    items: 5,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: false,
                },
            },
        });
		$(".customer-review").owlCarousel({
            margin: 8,
            items: 6,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: false,
                },
            },
        });
    });
</script>
		
        <script>
            $(".quick_view").on("click", function () {
                var id = $(this).data("id");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('quickview')}}",
                        success: function (data) {
                            if (data) {
                                $("#custom-modal").html(data);
                                $("#custom-modal").show();
                                $("#loading").hide();
                                $("#page-overlay").show();
                            }
                        },
                    });
                }
            });
        </script>
        <!-- quick view end -->
        <!-- cart js start -->
        <script>
            function runFlyToCart($sourceEl, onComplete) {
                var $flyImg;
                if ($sourceEl && $sourceEl.closest && $sourceEl.closest('.variant-modal-content').length)
                    $flyImg = $sourceEl.closest('.variant-modal-content').find('.variant-modal-img img').first();
                if (!$flyImg || !$flyImg.length)
                    $flyImg = $sourceEl.closest('.product_item, .wist_item, .search-item, .quick-product, .product-section, .main-details-page').find('.pro_img img, .quick-product-img img, .details_slider img, .block__pic, .dimage_item img').first();
                if (!$flyImg || !$flyImg.length) $flyImg = $('.details_slider img, .block__pic, #details_slider_main img').first();
                if (!$flyImg || !$flyImg.length) { if (typeof onComplete === 'function') onComplete(); return; }
                var rect = $flyImg[0].getBoundingClientRect();
                var $clone = $flyImg.clone().addClass('fly-to-cart-img').css({
                    position: 'fixed', width: 90, height: 110,
                    left: rect.left, top: rect.top, margin: 0, padding: 0, zIndex: 99999
                }).appendTo('body');
                var $target = $('#floatingCartBtn, .floating-cart-widget').first();
                if (!$target.length || !$target.is(':visible')) $target = $('.mobile_bottom_nav .cart_badge').closest('a').first();
                if (!$target.length) $target = $('.menu-bag a').first();
                var destRect = $target.length && $target.is(':visible') ? $target[0].getBoundingClientRect() : { left: $(window).width() - 60, top: $(window).height() / 2 - 40 };
                var endW = 36, endH = 44;
                var endLeft = destRect.left + ($target.length ? (destRect.width || 0) / 2 - endW / 2 : 0);
                var endTop = destRect.top + ($target.length ? (destRect.height || 0) / 2 - endH / 2 : 0);
                var midLeft = (rect.left + endLeft) / 2 - 20;
                var midTop = Math.min(rect.top, endTop) - 100;
                $clone.animate({ left: midLeft, top: midTop, width: 70, height: 85, opacity: 1 }, 300, 'swing', function() {
                    $(this).animate({ left: endLeft, top: endTop, width: endW, height: endH, opacity: 0.6 }, 350, 'swing', function() {
                        $clone.remove();
                        if ($target && $target.length) { $target.addClass('cart-bump-animate'); setTimeout(function() { $target.removeClass('cart-bump-animate'); }, 450); }
                        if (typeof onComplete === 'function') onComplete();
                    });
                });
            }
            $(document).on("click", ".addcartbutton", function (e) {
                var $btn = $(this);
                var id = $btn.data("id");
                var checkout = $btn.data("checkout");
                var qty = 1;
                if (id) {
                    e.preventDefault();
                    $.ajax({
                        cache: "false",
                        type: "GET",
                        url: "{{url('add-to-cart')}}/" + id + "/" + qty,
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                toastr.success('Success', 'Product add to cart successfully');
                                cart_count();
                                mobile_cart();
                                if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                                runFlyToCart($btn, function() { if (typeof openSidebarCart === "function") openSidebarCart(); });
                            }
                        },
                    });
                }
                if(checkout){
                    window.location.href = '{{route('customer.checkout')}}'; 
                }
            });
            $(document).on("click", ".cart_store", function (e) {
                var $btn = $(this);
                var $form = $btn.closest('form');
                if (!$form.length) return;
                var id = $btn.data("id") || $form.find("input[name=id]").val();
                if (!id) return;
                e.preventDefault();
                $form.addClass('cart-ajax-submit');
                $.ajax({
                    type: "POST",
                    data: $form.serialize(),
                    url: $form.attr('action'),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    dataType: "json",
                    success: function (data) {
                        if (data && data.success) {
                            if (typeof window.EcomTracking !== 'undefined') {
                                var currentItem = (typeof window.getCurrentDetailsItem === 'function') ? window.getCurrentDetailsItem() : null;
                                var trackPrice = currentItem ? currentItem.price : Number(data.price || 0);
                                var trackQty = currentItem ? currentItem.quantity : Number(data.qty || 1);
                                var trackName = currentItem ? currentItem.item_name : (data.product_name || '');
                                var trackCat = currentItem ? currentItem.item_category : (data.category || '');
                                var trackBrand = currentItem ? currentItem.item_brand : '';

                                window.EcomTracking.addToCart({
                                    items: [{
                                        id: String(data.product_id || id),
                                        name: trackName,
                                        price: trackPrice,
                                        qty: trackQty,
                                        category: trackCat,
                                        brand: trackBrand
                                    }],
                                    value: trackPrice * trackQty
                                });
                            }
                            toastr.success('Success', 'Product add to cart successfully');
                            cart_count();
                            mobile_cart();
                            if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                            var isOrderNow = $btn.is('[name="order_now"]') || $btn.hasClass('order_now_btn');
                            if (isOrderNow) {
                                window.location.href = '{{ route('customer.checkout') }}';
                                return;
                            }
                            runFlyToCart($btn, function() { if (typeof openSidebarCart === "function") openSidebarCart(); });
                        } else {
                            toastr.error(data && data.message ? data.message : 'Failed');
                        }
                    },
                    error: function(xhr) {
                        try {
                            var d = xhr.responseJSON;
                            if (d && !d.success) {
                                toastr.error(d.message || 'Failed');
                                return;
                            }
                        } catch(e) {}
                        $form.submit();
                    },
                    complete: function() { $form.removeClass('cart-ajax-submit'); }
                });
            });

            $(document).on("click", ".cart_remove", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.remove')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                cart_count();
                                mobile_cart();
                                cart_summary();
                                if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                            }
                        },
                    });
                }
            });

            $(document).on("click", ".cart_increment", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.increment')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                cart_count();
                                mobile_cart();
                                if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                            }
                        },
                    });
                }
            });

            $(document).on("click", ".cart_decrement", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.decrement')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                cart_count();
                                mobile_cart();
                                if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                            }
                        },
                    });
                }
            });

            function cart_count() {
                $.ajax({
                    type: "GET",
                    url: "{{route('cart.count')}}",
                    success: function (data) {
                        if (data) {
                            $("#cart-qty").html(data);
                        } else {
                            $("#cart-qty").empty();
                        }
                    },
                });
            }
            function mobile_cart() {
                $.ajax({
                    type: "GET",
                    url: "{{route('mobile.cart.count')}}",
                    success: function (data) {
                        if (data) {
                            $(".mobilecart-qty").html(data);
                        } else {
                            $(".mobilecart-qty").empty();
                        }
                    },
                });
            }
            function cart_summary() {
                $.ajax({
                    type: "GET",
                    url: "{{route('shipping.charge')}}",
                    dataType: "html",
                    success: function (response) {
                        $(".cart-summary").html(response);
                    },
                });
            }
        </script>
        <!-- cart js end -->
        <script>
            $(".search_click").on("keyup change", function () {
                var keyword = $(".search_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "{{route('livesearch')}}",
                    success: function (products) {
                        if (products) {
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
            $(".msearch_click").on("keyup change", function () {
                var keyword = $(".msearch_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "{{route('livesearch')}}",
                    success: function (products) {
                        if (products) {
                            $("#loading").hide();
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
        </script>
        <!-- search js start -->
        <script></script>
        <script></script>
        <script>
            $(".district").on("change", function () {
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    data: { id: id },
                    url: "{{route('districts')}}",
                    success: function (res) {
                        if (res) {
                            $(".area").empty();
                            $(".area").append('<option value="">Select..</option>');
                            $.each(res, function (key, value) {
                                $(".area").append('<option value="' + key + '" >' + value + "</option>");
                            });
                        } else {
                            $(".area").empty();
                        }
                    },
                });
            });
        </script>
        <script>
            $(".toggle").on("click", function () {
                $("#page-overlay").show();
                $(".mobile-menu").addClass("active");
            });

            $("#page-overlay").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
                $(".feature-products").removeClass("active");
            });

            $(".mobile-menu-close").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
            });

            $(".mobile-filter-toggle").on("click", function () {
                $("#page-overlay").show();
                $(".feature-products").addClass("active");
            });
        </script>
        <script>
            $(document).ready(function () {
                $(document).on("click", ".mobile-menu .menu-category-toggle", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var $toggle = $(this);
                    var $parent = $toggle.closest(".parent-category");
                    var $secondNav = $parent.find("> .second-nav");
                    $toggle.toggleClass("active");
                    $secondNav.stop(true, true).slideToggle(220);
                    $parent.toggleClass("active");
                });

                $(document).on("click", ".mobile-menu .menu-subcategory-toggle", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var $toggle = $(this);
                    var $parent = $toggle.closest(".parent-subcategory");
                    var $thirdNav = $parent.find("> .third-nav");
                    $toggle.toggleClass("active");
                    $thirdNav.stop(true, true).slideToggle(180);
                    $parent.toggleClass("active");
                });
            });
        </script>

        {{-- ✍️ Dynamic Animated Typewriter Placeholder Effect --}}
        <script>
            (function () {
                const searchWords = [
                    "পছন্দের পণ্য খুঁজুন...",
                    "স্মার্টফোন ও গ্যাজেট...",
                    "টি-শার্ট ও ফ্যাশন আইটেম...",
                    "ঘড়ি, চশমা ও জুয়েলারি...",
                    "হোম ও কিচেন এক্সেসরিজ...",
                    "সেরা অফার ও ডিসকাউন্ট..."
                ];

                let wordIdx = 0;
                let charIdx = 0;
                let isDeleting = false;
                let isPaused = false;
                const typingSpeed = 90;
                const deletingSpeed = 45;
                const pauseDelay = 1800;

                function getSearchInputs() {
                    return document.querySelectorAll(".search_keyword, .msearch_keyword");
                }

                function bindInputEvents() {
                    const inputs = getSearchInputs();
                    inputs.forEach(function (input) {
                        input.addEventListener("focus", function () { isPaused = true; });
                        input.addEventListener("blur", function () {
                            if (!input.value.trim()) {
                                isPaused = false;
                            }
                        });
                        input.addEventListener("input", function () {
                            if (input.value.trim()) isPaused = true;
                        });
                    });
                }

                function typeEffect() {
                    const inputs = getSearchInputs();
                    if (!inputs.length) {
                        setTimeout(typeEffect, 1000);
                        return;
                    }

                    if (isPaused) {
                        setTimeout(typeEffect, 500);
                        return;
                    }

                    const currentWord = searchWords[wordIdx];
                    let displayText = "";

                    if (isDeleting) {
                        displayText = currentWord.substring(0, charIdx - 1);
                        charIdx--;
                    } else {
                        displayText = currentWord.substring(0, charIdx + 1);
                        charIdx++;
                    }

                    inputs.forEach(function (input) {
                        if (document.activeElement !== input || !input.value) {
                            input.setAttribute("placeholder", displayText || " ");
                        }
                    });

                    let speed = isDeleting ? deletingSpeed : typingSpeed;

                    if (!isDeleting && charIdx === currentWord.length) {
                        speed = pauseDelay;
                        isDeleting = true;
                    } else if (isDeleting && charIdx === 0) {
                        isDeleting = false;
                        wordIdx = (wordIdx + 1) % searchWords.length;
                        speed = 350;
                    }

                    setTimeout(typeEffect, speed);
                }

                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", function () {
                        bindInputEvents();
                        setTimeout(typeEffect, 600);
                    });
                } else {
                    bindInputEvents();
                    setTimeout(typeEffect, 600);
                }
            })();
        </script>

        <script>
            var menu = new MmenuLight(document.querySelector("#menu"), "all");

            var navigator = menu.navigation({
                selectedClass: "Selected",
                slidingSubmenus: true,
                // theme: 'dark',
                title: "ক্যাটাগরি",
            });

            var drawer = menu.offcanvas({
                // position: 'left'
            });

            //  Open the menu.
            document.querySelector('a[href="#menu"]').addEventListener("click", (evnt) => {
                evnt.preventDefault();
                drawer.open();
            });
        </script>

        <script>
            // document.addEventListener("DOMContentLoaded", function () {
            //     window.addEventListener("scroll", function () {
            //         if (window.scrollY > 200) {
            //             document.getElementById("navbar_top").classList.add("fixed-top");
            //         } else {
            //             document.getElementById("navbar_top").classList.remove("fixed-top");
            //             document.body.style.paddingTop = "0";
            //         }
            //     });
            // });
            /*=== Main Menu Fixed === */
            // document.addEventListener("DOMContentLoaded", function () {
            //     window.addEventListener("scroll", function () {
            //         if (window.scrollY > 0) {
            //             document.getElementById("m_navbar_top").classList.add("fixed-top");
            //             // add padding top to show content behind navbar
            //             navbar_height = document.querySelector(".navbar").offsetHeight;
            //             document.body.style.paddingTop = navbar_height + "px";
            //         } else {
            //             document.getElementById("m_navbar_top").classList.remove("fixed-top");
            //             // remove padding top from body
            //             document.body.style.paddingTop = "0";
            //         }
            //     });
            // });
            /*=== Main Menu Fixed === */

            $(window).scroll(function () {
                if ($(this).scrollTop() > 50) {
                    $(".scrolltop:hidden").stop(true, true).fadeIn();
                } else {
                    $(".scrolltop").stop(true, true).fadeOut();
                }
            });
            $(function () {
                $(".scroll").click(function () {
                    $("html,body").animate({ scrollTop: $(".gotop").offset().top }, "1000");
                    return false;
                });
            });
        </script>
        <script>
            $(".filter_btn").click(function(){
               $(".filter_sidebar").addClass('active');
               $("body").css("overflow-y", "hidden");
            })
            $(".filter_close").click(function(){
               $(".filter_sidebar").removeClass('active');
               $("body").css("overflow-y", "auto");
            })
        </script>
        
        
        @php
    $popup = App\Models\Popup::where('status', 1)->latest()->first();
@endphp

@if($popup)
@php
    $isSimpleImagePopup = empty(trim($popup->description ?? '')) && empty(trim($popup->btn_text ?? '')) && empty(trim($popup->offer_end_text ?? ''));
@endphp
<div class="modal fade" id="popShopModal" tabindex="-1" aria-hidden="true" style="z-index: 10000;">
    <div class="modal-dialog modal-dialog-centered popup-modal-compact {{ $isSimpleImagePopup ? 'popup-simple-fit' : '' }}">
        <div class="modal-content ps-content {{ $isSimpleImagePopup ? 'popup-simple-image' : '' }}">
            <button type="button" class="ps-close" data-bs-dismiss="modal" aria-label="বন্ধ">&times;</button>
            
            @if($isSimpleImagePopup)
                {{-- শুধু ইমেজ পপআপ (FABRILIFE/bKash স্টাইল) --}}
                <a href="{{ !empty(trim($popup->link ?? '')) ? $popup->link : 'javascript:void(0)' }}" {{ !empty(trim($popup->link ?? '')) ? 'target="_blank"' : '' }} class="popup-simple-link">
                    <img src="{{ url('public/'.$popup->image) }}" alt="{{ $popup->title }}" class="popup-simple-img">
                </a>
            @else
                {{-- পুরনো লেআউট (টেক্সট + ইমেজ) --}}
                <div class="ps-layout">
                    <div class="ps-text-section">
                        <h3 class="ps-brand">{{ $popup->title }}</h3>
                        <div class="ps-headline">
                            <p>{!! nl2br(e($popup->description)) !!}</p>
                        </div>
                        @if($popup->offer_end_text)
                        <p class="ps-deadline">{{ $popup->offer_end_text }}</p>
                        @endif
                        <a href="{{ $popup->link ?? '#' }}" class="ps-btn">
                            {{ $popup->btn_text ?? 'Shop the Sale' }}
                        </a>
                        <div class="ps-footer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                              <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                            </svg>
                            <span>POWERED BY <strong>{{ $generalsetting->name ?? 'CommerceGurus' }}</strong></span>
                        </div>
                    </div>
                    <div class="ps-image-section">
                        <img src="{{ url('public/'.$popup->image) }}" alt="Offer Image">
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    #popShopModal .modal-dialog.popup-modal-compact:not(.popup-simple-fit) {
        max-width: min(620px, 92vw) !important;
        margin-left: auto;
        margin-right: auto;
    }
    /* শুধু ইমেজ পপআপ: বক্স টানা না রেখে ছবির আসল লেআউটে ফিট — সাদা স্ট্রাইপ কমে */
    #popShopModal .modal-dialog.popup-modal-compact.popup-simple-fit {
        width: fit-content !important;
        max-width: min(560px, calc(100vw - 24px)) !important;
        margin-left: auto;
        margin-right: auto;
    }

    #popShopModal .modal-content.ps-content.popup-simple-image {
        background-color: transparent !important;
        box-shadow: none !important;
    }
    /* শুধু ইমেজ পপআপ: সাদা ফ্রেম + ছায়া (overflow hidden স্ট্রোক কাটে না) */
    #popShopModal .modal-content.popup-simple-image .popup-simple-link {
        display: block;
        padding: 5px;
        background: rgba(255, 255, 255, 0.96);
        border-radius: 16px;
        box-shadow:
            0 0 0 1px rgba(255, 255, 255, 0.55),
            0 12px 40px rgba(0, 0, 0, 0.24);
        overflow: visible;
        line-height: 0;
    }

    #popShopModal .modal-content.ps-content {
        border: none !important;
        outline: none !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.14);
    }
    #popShopModal .modal-content.ps-content:focus,
    #popShopModal .modal-content.ps-content:focus-visible {
        outline: none !important;
    }

    .ps-content {
        border: none !important;
        border-radius: 12px;
        overflow: hidden;
        background-color: #fff;
        max-width: 100%;
        margin: 0 auto;
    }

    .ps-layout {
        display: flex;
        flex-direction: row;
        min-height: 340px;
    }

    .ps-text-section {
        width: 50%;
        padding: 34px 28px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
        position: relative;
    }

    .ps-brand {
        color: #b93a3a;
        font-family: Georgia, 'Times New Roman', serif;
        font-weight: 700;
        font-size: 32px;
        margin-bottom: 12px;
        line-height: 1;
    }

    .ps-headline {
        color: #222;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.35;
        margin-bottom: 12px;
    }

    .ps-headline span, .ps-headline p {
        font-weight: 400;
        font-size: 14px;
        color: #555;
        margin-top: 10px;
    }

    .ps-deadline {
        color: #888;
        font-size: 13px;
        margin-bottom: 22px;
    }

    .ps-btn {
        background-color: #2c3e50;
        color: #fff !important;
        text-decoration: none;
        padding: 12px 22px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        border-radius: 2px;
        display: block;
        width: 100%;
        transition: 0.3s;
        border: none;
    }
    .ps-btn:hover {
        background-color: #000;
    }

    .ps-footer {
        margin-top: 28px;
        font-size: 9px;
        color: #aaa;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
    }
    .ps-footer strong { color: #333; }

    .ps-image-section {
        width: 50%;
        position: relative;
        background: #ececec;
        padding: 5px;
        box-sizing: border-box;
    }
    .ps-image-section img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 2px;
        border: 3px solid rgba(255, 255, 255, 0.96);
        box-sizing: border-box;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.08);
    }

    .popup-simple-image {
        padding: 0;
        width: fit-content;
        max-width: min(560px, calc(100vw - 24px));
        margin: 0 auto;
        border-radius: 16px !important;
        overflow: visible !important;
        border: none !important;
        background: transparent;
    }
    .popup-simple-link {
        display: block;
        line-height: 0;
        text-decoration: none;
        border: none;
        outline: none;
        border-radius: inherit;
    }
    .popup-simple-link:focus,
    .popup-simple-link:focus-visible {
        outline: none !important;
    }
    .popup-simple-link[href="javascript:void(0)"] {
        cursor: default;
    }
    .popup-simple-img {
        width: auto;
        max-width: min(560px, calc(100vw - 24px));
        height: auto;
        max-height: min(80vh, 600px);
        object-fit: contain;
        display: block;
        border-radius: 12px !important;
        vertical-align: top;
        border: none;
        outline: none;
    }

    .ps-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #fff;
        border: none !important;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 22px;
        line-height: 30px;
        color: #333;
        cursor: pointer;
        z-index: 1050;
        box-shadow: none;
        transition: 0.2s;
        outline: none !important;
    }
    .ps-close:hover {
        color: #b93a3a;
        transform: scale(1.06);
        background: rgba(255, 255, 255, 0.95);
    }
    .ps-close:focus-visible {
        outline: 2px solid rgba(185, 58, 58, 0.35) !important;
        outline-offset: 2px;
    }

    @media (max-width: 768px) {
        #popShopModal .modal-dialog.popup-modal-compact:not(.popup-simple-fit) {
            max-width: min(540px, 94vw) !important;
            margin: 10px auto;
        }
        #popShopModal .modal-dialog.popup-simple-fit {
            max-width: calc(100vw - 20px) !important;
        }
        .popup-simple-image {
            max-width: calc(100vw - 20px);
        }
        .popup-simple-img {
            max-width: calc(100vw - 20px);
            max-height: min(78vh, 520px);
        }
        .ps-layout {
            flex-direction: column-reverse;
            min-height: 0;
        }
        .ps-text-section { width: 100%; padding: 24px 20px; }
        .ps-image-section { width: 100%; height: 200px; }
        .ps-brand { font-size: 26px; }
        .popup-simple-img {
            max-height: min(72vh, 440px);
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ৩ ঘন্টা পর পর দেখাবে
        const hoursToWait = 3;  
        // সাইটে ঢোকার ২ সেকেন্ড পর দেখাবে
        const delayInSeconds = 2; 

        const timeLimit = hoursToWait * 60 * 60 * 1000;
        const lastShown = localStorage.getItem('popupLastShown');
        const now = new Date().getTime();

        if (!lastShown || (now - lastShown > timeLimit)) {
            setTimeout(function() {
                // Try opening with jQuery (Standard for Laravel themes)
                if (typeof jQuery != 'undefined') {
                    $('#popShopModal').modal('show');
                } 
                // Try opening with Bootstrap 5
                else if (typeof bootstrap != 'undefined') {
                    var myModal = new bootstrap.Modal(document.getElementById('popShopModal'));
                    myModal.show();
                }

                localStorage.setItem('popupLastShown', now);
            }, delayInSeconds * 1000);
        }
    });
</script>
@endif

        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

@if(session('show_order_limit_modal'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ডাইনামিক হোয়াটসঅ্যাপ নাম্বার (ডাটাবেস থেকে)
        var whatsappNumber = "{{ $contact->whatsapp ?? $contact->hotline ?? '8801700000000' }}"; 
        
        Swal.fire({
            title: '', 
            html: `
                <div class="custom-modal-content">
                    <div class="modal-header-custom">
                        <div class="header-left">
                            <i class="fas fa-exclamation-triangle header-icon"></i>
                            <span>Duplicate Order Detective Alert</span>
                        </div>
                        <i class="fas fa-times close-icon" onclick="Swal.close()"></i>
                    </div>

                    <div class="modal-body-custom">
                        <p>
                            <img src="https://img.icons8.com/emoji/48/000000/warning-emoji.png" style="width: 20px; vertical-align: text-bottom;"> 
                            <b>সতর্কতা!</b> আপনি ইতিমধ্যে এই পণ্যটির জন্য অর্ডার দিয়েছেন। নির্দিষ্ট সময়ের মধ্যে একই পণ্যের পুনরায় অর্ডার দেওয়া অনুমোদিত নয়। 
                            👉 আপনি যদি সত্যিই আবার অর্ডার করতে চান, তাহলে নিচে দেওয়া WhatsApp নম্বরে যোগাযোগ করুন:
                        </p>
                    </div>

                    <div class="modal-footer-custom">
                        <a href="https://wa.me/${whatsappNumber}?text=আমি একই পণ্য পুনরায় অর্ডার করতে চাই, অনুগ্রহ করে সাহায্য করুন।" target="_blank" class="btn-whatsapp-custom">
                            <i class="fab fa-whatsapp"></i> CONTACT ON WHATSAPP
                        </a>
                        <button onclick="Swal.close()" class="btn-close-custom">Close</button>
                    </div>
                </div>
            `,
            showConfirmButton: false, // ডিফল্ট বাটন বন্ধ রাখা হয়েছে
            background: 'transparent', // ডিফল্ট ব্যাকগ্রাউন্ড রিমুভ
            customClass: {
                popup: 'swal-no-padding'
            },
            allowOutsideClick: false
        });
    });
</script>

<style>
    /* পপ-আপ কন্টেইনার রিসেট */
    .swal-no-padding {
        padding: 0 !important;
        background: none !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    /* মেইন বক্স ডিজাইন */
    .custom-modal-content {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        font-family: 'Arial', sans-serif;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        max-width: 500px;
        margin: 0 auto;
    }

    /* লাল হেডার (ছবির মতো হুবহু) */
    .modal-header-custom {
        background-color: #b91c1c; /* গাঢ় লাল */
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        font-size: 18px;
        font-weight: bold;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-icon {
        color: #facc15; /* হলুদ আইকন */
        font-size: 20px;
    }

    .close-icon {
        cursor: pointer;
        opacity: 0.8;
        font-size: 20px;
        transition: 0.2s;
    }
    .close-icon:hover {
        opacity: 1;
    }

    /* বডি টেক্সট */
    .modal-body-custom {
        padding: 30px 25px;
        text-align: left;
        font-size: 15px;
        line-height: 1.6;
        color: #4b5563;
    }

    /* ফুটার এবং বাটন */
    .modal-footer-custom {
        padding: 0 25px 30px 25px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    /* হোয়াটসঅ্যাপ বাটন (সবুজ) */
    .btn-whatsapp-custom {
        background-color: #10b981;
        color: white !important;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: bold;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: background 0.3s;
        border: none;
    }
    .btn-whatsapp-custom:hover {
        background-color: #059669;
    }

    /* ক্লোজ বাটন (লাল) */
    .btn-close-custom {
        background-color: #dc2626;
        color: white;
        padding: 10px 30px;
        border-radius: 50px;
        font-weight: bold;
        font-size: 14px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: background 0.3s;
    }
    .btn-close-custom:hover {
        background-color: #b91c1c;
    }

    /* রেস্পন্সিভ ডিজাইন */
    @media (max-width: 450px) {
        .modal-footer-custom {
            flex-direction: column;
        }
        .btn-whatsapp-custom, .btn-close-custom {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endif

@php $snxAccent = optional($generalsetting)->primary_color ?? '#007bff'; @endphp
{{-- 🛍️ MODERN LIVE SALES NOTIFICATION POPUP --}}
<style>
#snx-popup {
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 99995;
    width: 330px;
    max-width: calc(100vw - 32px);
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 14px 38px -4px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    transform: translateY(30px) scale(0.95);
    opacity: 0;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease;
    pointer-events: none;
    border-top: 3px solid {{ $snxAccent }};
}
#snx-popup.snx-show {
    transform: translateY(0) scale(1);
    opacity: 1;
    pointer-events: auto;
}
.snx-img-wrap {
    position: relative;
    width: 58px;
    height: 58px;
    flex-shrink: 0;
}
#snx-popup .snx-img {
    width: 100%;
    height: 100%;
    border-radius: 12px;
    object-fit: cover;
    background: #f8fafc;
    border: 1px solid #eef2f6;
    display: block;
}
.snx-cart-badge {
    position: absolute;
    bottom: -4px;
    right: -4px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: {{ $snxAccent }};
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}
#snx-popup .snx-body {
    flex: 1;
    min-width: 0;
}
#snx-popup .snx-header {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 2px;
}
#snx-popup .snx-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
#snx-popup .snx-title span#snx-name {
    color: {{ $snxAccent }};
}
#snx-popup .snx-product {
    font-size: 12px;
    font-weight: 500;
    color: #475569;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.15s ease;
}
#snx-popup .snx-product:hover {
    color: {{ $snxAccent }};
    text-decoration: underline;
}
.snx-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-top: 5px;
}
#snx-popup .snx-time {
    font-size: 10.5px;
    font-weight: 500;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
}
#snx-popup .snx-dot {
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    animation: snx-pulse 1.8s infinite;
}
@keyframes snx-pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.35); opacity: 0.6; }
}
#snx-popup .snx-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 600;
    color: #059669;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 20px;
    padding: 1px 7px;
    white-space: nowrap;
}
#snx-popup .snx-close {
    position: absolute;
    top: 6px;
    right: 8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 11px;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    padding: 0;
}
#snx-popup .snx-close:hover {
    background: #fee2e2;
    color: #ef4444;
    border-color: #fecaca;
    transform: rotate(90deg);
}
@media (max-width: 768px) {
    #snx-popup {
        left: 12px;
        bottom: 74px;
        width: calc(100vw - 24px);
        max-width: 320px;
    }
}
</style>

<div id="snx-popup" role="alert" aria-live="polite">
    <button class="snx-close" id="snx-close-btn" aria-label="Close" title="বিজ্ঞপ্তিটি বন্ধ করুন">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="snx-img-wrap">
        <img id="snx-img" class="snx-img" src="" alt="Product" loading="lazy">
        <span class="snx-cart-badge"><i class="fa-solid fa-bag-shopping"></i></span>
    </div>
    <div class="snx-body">
        <div class="snx-header">
            <span class="snx-title"><span id="snx-name"></span> একটি অর্ডার করেছেন</span>
        </div>
        <div class="snx-product" id="snx-product" title="পণ্যটি দেখতে ক্লিক করুন"></div>
        <div class="snx-footer">
            <div class="snx-time">
                <span class="snx-dot"></span>
                <span id="snx-time">কিছুক্ষণ আগে</span>
            </div>
            <div class="snx-badge"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড অর্ডার</div>
        </div>
    </div>
</div>

<script>
(function(){
    var popup      = document.getElementById('snx-popup');
    if (!popup) return;

    var imgEl      = document.getElementById('snx-img');
    var nameEl     = document.getElementById('snx-name');
    var productEl  = document.getElementById('snx-product');
    var timeEl     = document.getElementById('snx-time');
    var closeBtn   = document.getElementById('snx-close-btn');

    var notifications = [];
    var currentIndex  = 0;
    var hideTimer, nextTimer, fetchTimer;
    var userClosed    = false;
    var SHOW_DURATION = 5000;
    var INT_MIN       = 8000;
    var INT_MAX       = 15000;

    closeBtn.addEventListener('click', function(){
        hidePopup();
        userClosed = true;
        clearTimeout(fetchTimer);
        fetchTimer = setTimeout(function(){ userClosed = false; showNext(); }, 120000);
    });

    function showPopup(item) {
        imgEl.src        = item.image || '{{ asset("public/frontEnd/images/default-product.png") }}';
        imgEl.onerror    = function(){ this.src = '{{ asset("public/frontEnd/images/default-product.png") }}'; };
        nameEl.textContent    = item.name;
        productEl.textContent = item.product_name;
        timeEl.textContent    = item.time;

        productEl.onclick = function(){
            if (item.product_url && item.product_url !== '#') {
                window.location.href = item.product_url;
            }
        };
        productEl.style.cursor = item.product_url && item.product_url !== '#' ? 'pointer' : 'default';

        popup.classList.add('snx-show');
        clearTimeout(hideTimer);
        hideTimer = setTimeout(hidePopup, SHOW_DURATION);
    }

    function hidePopup() {
        popup.classList.remove('snx-show');
    }

    function showNext() {
        if (userClosed || notifications.length === 0) return;
        var item = notifications[currentIndex % notifications.length];
        currentIndex++;
        showPopup(item);
        clearTimeout(nextTimer);
        var delay = INT_MIN + Math.floor(Math.random() * (INT_MAX - INT_MIN));
        nextTimer = setTimeout(showNext, delay);
    }

    function fetchAndStart() {
        fetch('{{ route("sales.notifications") }}')
            .then(function(r){ return r.json(); })
            .then(function(data){
                if (!data.enabled) return;
                var items = data.items || data;
                if (!Array.isArray(items) || items.length === 0) return;

                if (data.display_duration) SHOW_DURATION = data.display_duration;
                if (data.interval_min)     INT_MIN       = data.interval_min;
                if (data.interval_max)     INT_MAX       = data.interval_max;

                notifications = items.sort(function(){ return Math.random() - 0.5; });
                currentIndex  = 0;
                clearTimeout(nextTimer);
                nextTimer = setTimeout(showNext, 3000);
            })
            .catch(function(){});
    }

    function scheduleFetch() {
        if ('requestIdleCallback' in window) {
            requestIdleCallback(fetchAndStart, { timeout: 10000 });
        } else {
            setTimeout(fetchAndStart, 8000);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleFetch);
    } else {
        scheduleFetch();
    }

    setInterval(fetchAndStart, 300000);
})();
</script>

    </body>
</html>
