@php
    $generalsetting = \App\Models\GeneralSetting::first();
    $hasNewsTicker = $generalsetting
        && (int) ($generalsetting->news_ticker_enabled ?? 0) === 1
        && trim((string) ($generalsetting->top_headline ?? '')) !== '';
    $mobileContentPaddingTop = $hasNewsTicker ? '124px' : '94px';
@endphp
@media only screen and (min-width:767px) {
    .mobile-menu {
        display: none;
    }
    .mobile-search {
        display: none;
    }
    .mobile-filter-toggle {
        display: none;
    }
}
@media only screen and (min-width:320px) and (max-width:767px) {
    .payment-method-wrappers {
        grid-template-columns: repeat(2, 1fr);
    }
    .filter_sort {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        padding: 0 6px;
        margin-top: 12px;
    }
    .filter_sidebar {
        position: fixed;
        visibility: hidden;
        opacity: 0;
        top: 30px;
        transition: 0.35s all;
        background: #fff;
    }
    .filter_sidebar.active {
        background: #fff;
        z-index: 9999;
        height: 100%;
        width: 97%;
        top: 2px;
        visibility: visible;
        opacity: 1;
        overflow-y: auto;
    }
    .filter_sidebar.active::-webkit-scrollbar-track
    {
    	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    	background-color: #F5F5F5;
    }
    
    .filter_sidebar.active::-webkit-scrollbar
    {
    	width: 6px;
    	background-color: #F5F5F5;
    }
    
    .filter_sidebar.active::-webkit-scrollbar-thumb
    {
    	background-color: #00aef0;
    }

    .filter_btn {
        display: inline-block;
        background: {{$generalsetting->secodery_color}};
        color: #fff;
        width: 55px;
        height: 33px;
        line-height: 33px;
        font-size: 17px;
        border: 1px solid #ddd;
        text-align: center;
        text-transform: capitalize;
        cursor: pointer;
    }
    .filter_close {
        background: #00aef0;
        padding: 10px 15px;
        font-size: 18px;
        color: #fff;
        border-radius: 5px;
        margin: 8px 0;
        cursor: pointer;
        display: block;
    }
    .sidebar-menu {
        display: none;
    }
      li.mobile_home {
        border: 2px solid #ddd;
        margin-top: -35px;
        background: {{$generalsetting->secodery_color}};
        padding-top: 15px;
        border-radius: 50%;
        width: 75px;
        height: 75px;
    }
    
    li.mobile_home a {
        color: #fff;
    }
    
	.scrolltop {
	    display: none !important;
	}
    .hightlight_cont ul {

    padding-left: 5px;
}
    .mobile_hide {
        display:none;
    }
    .desktop_hide {
        display:block;
    }
    .sorting-section {
    margin-top: -15px;
}
    .card-body.cartlist {
    overflow-x: scroll;
}
    .section-title-header .section-title-name {
    font-size: 15px;
}
	#content {
     margin-left: 0;
     padding-top: {{ $mobileContentPaddingTop }};
   }
   .meta_description {
   	display: none;
   }
	.mm-ocd {
    display: none;
   }
    .page-sort {
        padding-right: 6px;
    }
    .showing-data {
        display: none;
    }
    .mobile-filter-toggle {
        display: flex;
        justify-content: center;
        margin: 10px 0;
        align-items: center;
        column-gap: 10px;
        display: none;
    }
.mobile-filter-toggle span {
    font-size: 17px;
    text-transform: uppercase;
    font-weight: 500;
}
    .home-slider-container {
        width: 100%;
        margin-left: 0;
    }
    .feature-products p {
        padding-left: 20px;
    }
.feature-products {
    position: fixed;
    top: 0;
    z-index: 99999;
    background-color: #fff;
    left: -300px;
    width: 300px;
    padding-top: 10px;
    height: 100vh;
    overflow-y: auto;
    transition: all 0.3s ease;
}
.category-breadcrumb {
    justify-content: center;
}
.feature-products.active {
    left: 0;
}

    /* Clean modern responsive overrides handled in master.blade.php */
    .logo-area {
        display: none;
    }
    .menu-area {
        display: none;
    }
    .header-left {
        justify-content: center;
    }
    .header-right {
        display: none;
    }
	.category-item {
	    padding: 5px 0;
	}
	.category-item p {
	    font-weight: 400;
	}
.qty-cart .quantity {
    height: 35px;
    margin-left: 4px;
}
	.quantity .minus,.quantity .plus {
	    height: 35px;
	    line-height: 35px;
	    width: 35px;
	    font-size: 35px;
	}
.d-flex.single_product.col-sm-6 {
    margin-left: 4px;
    margin-top: 10px;
}

	.qty-cart {
	    width: auto !important;
	}
	.cus-order-2 {
	    order: 2;
	}
	.cus-order-1 {
	    order: 1;
	}
	.chheckout-section {
	    padding: 10px 0;
	}
	.cart_details{
	    margin-bottom: 15px;
	}
    .success-img img {
        width: 200px;
    }
	.main_product_inner {
	    grid-template-columns: 1fr 1fr;
	    grid-gap: 8px;
	}
    .product_item {
        padding: 5px !important;
        margin-bottom: 10px !important;
        border-radius: 8px !important;
    }
    .pro_img {
        height: 165px !important;
        border-radius: 6px !important;
    }
    .pro_name {
        margin-top: 5px !important;
        margin-bottom: 2px !important;
    }
    .pro_name a {
        font-size: 13px !important;
    }
    .pro_price {
        margin-top: 2px !important;
        margin-bottom: 3px !important;
    }
    .pro_price p {
        font-size: 14.5px !important;
    }
    .product_item .order-btn,
    .product_item .order-btn-link {
        height: 33px !important;
        font-size: 12px !important;
        padding: 3px 6px !important;
    }
    .product_item .cart-icon-btn,
    .product_item .cart-icon-link {
        height: 33px !important;
        flex: 0 0 36px !important;
        max-width: 36px !important;
    }
    .product_item .cart-icon-btn i,
    .product_item .cart-icon-link i {
        font-size: 15px !important;
    }
	.qty-cart {
		grid-template-columns: 130px auto;
	}
	.quantity .minus {
		width: 40px;
	}
	.quantity .plus {
		width: 40px;
	} 
	.compare_store.mobile-show {
        display: block !important;
        line-height: 42px;
        text-align: center;
    }
    
	.add-to-cart.mobile-fix {
		position: fixed;
		bottom: 64px;
		left: 0;
		right: 0;
		z-index: 999;
		padding: 10px;
		margin: 0 10px;
	}
	.footer-top {
		padding: 30px 0;
	}
	.footer-menu ul li img {
		margin: 0 auto;
		display: block;
	}
	.front-view-flex {
		padding: 10px 0px;
	}
	.front_category_title h1 {
		font-size: 20px;
	}
	.front-view-item {
		margin-bottom: 15px;
	}
	.front-view-title a {
		font-size: 13px;
	}
	.home-page-section-title-box h3 {
		font-size: 20px;
	}
	.category-banner-products {
		padding: 20px 5px;
	}
    .flash-product-section .col-sm-4 {
        padding: 10px 10px !important;
    }
    .flash_all {
        margin-top: 10px;
    }
    .slider-section .offset-sm-3 {
        padding-left: 5px;
    }
	.main-header {
	    display: none;
	}
	.mobile-header {
	    display: block;
	    background-color: #fff;
	}
	.mobile-top {
	    background: #108BC3;
	    padding: 8px 0;
	}
	.mobile-top ul{
		text-align: right;
	}
	.mobile-top ul li a {
	    color: #fff;
	    margin: 0 5px;
	}
	.menu-bar i {
    font-size: 22px;
}
.mobile-logo {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    height: 52px;
    padding: 0 12px;
    gap: 12px;
}
.fixed-top .mobile-logo {
    margin-bottom: 0px;
}
/* Logo — বাম দিকে সুন্দরভাবে অ্যালাইন */
.menu-logo {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    min-width: 0;
}
.menu-logo a {
    display: inline-flex;
    align-items: center;
}
.menu-logo img {
    width: auto;
    max-width: 145px;
    height: 38px;
    max-height: 38px;
    margin-top: 0;
    object-fit: contain;
}

/* Header Actions Wrapper — ডানে কার্ট ও টগল বাটন পাশাপাশি পারফেক্ট অ্যালাইনমেন্টে */
.mobile-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.mobile-logo .menu-bag,
.mobile-logo .menu-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 0;
    flex-shrink: 0;
}

/* Cart & Toggle Buttons — একদম সমান মাপ ও সুন্দর লুক */
.menu-bag .margin-shopping,
.menu-bar a.toggle {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.2s ease;
    box-sizing: border-box;
    padding: 0;
    margin: 0;
    line-height: 1;
}

.menu-bag .margin-shopping:hover,
.menu-bar a.toggle:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.menu-bag .margin-shopping i,
.menu-bar i {
    font-size: 17px !important;
    color: #334155;
    line-height: 1;
    display: inline-block;
    transition: color 0.2s ease;
}

.menu-bag .margin-shopping:hover i,
.menu-bar a.toggle:hover i {
    color: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
}

/* Cart Badge — পারফেক্ট পজিশনিং ও সাইজিং */
.menu-bag .margin-shopping span.mobilecart-qty {
    position: absolute;
    top: -5px;
    right: -5px;
    background: {{ optional($generalsetting)->primary_color ?? '#007bff' }};
    color: #ffffff;
    min-width: 18px;
    height: 18px;
    border-radius: 10px;
    padding: 0 4px;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    line-height: 1;
}

.footer-menu ul li a {
    text-align: center;
    text-transform: capitalize;
}
.main-search.mobile-search {
    margin: 18px 0;
    padding: 0 10px;
}
	.menu-bag ul li a {
	    margin-right: 15px;
	    position: relative;
	}
	.menu-bag li span {
	    background: #E62E04;
	    font-size: 10px;
	    color: #fff;
	    position: absolute;
	    top: 0;
	    left: 12px;
	    width: 15px;
	    height: 15px;
	    border-radius: 50px;
	    line-height: 15px;
	}

	.slider-item {
	    height: 150px;
	    margin-top: 0px;
	}
	.footer-about p {
	    text-align: center;
	}
	.footer-bottom {
    margin-bottom: 24px;
    padding-bottom: 77px;
}
	.footer-about {
        text-align: center;
        padding: 0 15px;
    }
	/* Footer V2: responsive handled in master; keep legacy .footer_nav for other pages */
	.footer_nav {
	    display: block;
	}
	.section-title-left h4 {
	    font-size: 16px;
	}
	.section-title-right a {
	    font-size: 13px;
	}
	.product-info .name {
	    height: 70px;
	}
	.product-info {
	    padding: 15px 10px;
	}
	.row>* {
	    padding-right: calc(var(--bs-gutter-x) * .3);
	    padding-left: calc(var(--bs-gutter-x) * .3);
	}
	.feature-title ul {
	    text-align: center;
	    overflow-y: scroll;
	}
	.feature-title h4 {
		text-align: center;
	    margin-bottom: 10px;
	}
	.footer-top {
	    padding-bottom: 32px;
	}

	.category-sidebar {
	    position: fixed;
	    z-index: 9999;
	    width: 100%;
	    top: 0;
	    background: #fff;
	    left: 0;
	    visibility: hidden;
	    opacity: 0;
	    transition: 0.35s all;
	}
	.close_filter {
	    position: absolute;
	    top: 0;
	    right: 12px;
	    border: 2px solid #ddd;
	    font-size: 19px;
	    padding: 0px 10px;
	    border-radius: 50px;
	    background: #d3b520;
	    color: #fff;
	}
	.close_filter,.show_filter {
	    display: block;
	}
	.show_filter {
	    display: inline-block;
	    margin-right: 10px;
	    margin-left: 8px;
	}
	.page-title h5 {
   		 font-size: 16px;
	}
	.product-section {
	    margin-top: 0;
	}
	.sort-form select {
	    font-size: 14px;
	}
	.category-sidebar.active {
	    visibility: visible;
	    opacity: 1;
	}
	.auth-section, .checkout-shipping {
      margin-top: 5px;
    }
	.payment-form .gap-3 {
	    gap: 0 !important;
	}
	.modal-view.quick-product {
    	width: 100%;
	}
	.quick-product .short_description,.quick-product .details_short {
	    display: none;
	} 	
	.quick-product-img {
	    width: 20%;
	}
	.quick-product-content {
	    width: 80%;
	}
	.close-modal {
	    left: 50%;
	    top: -17px;
	    transform: translateX(-50%);
	}
	.vcart-section {
	    margin-top: 60px;
	}
	.menu-product{
		display: none;
	}
	.details-wishlist {
        display: none !important;
    }
    a.details-wishlist.compare_store.cursor {
    display: none !important;
}
}
@media only screen and (min-width:767px) and (max-width:991px) {
.menu-product{
	display: none;
}

}
@media only screen and (min-width:992px) and (max-width:1140px) {


}
@media only screen and (min-width:1141px){


}


/* ============================================================
   GLOBAL OVERFLOW & BOX-SIZING FIX
   ============================================================ */
*,
*::before,
*::after {
    box-sizing: border-box;
}

html {
    overflow-x: hidden;
    max-width: 100%;
}

body {
    overflow-x: hidden;
    max-width: 100%;
}

img {
    max-width: 100%;
    height: auto;
}

/* ============================================================
   HEADER — DESKTOP FIXES
   ============================================================ */
@media only screen and (min-width: 992px) {
    header {
        width: 100%;
        max-width: 100%;
        overflow: visible;
    }

    .logo-header {
        grid-template-columns: 18% 60% 22%;
        align-items: center;
    }

    .main-search {
        width: 100%;
    }

    .main-search form {
        width: 100%;
        max-width: 100%;
    }

    .main-search form input {
        width: 88%;
    }

    .main-search form button {
        width: 12%;
    }

    .heder__category {
        grid-template-columns: 24% 8% 10% 44% 14%;
        overflow: hidden;
    }

    li.all__category__list {
        max-width: 100%;
        overflow: hidden;
    }

    .side__bar,
    .side__barsub,
    .side__barchild {
        max-width: 290px;
    }
}

/* ============================================================
   HEADER — TABLET FIXES (768px – 991px)
   ============================================================ */
@media only screen and (min-width: 768px) and (max-width: 991px) {
    .main-header {
        display: block;
    }
    .mobile-header {
        display: none;
    }

    .logo-header {
        grid-template-columns: 22% 52% 26%;
        gap: 8px;
        align-items: center;
    }

    .main-logo {
        height: 45px;
    }

    .main-logo img {
        height: 100%;
        width: auto;
    }

    .main-search form {
        width: 100%;
    }

    .main-search form input {
        width: 85%;
        font-size: 13px;
    }

    .main-search form button {
        width: 15%;
    }

    .heder__category {
        grid-template-columns: 28% 8% 12% 36% 16%;
        font-size: 14px;
    }

    li.all__category__list {
        width: 100%;
        font-size: 15px;
    }

    li.all__category__list i {
        padding-left: 20px;
    }

    .catagory_menu ul li a {
        font-size: 14px;
        margin: 0 5px;
    }

    #content {
        padding-top: {{ $hasNewsTicker ? '150px' : '120px' }};
    }
}

/* ============================================================
   SLIDER SECTION — RESPONSIVE
   ============================================================ */
@media only screen and (max-width: 991px) {
    .col-sm-3.hidetosm {
        display: none;
    }

    .col-sm-9 {
        width: 100%;
        flex: 0 0 100%;
        max-width: 100%;
    }

    .home-slider-container {
        width: 100%;
        margin-left: 0;
        padding: 0;
    }

    .slider-item {
        width: 100%;
        height: auto;
        max-height: 350px;
        overflow: hidden;
    }

    .slider-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

@media only screen and (max-width: 767px) {
    .slider-item {
        height: 180px;
    }

    .slider-item img {
        object-fit: cover;
    }
}

/* ============================================================
   CONTAINER — PREVENT OVERFLOW
   ============================================================ */
.container {
    max-width: 1200px;
    width: 100%;
    padding-left: 15px;
    padding-right: 15px;
    margin-left: auto;
    margin-right: auto;
}

@media only screen and (max-width: 767px) {
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }

    .row {
        margin-left: -5px;
        margin-right: -5px;
    }

    .row > * {
        padding-left: 5px;
        padding-right: 5px;
    }
}

/* ============================================================
   NEWS TICKER — RESPONSIVE
   ============================================================ */
@media only screen and (max-width: 767px) {
    .news-ticker-bar {
        height: 30px;
    }

    .news-ticker-label {
        font-size: 9px;
        padding: 0 8px;
        letter-spacing: 0.5px;
    }

    .news-ticker-item {
        font-size: 11px;
        padding-right: 40px;
    }

    .news-ticker-item::after {
        margin-left: 40px;
    }
}

/* ============================================================
   PRODUCT DETAILS — STICKY VIDEO
   ============================================================ */
@media only screen and (max-width: 767px) {
    section.pro_details_area .product-video-sticky-wrap {
        position: static;
        top: auto;
    }
}

/* ============================================================
   PREVENT HORIZONTAL SCROLL
   ============================================================ */
section,
.homeproduct,
.slider-section,
.brand-section,
.vendor-shops-section,
.blog-home-section,
.footer-v2 {
    max-width: 100%;
    overflow-x: hidden;
}

section.pro_details_area {
    overflow: visible;
}