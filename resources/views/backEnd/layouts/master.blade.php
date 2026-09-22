<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />

    <title>@yield('title')@if(isset($generalsetting) && $generalsetting) - {{$generalsetting->name}}@endif</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset(isset($generalsetting->favicon) ? $generalsetting->favicon : 'public/backEnd/assets/images/favicon.ico')}}" />

    <!-- Bootstrap css -->
    <link href="{{asset('public/backEnd/')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="{{asset('public/backEnd/')}}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="{{asset('public/backEnd/')}}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- toastr css -->
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets/css/toastr.min.css" />
    <!-- SweetAlert2 - ডেমো মুড পপআপের জন্য -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <!-- custom css -->
    <link href="{{asset('public/backEnd/')}}/assets/css/custom.css" rel="stylesheet" type="text/css" />
    <!-- Head js -->
    @yield('css')
    <script src="{{asset('public/backEnd/')}}/assets/js/head.js"></script>
  </head>

  <!-- body start -->
  <body data-layout-mode="default" data-theme="light" data-layout-width="fluid" data-topbar-color="dark" data-menu-position="fixed" data-leftbar-color="light" data-leftbar-size="default" data-sidebar-user="false">
    <!-- Begin page -->
    <div id="wrapper">
      <!-- Topbar Start -->
      <div class="navbar-custom">
        <div class="container-fluid">
          <ul class="list-unstyled topnav-menu float-end mb-0">
            <li class="dropdown d-inline-block d-lg-none">
              <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-search noti-icon"></i>
              </a>
              <div class="dropdown-menu dropdown-lg dropdown-menu-end p-0">
                <form class="p-3">
                  <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username" />
                </form>
              </div>
            </li>

            <li class="dropdown d-none d-lg-inline-block">
              <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="fullscreen" href="#">
                <i class="fe-maximize noti-icon"></i>
              </a>
            </li>

            @if(isset($demoMode) && $demoMode)
            <li class="dropdown d-none d-lg-inline-block">
              <span class="badge bg-warning text-dark px-2 py-1 mt-1" title=".env থেকে DEMO_MODE=true সেট করা আছে"><i class="fe-eye me-1"></i>ডেমো</span>
            </li>
            @endif

            <li class="dropdown notification-list topbar-dropdown">
              <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon"></i>
                <span class="badge bg-danger rounded-circle noti-icon-badge">{{$neworder}}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-lg">
                <!-- item-->
                <div class="dropdown-item noti-title">
                  <h5 class="m-0">
                    <span class="float-end">
                      <a href="{{route('admin.orders',['slug'=>'pending'])}}" class="text-dark">
                        <small>View All</small>
                      </a>
                    </span>
                    Orders
                  </h5>
                </div>

                <div class="noti-scroll" data-simplebar>
                  @foreach($pendingorder as $porder)
                  <!-- item-->
                  <a href="{{route('admin.orders',['slug'=>'pending'])}}" class="dropdown-item notify-item active">
                    <div class="notify-icon">
                      <img src="{{asset($porder->customer?$porder->customer->image:'')}}" class="img-fluid rounded-circle" alt="" />
                    </div>
                    <p class="notify-details">{{$porder->customer?$porder->customer->name:''}}</p>
                    <p class="text-muted mb-0 user-msg">
                      <small>Invoice : {{$porder->invoice_id}}</small>
                    </p>
                  </a>
                  @endforeach

                  <!-- item-->
                </div>

                <!-- All-->
                <a href="{{route('admin.orders',['slug'=>'pending'])}}" class="dropdown-item text-center text-primary notify-item notify-all">
                  View all
                  <i class="fe-arrow-right"></i>
                </a>
              </div>
            </li>

            <li class="dropdown notification-list topbar-dropdown">
              <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="{{asset(Auth::guard('admin')->user()->image)}}" alt="user-image" class="rounded-circle" />
                <span class="pro-user-name ms-1"> {{Auth::guard('admin')->user()->name}} <i class="mdi mdi-chevron-down"></i> </span>
              </a>
              <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                <!-- item-->
                <div class="dropdown-header noti-title">
                  <h6 class="text-overflow m-0">Welcome !</h6>
                </div>

                <!-- item-->
                <a href="{{url('admin/dashboard')}}" class="dropdown-item notify-item">
                  <i class="fe-user"></i>
                  <span>Dashboard</span>
                </a>

                <!-- item-->

                <div class="dropdown-divider"></div>

                <!-- item-->
                <a
                  href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();"
                  class="dropdown-item notify-item"
                >
                  <i class="fe-log-out me-1"></i>
                  <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </div>
            </li>

            <!--<li class="dropdown notification-list">-->
            <!--    <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">-->
            <!--        <i class="fe-settings noti-icon"></i>-->
            <!--    </a>-->
            <!--</li>-->
          </ul>

          <!-- LOGO -->
          <div class="logo-box">
            <a href="{{url('admin/dashboard')}}" class="logo logo-dark text-center">
              <span class="logo-sm">
                <img src="{{asset(isset($generalsetting->white_logo) ? $generalsetting->white_logo : 'public/backEnd/assets/images/logo.png')}}" alt="" height="50" />
                <!-- <span class="logo-lg-text-light">UBold</span> -->
              </span>
              <span class="logo-lg">
                <img src="{{asset(isset($generalsetting->white_logo) ? $generalsetting->white_logo : 'public/backEnd/assets/images/logo.png')}}" alt="" height="50" />
                <!-- <span class="logo-lg-text-light">U</span> -->
              </span>
            </a>

            <a href="{{url('admin/dashboard')}}" class="logo logo-light text-center">
              <span class="logo-sm">
                <img src="{{asset(isset($generalsetting->white_logo) ? $generalsetting->white_logo : 'public/backEnd/assets/images/logo.png')}}" alt="" height="50" />
              </span>
              <span class="logo-lg">
                <img src="{{asset(isset($generalsetting->white_logo) ? $generalsetting->white_logo : 'public/backEnd/assets/images/logo.png')}}" alt="" height="50" />
              </span>
            </a>
          </div>

          <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
            <li>
              <button class="button-menu-mobile waves-effect waves-light" id="sidebar-toggle-btn" title="Toggle Sidebar">
                <i class="fe-menu"></i>
              </button>
            </li>

            <li>
              <!-- Mobile menu toggle (Horizontal Layout)-->
              <a class="navbar-toggle nav-link" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </a>
              <!-- End mobile menu toggle-->
            </li>

            <li class="dropdown d-none d-xl-block">
              <a class="nav-link dropdown-toggle waves-effect waves-light" href="{{route('home')}}" target="_blank"> <i data-feather="globe"></i> Visit Site </a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
      </div>
      <!-- end Topbar -->

      <!-- ========== Left Two-Column Sidebar Start ========== -->
@php
  use Illuminate\Support\Facades\Auth;
  $user = Auth::guard('admin')->user();
  $pending_reviews = \App\Models\Review::where('status', 'pending')->count();
  $vendorEnabled = (isset($generalsetting) && $generalsetting) ? (isset($generalsetting->vendor_enabled) ? $generalsetting->vendor_enabled : 1) : 1;
  $resellerEnabled = (isset($generalsetting) && $generalsetting) ? (isset($generalsetting->reseller_enabled) ? $generalsetting->reseller_enabled : 1) : 1;

  // Determine active section based on current route
  $activeSection = 'section-main';
  if (request()->routeIs('users.*', 'roles.*', 'permissions.*', 'customers.*', 'admin.vendors.*', 'admin.vendor.*', 'admin.resellers.*', 'admin.reseller.*', 'admin.reseller-deposits.*', 'admin.delivery.*', 'admin.delivery-boys.*', 'admin.employees.*', 'admin.attendances.*', 'admin.leaves.*', 'admin.salaries.*', 'admin.bonuses.*', 'admin.salary_payments.*')) {
      $activeSection = 'section-people';
  } elseif (request()->routeIs('admin.orders', 'admin.reseller-orders.*', 'admin.incomplete-orders.*', 'orderstatus.*', 'customers.ip_block', 'admin.refunds.*', 'manualFraud.page', 'admin.order.restriction.setting.*') || request()->is('admin/orders/*')) {
      $activeSection = 'section-orders';
  } elseif (request()->routeIs('inhouse.products.*', 'products.*', 'categories.*', 'subcategories.*', 'childcategories.*', 'brands.*', 'colors.*', 'sizes.*', 'admin.products.wholesale', 'purchases.*', 'admin.suppliers.*')) {
      $activeSection = 'section-catalog';
  } elseif (request()->routeIs('admin.fund.*', 'admin.expenses.*')) {
      $activeSection = 'section-finance';
  } elseif (request()->routeIs('settings.*', 'socialmedias.*', 'contact.*', 'pages.*', 'paymentgeteway.*', 'manual-payment-gateway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.gemini_ai.*', 'admin.fraud.*', 'email_setting*', 'backEnd.complaints.*', 'admin.contact.messages*', 'admin.newsletter.subscribers*', 'admin.seo_settings.*', 'admin.sitemap.*', 'admin.cron.*', 'error-log.*')) {
      $activeSection = 'section-settings';
  } elseif (request()->routeIs('admin.ads_analytics.*', 'tagmanagers.*', 'pixels.*', 'tiktok.pixels.*', 'admin.facebook_page.*', 'admin.reports.*')) {
      $activeSection = 'section-analytics';
  } elseif (request()->routeIs('campaign.*', 'admin.coupons.*', 'banners.*', 'admin.popup.*', 'admin.sale-notification.*', 'reviews.*', 'admin.blog.*', 'admin.sms.custom.*')) {
      $activeSection = 'section-marketing';
  }
@endphp

      <div class="left-side-menu two-column-sidebar" id="two-column-sidebar">
        <!-- 1. LEFT ICON RAIL (Always visible on desktop & in collapsed mode) -->
        <div class="sidebar-icon-rail">
          <div class="icon-rail-header">
            <a href="{{ url('admin/dashboard') }}" class="rail-logo" title="{{ isset($generalsetting->name) ? $generalsetting->name : 'Dashboard' }}">
              <img src="{{ asset(isset($generalsetting->favicon) && $generalsetting->favicon ? $generalsetting->favicon : (isset($generalsetting->white_logo) && $generalsetting->white_logo ? $generalsetting->white_logo : 'public/backEnd/assets/images/logo.png')) }}" alt="Logo" class="rail-brand-logo" />
            </a>
          </div>

          <div class="icon-rail-nav">
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-main' ? 'active' : '' }}" data-section="section-main" title="Main Menu" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="grid"></i>
            </a>

            @canany(['user-list', 'role-list', 'permission-list', 'customer-list', 'vendor-list', 'reseller-list', 'delivery-boy-list', 'employee-list'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-people' ? 'active' : '' }}" data-section="section-people" title="People & Partners" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="users"></i>
            </a>
            @endcanany

            @canany(['order-list', 'order-edit', 'order-create', 'fraud-check'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-orders' ? 'active' : '' }}" data-section="section-orders" title="Sales & Orders" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="shopping-cart"></i>
              @if(isset($neworder) && $neworder > 0)
                <span class="rail-badge">{{ $neworder }}</span>
              @endif
            </a>
            @endcanany

            @canany(['product-list', 'category-list', 'subcategory-list', 'childcategory-list', 'brand-list', 'color-list', 'size-list', 'purchase-list', 'supplier-list'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-catalog' ? 'active' : '' }}" data-section="section-catalog" title="Catalog & Inventory" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="package"></i>
            </a>
            @endcanany

            @canany(['fund-list', 'fund-create', 'fund-edit', 'expense-list', 'expense-create', 'expense-edit'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-finance' ? 'active' : '' }}" data-section="section-finance" title="Finance & Accounts" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="dollar-sign"></i>
            </a>
            @endcanany

            @canany(['setting-list', 'social-list', 'contact-list', 'api-manage', 'email-setting-list', 'complaint-list', 'seo-manage', 'sitemap-manage', 'cache-clear', 'error-log-view'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-settings' ? 'active' : '' }}" data-section="section-settings" title="System & Settings" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="settings"></i>
            </a>
            @endcanany

            @canany(['pixel-manage', 'report-view', 'order-report', 'purchase-report', 'expense-report', 'stock-report', 'profit-loss-report'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-analytics' ? 'active' : '' }}" data-section="section-analytics" title="Analytics & Reports" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="bar-chart-2"></i>
            </a>
            @endcanany

            @canany(['campaign-list', 'coupon-list', 'banner-list', 'popup-list', 'review-list', 'blog-list', 'sms-send'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-marketing' ? 'active' : '' }}" data-section="section-marketing" title="Marketing & Promotions" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="volume-2"></i>
              @if($pending_reviews > 0)
                <span class="rail-badge">{{ $pending_reviews }}</span>
              @endif
            </a>
            @endcanany
          </div>

          <div class="icon-rail-footer">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('rail-logout-form').submit();" class="rail-item rail-logout-btn" title="Logout" data-bs-toggle="tooltip" data-bs-placement="right">
              <i data-feather="log-out"></i>
            </a>
            <form id="rail-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </div>
        </div>

        <!-- 2. SUBMENU PANEL (Collapsible) -->
        <div class="sidebar-subpanel" id="sidebar-subpanel">
          <div class="subpanel-header">
            <div class="store-branding">
              <a href="{{ url('admin/dashboard') }}" class="store-name-link">
                <div class="store-avatar">
                  <img src="{{ asset(isset($generalsetting->white_logo) && $generalsetting->white_logo ? $generalsetting->white_logo : 'public/backEnd/assets/images/logo.png') }}" alt="Logo" />
                </div>
                <div class="store-info">
                  <h6 class="store-title">{{ isset($generalsetting->name) ? $generalsetting->name : 'ecomlaravel1' }}</h6>
                  <span class="store-subtitle">Shop Management</span>
                </div>
              </a>
            </div>
            <button type="button" class="subpanel-toggle-btn" id="sidebar-collapse-btn" title="Collapse Sidebar">
              <i data-feather="sidebar"></i>
            </button>
          </div>

          <!-- Quick Jump Search -->
          <div class="subpanel-search">
            <div class="search-wrap">
              <i data-feather="search"></i>
              <input type="text" id="sidebar-jump-search" placeholder="Jump to..." autocomplete="off" />
              <button type="button" class="clear-search-btn" id="clear-jump-search" style="display:none;">&times;</button>
            </div>
          </div>

          <!-- Submenu Body -->
          <div class="subpanel-body" data-simplebar>

            {{-- 1. MAIN PANE --}}
            <div class="section-pane {{ $activeSection === 'section-main' ? 'active' : '' }}" id="pane-section-main">
              <div class="pane-title">Main Menu</div>
              <ul class="subpanel-menu">
                @can('dashboard-view')
                <li class="{{ request()->is('admin/dashboard') ? 'menuitem-active' : '' }}">
                  <a href="{{ url('admin/dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i data-feather="airplay"></i>
                    <span> Dashboard </span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('admin.gemini_chat.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.gemini_chat.index') }}" class="{{ request()->routeIs('admin.gemini_chat.*') ? 'active' : '' }}">
                    <i data-feather="message-circle"></i>
                    <span> Gemini Assistant </span>
                  </a>
                </li>
                @endcan

                @can('order-create')
                <li class="{{ request()->routeIs('admin.order.create') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.order.create') }}" class="{{ request()->routeIs('admin.order.create') ? 'active' : '' }}">
                    <i data-feather="cpu"></i>
                    <span> POS System </span>
                  </a>
                </li>
                @endcan
              </ul>
            </div>

            {{-- 2. PEOPLE & PARTNERS PANE --}}
            <div class="section-pane {{ $activeSection === 'section-people' ? 'active' : '' }}" id="pane-section-people">
              <div class="pane-title">People & Partners</div>
              <ul class="subpanel-menu">
                @canany(['user-list', 'role-list', 'permission-list', 'customer-list'])
                <li class="{{ request()->routeIs('users.*', 'roles.*', 'permissions.*', 'customers.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-users" data-bs-toggle="collapse" class="{{ request()->routeIs('users.*', 'roles.*', 'permissions.*', 'customers.*') ? 'active' : '' }}">
                    <i data-feather="users"></i>
                    <span> Users & Customers </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('users.*', 'roles.*', 'permissions.*', 'customers.*') ? 'show' : '' }}" id="sub-users">
                    <ul class="subpanel-nested-menu">
                      @canany(['customer-list', 'customer-create', 'customer-edit'])
                      <li><a href="{{ route('customers.index') }}"><i data-feather="file-plus"></i> Customers</a></li>
                      @endcanany
                      @can('user-list')
                      <li><a href="{{ route('users.index') }}"><i data-feather="file-plus"></i> Users</a></li>
                      @endcan
                      @can('role-list')
                      <li><a href="{{ route('roles.index') }}"><i data-feather="file-plus"></i> Roles</a></li>
                      @endcan
                      @can('permission-list')
                      <li><a href="{{ route('permissions.index') }}"><i data-feather="file-plus"></i> Permissions</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @if($vendorEnabled == 1)
                @canany(['vendor-list', 'vendor-create', 'vendor-edit', 'vendor-verification', 'vendor-withdrawal'])
                @php
                  $pendingVerificationCount = \App\Models\Vendor::where('verification_status', 'pending')->count();
                @endphp
                <li class="{{ request()->routeIs('admin.vendors.*', 'admin.vendor.verification.*', 'admin.vendor.withdrawals.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-vendors" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.vendors.*', 'admin.vendor.verification.*', 'admin.vendor.withdrawals.*') ? 'active' : '' }}">
                    <i data-feather="shopping-bag"></i>
                    <span> Vendors </span>
                    @if($pendingVerificationCount > 0)
                      <span class="badge bg-danger rounded-pill float-end">{{ $pendingVerificationCount }}</span>
                    @else
                      <span class="menu-arrow"></span>
                    @endif
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.vendors.*', 'admin.vendor.verification.*', 'admin.vendor.withdrawals.*') ? 'show' : '' }}" id="sub-vendors">
                    <ul class="subpanel-nested-menu">
                      @can('vendor-list')
                      <li><a href="{{ route('admin.vendors.index') }}"><i data-feather="file-plus"></i> All Vendors</a></li>
                      @endcan
                      @can('vendor-verification')
                      <li>
                        <a href="{{ route('admin.vendor.verification.index') }}">
                          <i data-feather="shield"></i> Verifications
                          @if($pendingVerificationCount > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ $pendingVerificationCount }}</span>
                          @endif
                        </a>
                      </li>
                      @endcan
                      @can('vendor-withdrawal')
                      <li><a href="{{ route('admin.vendor.withdrawals.index') }}"><i data-feather="dollar-sign"></i> Withdrawals</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany
                @endif

                @if($resellerEnabled == 1)
                @canany(['reseller-list', 'reseller-create', 'reseller-edit', 'reseller-verification', 'reseller-withdrawal'])
                @php
                  $pendingResellerVerificationCount = \App\Models\User::where('role', 'reseller')->where('verification_status', 'pending')->count();
                  $pendingResellerWithdrawalCount = \App\Models\ResellerWithdrawal::where('status', 'pending')->count();
                  $pendingDepositCount = \App\Models\ResellerDeposit::where('status', 'pending')->count();
                @endphp
                <li class="{{ request()->routeIs('admin.resellers.*', 'admin.reseller.verification.*', 'admin.reseller.withdrawals.*', 'admin.reseller-deposits.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-resellers" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.resellers.*', 'admin.reseller.verification.*', 'admin.reseller.withdrawals.*', 'admin.reseller-deposits.*') ? 'active' : '' }}">
                    <i data-feather="user-check"></i>
                    <span> Resellers </span>
                    @if(($pendingResellerVerificationCount + $pendingResellerWithdrawalCount + $pendingDepositCount) > 0)
                      <span class="badge bg-danger rounded-pill float-end">{{ $pendingResellerVerificationCount + $pendingResellerWithdrawalCount + $pendingDepositCount }}</span>
                    @else
                      <span class="menu-arrow"></span>
                    @endif
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.resellers.*', 'admin.reseller.verification.*', 'admin.reseller.withdrawals.*', 'admin.reseller-deposits.*') ? 'show' : '' }}" id="sub-resellers">
                    <ul class="subpanel-nested-menu">
                      @can('reseller-list')
                      <li><a href="{{ route('admin.resellers.index') }}"><i data-feather="file-plus"></i> All Resellers</a></li>
                      @endcan
                      @can('reseller-withdrawal')
                      <li>
                        <a href="{{ route('admin.reseller-deposits.index') }}">
                          <i data-feather="credit-card"></i> Reseller Deposits
                          @if($pendingDepositCount > 0)
                            <span class="badge bg-warning rounded-pill float-end">{{ $pendingDepositCount }}</span>
                          @endif
                        </a>
                      </li>
                      @endcan
                      @can('reseller-verification')
                      <li>
                        <a href="{{ route('admin.reseller.verification.index') }}">
                          <i data-feather="shield"></i> Verifications
                          @if($pendingResellerVerificationCount > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ $pendingResellerVerificationCount }}</span>
                          @endif
                        </a>
                      </li>
                      @endcan
                      @can('reseller-withdrawal')
                      <li>
                        <a href="{{ route('admin.reseller.withdrawals.index') }}">
                          <i data-feather="dollar-sign"></i> Withdrawals
                          @if($pendingResellerWithdrawalCount > 0)
                            <span class="badge bg-warning rounded-pill float-end">{{ $pendingResellerWithdrawalCount }}</span>
                          @endif
                        </a>
                      </li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany
                @endif

                @canany(['shipping-list', 'shipping-create', 'shipping-edit', 'delivery-boy-list', 'delivery-withdrawal-list', 'delivery-location-list'])
                <li class="{{ request()->routeIs('admin.delivery.*', 'admin.delivery-boys.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-delivery" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.delivery.*', 'admin.delivery-boys.*') ? 'active' : '' }}">
                    <i data-feather="truck"></i>
                    <span> Delivery / Riders </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.delivery.*', 'admin.delivery-boys.*') ? 'show' : '' }}" id="sub-delivery">
                    <ul class="subpanel-nested-menu">
                      @can('delivery-boy-list')
                      <li><a href="{{ route('admin.delivery-boys.index') }}"><i data-feather="users"></i> Delivery Persons</a></li>
                      @endcan
                      @can('delivery-withdrawal-list')
                      <li><a href="{{ route('admin.delivery-boys.withdrawals') }}"><i data-feather="dollar-sign"></i> Rider Withdrawals</a></li>
                      @endcan
                      @can('delivery-location-list')
                      <li><a href="{{ route('admin.delivery.divisions.index') }}"><i data-feather="map-pin"></i> Delivery Locations</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['employee-list', 'attendance-list', 'leave-list', 'salary-list', 'bonus-list', 'salary-payment-list'])
                <li class="{{ request()->routeIs('admin.employees.*', 'admin.attendances.*', 'admin.leaves.*', 'admin.salaries.*', 'admin.bonuses.*', 'admin.salary_payments.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-crm" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.employees.*', 'admin.attendances.*', 'admin.leaves.*', 'admin.salaries.*', 'admin.bonuses.*', 'admin.salary_payments.*') ? 'active' : '' }}">
                    <i data-feather="briefcase"></i>
                    <span> CRM / HR </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.employees.*', 'admin.attendances.*', 'admin.leaves.*', 'admin.salaries.*', 'admin.bonuses.*', 'admin.salary_payments.*') ? 'show' : '' }}" id="sub-crm">
                    <ul class="subpanel-nested-menu">
                      @can('employee-list')
                      <li><a href="{{ route('admin.employees.index') }}"><i data-feather="user"></i> Employees</a></li>
                      @endcan
                      @can('attendance-list')
                      <li><a href="{{ route('admin.attendances.index') }}"><i data-feather="check-circle"></i> Attendance</a></li>
                      @endcan
                      @can('leave-list')
                      <li><a href="{{ route('admin.leaves.index') }}"><i data-feather="calendar"></i> Leaves</a></li>
                      @endcan
                      @can('salary-list')
                      <li><a href="{{ route('admin.salaries.index') }}"><i data-feather="dollar-sign"></i> Salaries</a></li>
                      @endcan
                      @can('bonus-list')
                      <li><a href="{{ route('admin.bonuses.index') }}"><i data-feather="gift"></i> Bonuses</a></li>
                      @endcan
                      @can('salary-payment-list')
                      <li><a href="{{ route('admin.salary_payments.index') }}"><i data-feather="credit-card"></i> Salary Payments</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany
              </ul>
            </div>

            {{-- 3. SALES & ORDERS PANE --}}
            <div class="section-pane {{ $activeSection === 'section-orders' ? 'active' : '' }}" id="pane-section-orders">
              <div class="pane-title">Sales & Orders</div>
              <ul class="subpanel-menu">
                @canany(['order-list', 'order-edit', 'order-create'])
                <li class="{{ request()->routeIs('admin.orders', 'admin.reseller-orders.*', 'admin.incomplete-orders.*', 'orderstatus.*', 'customers.ip_block') || request()->is('admin/orders/*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-orders" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.orders', 'admin.reseller-orders.*', 'admin.incomplete-orders.*', 'orderstatus.*', 'customers.ip_block') || request()->is('admin/orders/*') ? 'active' : '' }}">
                    <i data-feather="shopping-cart"></i>
                    <span> Orders </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.orders', 'admin.reseller-orders.*', 'admin.incomplete-orders.*', 'orderstatus.*', 'customers.ip_block') || request()->is('admin/orders/*') ? 'show' : '' }}" id="sub-orders">
                    <ul class="subpanel-nested-menu">
                      @can('order-list')
                      <li><a href="{{ route('admin.orders', ['slug'=>'all']) }}" class="{{ request()->is('admin/orders/all') ? 'active' : '' }}"><i data-feather="file-plus"></i> All Orders</a></li>
                      <li><a href="{{ route('admin.reseller-orders.index') }}" class="{{ request()->routeIs('admin.reseller-orders.*') ? 'active' : '' }}"><i data-feather="users"></i> Reseller Orders</a></li>
                      <li><a href="{{ route('admin.incomplete-orders.index') }}" class="{{ request()->routeIs('admin.incomplete-orders.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Incomplete Orders</a></li>
                      @if(isset($orderstatus))
                        @foreach($orderstatus as $value)
                          <li><a href="{{ route('admin.orders', ['slug'=>$value->slug]) }}" class="{{ request()->is('admin/orders/'.$value->slug) ? 'active' : '' }}"><i data-feather="file-plus"></i> {{ $value->name }}</a></li>
                        @endforeach
                      @endif
                      @endcan
                      @can('order-edit')
                      <li><a href="{{ route('orderstatus.index') }}" class="{{ request()->routeIs('orderstatus.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Order Status</a></li>
                      @endcan
                      @can('order-manage')
                      <li><a href="{{ route('customers.ip_block') }}" class="{{ request()->routeIs('customers.ip_block') ? 'active' : '' }}"><i data-feather="file-plus"></i> IP Block</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['order-list', 'order-edit'])
                <li class="{{ request()->routeIs('admin.refunds.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-refunds" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}">
                    <i data-feather="rotate-ccw"></i>
                    <span> Refunds </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.refunds.*') ? 'show' : '' }}" id="sub-refunds">
                    <ul class="subpanel-nested-menu">
                      <li><a href="{{ route('admin.refunds.index') }}"><i data-feather="list"></i> All Refunds</a></li>
                      <li><a href="{{ route('admin.refunds.index', ['status' => 'pending']) }}"><i data-feather="clock"></i> Pending Refunds</a></li>
                      <li><a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}"><i data-feather="check-circle"></i> Approved Refunds</a></li>
                      <li><a href="{{ route('admin.refunds.index', ['status' => 'processed']) }}"><i data-feather="check"></i> Processed Refunds</a></li>
                    </ul>
                  </div>
                </li>
                @endcanany

                @can('fraud-check')
                <li class="{{ request()->routeIs('manualFraud.page') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('manualFraud.page') }}" class="{{ request()->routeIs('manualFraud.page') ? 'active' : '' }}">
                    <i data-feather="search"></i>
                    <span> Manual Fraud Check </span>
                  </a>
                </li>
                @endcan

                @canany(['setting-list', 'setting-edit'])
                <li class="{{ request()->routeIs('admin.order.restriction.setting.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.order.restriction.setting.index') }}" class="{{ request()->routeIs('admin.order.restriction.setting.*') ? 'active' : '' }}">
                    <i data-feather="clock"></i>
                    <span> Order Restriction </span>
                  </a>
                </li>
                @endcanany
              </ul>
            </div>

            {{-- 4. CATALOG & INVENTORY PANE --}}
            <div class="section-pane {{ $activeSection === 'section-catalog' ? 'active' : '' }}" id="pane-section-catalog">
              <div class="pane-title">Catalog & Inventory</div>
              <ul class="subpanel-menu">
                @canany(['product-list', 'category-list', 'subcategory-list', 'childcategory-list', 'brand-list', 'color-list', 'size-list'])
                <li class="{{ request()->routeIs('inhouse.products.*', 'products.*', 'categories.*', 'subcategories.*', 'childcategories.*', 'brands.*', 'colors.*', 'sizes.*', 'admin.products.wholesale') ? 'menuitem-active' : '' }}">
                  <a href="#sub-products" data-bs-toggle="collapse" class="{{ request()->routeIs('inhouse.products.*', 'products.*', 'categories.*', 'subcategories.*', 'childcategories.*', 'brands.*', 'colors.*', 'sizes.*', 'admin.products.wholesale') ? 'active' : '' }}">
                    <i data-feather="package"></i>
                    <span> Products </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('inhouse.products.*', 'products.*', 'categories.*', 'subcategories.*', 'childcategories.*', 'brands.*', 'colors.*', 'sizes.*', 'admin.products.wholesale') ? 'show' : '' }}" id="sub-products">
                    <ul class="subpanel-nested-menu">
                      @can('product-list')
                      <li><a href="{{ route('inhouse.products.index') }}"><i data-feather="package"></i> Inhouse Products</a></li>
                      <li><a href="{{ route('products.index') }}"><i data-feather="shopping-bag"></i> Vendor Products</a></li>
                      <li><a href="{{ route('products.pending') }}"><i data-feather="clock"></i> Pending Products</a></li>
                      <li><a href="{{ route('admin.products.wholesale') }}"><i data-feather="layers"></i> Wholesale Products</a></li>
                      @endcan
                      @can('product-create')
                      <li><a href="{{ route('products.create') }}"><i data-feather="plus-circle"></i> Add Product</a></li>
                      @endcan
                      @can('category-list')
                      <li><a href="{{ route('categories.index') }}"><i data-feather="file-plus"></i> Categories</a></li>
                      @endcan
                      @can('subcategory-list')
                      <li><a href="{{ route('subcategories.index') }}"><i data-feather="file-plus"></i> Subcategories</a></li>
                      @endcan
                      @can('childcategory-list')
                      <li><a href="{{ route('childcategories.index') }}"><i data-feather="file-plus"></i> Childcategories</a></li>
                      @endcan
                      @canany(['brand-list', 'brand-create', 'brand-edit'])
                      <li><a href="{{ route('brands.index') }}"><i data-feather="file-plus"></i> Brands</a></li>
                      @endcanany
                      @canany(['color-list', 'color-create', 'color-edit'])
                      <li><a href="{{ route('colors.index') }}"><i data-feather="file-plus"></i> Colors</a></li>
                      @endcanany
                      @canany(['size-list', 'size-create', 'size-edit'])
                      <li><a href="{{ route('sizes.index') }}"><i data-feather="file-plus"></i> Sizes</a></li>
                      @endcanany
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['purchase-list', 'purchase-create', 'purchase-edit'])
                <li class="{{ request()->routeIs('purchases.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <i data-feather="file-text"></i>
                    <span> Purchases </span>
                  </a>
                </li>
                @endcanany

                @canany(['supplier-list', 'supplier-create', 'supplier-edit'])
                <li class="{{ request()->routeIs('admin.suppliers.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                    <i data-feather="truck"></i>
                    <span> Suppliers </span>
                  </a>
                </li>
                @endcanany
              </ul>
            </div>

            {{-- 5. FINANCE & ACCOUNTS PANE --}}
            <div class="section-pane {{ $activeSection === 'section-finance' ? 'active' : '' }}" id="pane-section-finance">
              <div class="pane-title">Finance & Accounts</div>
              <ul class="subpanel-menu">
                @canany(['fund-list', 'fund-create', 'fund-edit'])
                <li class="{{ request()->routeIs('admin.fund.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.fund.index') }}" class="{{ request()->routeIs('admin.fund.*') ? 'active' : '' }}">
                    <i data-feather="briefcase"></i>
                    <span> Fund / Account </span>
                  </a>
                </li>
                @endcanany

                @canany(['expense-list', 'expense-create', 'expense-edit'])
                <li class="{{ request()->routeIs('admin.expenses.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                    <i data-feather="credit-card"></i>
                    <span> Expenses </span>
                  </a>
                </li>
                @endcanany
              </ul>
            </div>

            {{-- 6. SYSTEM & SETTINGS PANE --}}
            <div class="section-pane {{ $activeSection === 'section-settings' ? 'active' : '' }}" id="pane-section-settings">
              <div class="pane-title">System & Settings</div>
              <ul class="subpanel-menu">
                @canany(['setting-list', 'social-list', 'contact-list', 'page-list'])
                <li class="{{ request()->routeIs('settings.*', 'socialmedias.*', 'contact.*', 'pages.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-sitesetting" data-bs-toggle="collapse" class="{{ request()->routeIs('settings.*', 'socialmedias.*', 'contact.*', 'pages.*') ? 'active' : '' }}">
                    <i data-feather="settings"></i>
                    <span> Site Setting </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('settings.*', 'socialmedias.*', 'contact.*', 'pages.*') ? 'show' : '' }}" id="sub-sitesetting">
                    <ul class="subpanel-nested-menu">
                      @can('setting-list')
                      <li><a href="{{ route('settings.index') }}"><i data-feather="file-plus"></i> General Setting</a></li>
                      @endcan
                      @can('social-list')
                      <li><a href="{{ route('socialmedias.index') }}"><i data-feather="file-plus"></i> Social Media</a></li>
                      @endcan
                      @can('contact-list')
                      <li><a href="{{ route('contact.index') }}"><i data-feather="file-plus"></i> Contact</a></li>
                      @endcan
                      @canany(['page-list', 'page-create', 'page-edit'])
                      <li><a href="{{ route('pages.index') }}"><i data-feather="file-plus"></i> Create Page</a></li>
                      @endcanany
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['api-manage', 'fraud-setting-list'])
                <li class="{{ request()->routeIs('paymentgeteway.*', 'manual-payment-gateway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.gemini_ai.*', 'admin.fraud.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-api" data-bs-toggle="collapse" class="{{ request()->routeIs('paymentgeteway.*', 'manual-payment-gateway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.gemini_ai.*', 'admin.fraud.*') ? 'active' : '' }}">
                    <i data-feather="save"></i>
                    <span> API Integration </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('paymentgeteway.*', 'manual-payment-gateway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.gemini_ai.*', 'admin.fraud.*') ? 'show' : '' }}" id="sub-api">
                    <ul class="subpanel-nested-menu">
                      @can('api-manage')
                      <li><a href="{{ route('paymentgeteway.manage') }}"><i data-feather="file-plus"></i> Payment Gateway</a></li>
                      <li><a href="{{ route('manual-payment-gateway.manage') }}"><i data-feather="credit-card"></i> Manual Payment</a></li>
                      <li><a href="{{ route('smsgeteway.manage') }}"><i data-feather="file-plus"></i> SMS Gateway</a></li>
                      <li><a href="{{ route('courierapi.manage') }}"><i data-feather="file-plus"></i> Courier API</a></li>
                      <li><a href="{{ route('admin.facebook_capi.edit') }}"><i data-feather="facebook"></i> Facebook CAPI</a></li>
                      <li><a href="{{ route('admin.gemini_ai.edit') }}"><i data-feather="cpu"></i> Gemini AI</a></li>
                      @endcan
                      @can('fraud-setting-list')
                      <li><a href="{{ route('admin.fraud.index') }}"><i data-feather="key"></i> Manage Fraud API</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @can('email-setting-list')
                <li class="{{ request()->routeIs('email_setting*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('email_setting') }}" class="{{ request()->routeIs('email_setting*') ? 'active' : '' }}">
                    <i data-feather="mail"></i>
                    <span> Email Settings </span>
                  </a>
                </li>
                @endcan

                @canany(['complaint-list', 'contact-list', 'newsletter-list'])
                <li class="{{ request()->routeIs('backEnd.complaints.*', 'admin.contact.messages*', 'admin.newsletter.subscribers*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-inquiries" data-bs-toggle="collapse" class="{{ request()->routeIs('backEnd.complaints.*', 'admin.contact.messages*', 'admin.newsletter.subscribers*') ? 'active' : '' }}">
                    <i data-feather="inbox"></i>
                    <span> Customer Inquiries </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('backEnd.complaints.*', 'admin.contact.messages*', 'admin.newsletter.subscribers*') ? 'show' : '' }}" id="sub-inquiries">
                    <ul class="subpanel-nested-menu">
                      @canany(['complaint-list', 'complaint-create', 'complaint-edit'])
                      <li><a href="{{ route('backEnd.complaints.index') }}"><i data-feather="alert-circle"></i> Complaints</a></li>
                      @endcanany
                      @can('contact-list')
                      <li><a href="{{ route('admin.contact.messages') }}"><i data-feather="mail"></i> Contact Messages</a></li>
                      @endcan
                      @can('newsletter-list')
                      <li><a href="{{ route('admin.newsletter.subscribers') }}"><i data-feather="mail"></i> Subscribers</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['seo-manage', 'sitemap-manage'])
                <li class="{{ request()->routeIs('admin.seo_settings.*', 'admin.sitemap.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-seo" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.seo_settings.*', 'admin.sitemap.*') ? 'active' : '' }}">
                    <i data-feather="globe"></i>
                    <span> SEO & Sitemap </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.seo_settings.*', 'admin.sitemap.*') ? 'show' : '' }}" id="sub-seo">
                    <ul class="subpanel-nested-menu">
                      @can('seo-manage')
                      <li><a href="{{ route('admin.seo_settings.index') }}"><i data-feather="globe"></i> SEO Settings</a></li>
                      @endcan
                      @can('sitemap-manage')
                      <li><a href="{{ route('admin.sitemap.index') }}"><i data-feather="map"></i> Sitemap Settings</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['api-manage', 'cache-clear', 'error-log-view'])
                <li class="{{ request()->routeIs('admin.cron.*', 'error-log.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-maintenance" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.cron.*', 'error-log.*') ? 'active' : '' }}">
                    <i data-feather="tool"></i>
                    <span> Maintenance </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.cron.*', 'error-log.*') ? 'show' : '' }}" id="sub-maintenance">
                    <ul class="subpanel-nested-menu">
                      @can('api-manage')
                      <li><a href="{{ route('admin.cron.index') }}"><i data-feather="clock"></i> Cron Job</a></li>
                      @endcan
                      @can('cache-clear')
                      <li>
                        <a href="{{ route('admin.clear.cache') }}" onclick="return confirm('Are you sure you want to clear all cache?')">
                          <i data-feather="refresh-cw"></i> Clear Cache
                        </a>
                      </li>
                      @endcan
                      @can('error-log-view')
                      <li><a href="{{ route('error-log.index') }}"><i data-feather="file-text"></i> Error Log</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany
              </ul>
            </div>

            {{-- 7. ANALYTICS & REPORTS PANE --}}
            <div class="section-pane {{ $activeSection === 'section-analytics' ? 'active' : '' }}" id="pane-section-analytics">
              <div class="pane-title">Analytics & Reports</div>
              <ul class="subpanel-menu">
                @canany(['pixel-manage'])
                <li class="{{ request()->routeIs('admin.ads_analytics.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-ads" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.ads_analytics.*') ? 'active' : '' }}">
                    <i data-feather="trending-up"></i>
                    <span> Live Ads Result </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.ads_analytics.*') ? 'show' : '' }}" id="sub-ads">
                    <ul class="subpanel-nested-menu">
                      <li><a href="{{ route('admin.ads_analytics.dashboard') }}"><i data-feather="layout"></i> Overview</a></li>
                      <li><a href="{{ route('admin.ads_analytics.facebook') }}"><i data-feather="facebook"></i> Facebook Ads</a></li>
                      <li><a href="{{ route('admin.ads_analytics.google') }}"><i data-feather="globe"></i> Google Ads</a></li>
                      <li><a href="{{ route('admin.ads_analytics.tiktok') }}"><i data-feather="video"></i> TikTok Ads</a></li>
                    </ul>
                  </div>
                </li>

                <li class="{{ request()->routeIs('tagmanagers.*', 'pixels.*', 'tiktok.pixels.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-pixels" data-bs-toggle="collapse" class="{{ request()->routeIs('tagmanagers.*', 'pixels.*', 'tiktok.pixels.*') ? 'active' : '' }}">
                    <i data-feather="save"></i>
                    <span> G. Pixel & GTM </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('tagmanagers.*', 'pixels.*', 'tiktok.pixels.*') ? 'show' : '' }}" id="sub-pixels">
                    <ul class="subpanel-nested-menu">
                      <li><a href="{{ route('tagmanagers.index') }}"><i data-feather="file-plus"></i> Tag Manager</a></li>
                      <li><a href="{{ route('pixels.index') }}"><i data-feather="file-plus"></i> Pixel Manage</a></li>
                      <li><a href="{{ route('tiktok.pixels.index') }}"><i data-feather="film"></i> TikTok Pixel</a></li>
                    </ul>
                  </div>
                </li>

                <li class="{{ request()->routeIs('admin.facebook_page.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.facebook_page.settings') }}" class="{{ request()->routeIs('admin.facebook_page.*') ? 'active' : '' }}">
                    <i data-feather="share-2"></i>
                    <span> Facebook Page Post </span>
                  </a>
                </li>
                @endcanany

                @canany(['report-view','order-report','purchase-report','expense-report','stock-report','profit-loss-report'])
                <li class="{{ request()->routeIs('admin.reports.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-reports" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i data-feather="pie-chart"></i>
                    <span> Reports </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}" id="sub-reports">
                    <ul class="subpanel-nested-menu">
                      @canany(['order-report','report-view'])
                      <li><a href="{{ route('admin.reports.orders') }}"><i data-feather="file-text"></i> Order Report</a></li>
                      @endcanany
                      @canany(['purchase-report','report-view'])
                      <li><a href="{{ route('admin.reports.purchases') }}"><i data-feather="shopping-bag"></i> Purchase Report</a></li>
                      @endcanany
                      @canany(['expense-report','report-view'])
                      <li><a href="{{ route('admin.reports.expenses') }}"><i data-feather="trending-down"></i> Expense Report</a></li>
                      @endcanany
                      @canany(['stock-report','report-view'])
                      <li><a href="{{ route('admin.reports.stock') }}"><i data-feather="archive"></i> Stock Report</a></li>
                      @endcanany
                      @canany(['profit-loss-report','report-view'])
                      <li><a href="{{ route('admin.reports.profit_loss') }}"><i data-feather="activity"></i> Profit & Loss</a></li>
                      @endcanany
                    </ul>
                  </div>
                </li>
                @endcanany
              </ul>
            </div>

            {{-- 8. MARKETING & PROMOTIONS PANE --}}
            <div class="section-pane {{ $activeSection === 'section-marketing' ? 'active' : '' }}" id="pane-section-marketing">
              <div class="pane-title">Marketing & Promotions</div>
              <ul class="subpanel-menu">
                @canany(['campaign-list', 'campaign-create'])
                <li class="{{ request()->routeIs('campaign.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-campaign" data-bs-toggle="collapse" class="{{ request()->routeIs('campaign.*') ? 'active' : '' }}">
                    <i data-feather="airplay"></i>
                    <span> Landing Page </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('campaign.*') ? 'show' : '' }}" id="sub-campaign">
                    <ul class="subpanel-nested-menu">
                      @can('campaign-list')
                      <li><a href="{{ route('campaign.index') }}"><i data-feather="file-plus"></i> All Campaigns</a></li>
                      @endcan
                      @can('campaign-create')
                      <li><a href="{{ route('campaign.create') }}"><i data-feather="file-plus"></i> Create Campaign</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['coupon-list', 'coupon-create', 'coupon-edit', 'coupon-delete'])
                <li class="{{ request()->routeIs('admin.coupons.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-coupons" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i data-feather="gift"></i>
                    <span> Coupons </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.coupons.*') ? 'show' : '' }}" id="sub-coupons">
                    <ul class="subpanel-nested-menu">
                      @can('coupon-list')
                      <li><a href="{{ route('admin.coupons.index') }}"><i data-feather="list"></i> All Coupons</a></li>
                      @endcan
                      @can('coupon-create')
                      <li><a href="{{ route('admin.coupons.create') }}"><i data-feather="plus-circle"></i> Add New</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['banner-list'])
                <li class="{{ request()->routeIs('banners.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('banners.index') }}" class="{{ request()->routeIs('banners.*') ? 'active' : '' }}">
                    <i data-feather="image"></i>
                    <span> Banner & Sliders </span>
                  </a>
                </li>
                @endcanany

                @canany(['popup-list', 'popup-manage', 'setting-list'])
                <li class="{{ request()->routeIs('admin.popup.*', 'admin.sale-notification.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-popups" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.popup.*', 'admin.sale-notification.*') ? 'active' : '' }}">
                    <i data-feather="bell"></i>
                    <span> Popups & Alerts </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.popup.*', 'admin.sale-notification.*') ? 'show' : '' }}" id="sub-popups">
                    <ul class="subpanel-nested-menu">
                      @canany(['popup-list', 'popup-manage'])
                      <li><a href="{{ route('admin.popup.index') }}"><i data-feather="message-square"></i> Popup Offer</a></li>
                      @endcanany
                      @can('setting-list')
                      <li><a href="{{ route('admin.sale-notification.index') }}"><i data-feather="bell"></i> Sales Notification</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @can('review-list')
                <li class="{{ request()->routeIs('reviews.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-reviews" data-bs-toggle="collapse" class="{{ request()->routeIs('reviews.*') ? 'active' : '' }}">
                    <i data-feather="star"></i>
                    <span> Reviews </span>
                    @if($pending_reviews > 0)
                      <span class="badge bg-warning rounded-pill float-end">{{ $pending_reviews }}</span>
                    @else
                      <span class="menu-arrow"></span>
                    @endif
                  </a>
                  <div class="collapse {{ request()->routeIs('reviews.*') ? 'show' : '' }}" id="sub-reviews">
                    <ul class="subpanel-nested-menu">
                      <li><a href="{{ route('reviews.pending') }}"><i data-feather="file-plus"></i> Pending ({{ $pending_reviews }})</a></li>
                      <li><a href="{{ route('reviews.index') }}"><i data-feather="file-plus"></i> All Reviews</a></li>
                      @can('review-create')
                      <li><a href="{{ route('reviews.pending') }}"><i data-feather="file-plus"></i> Create Review</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcan

                @canany(['blog-list', 'blog-create', 'blog-edit', 'blog-delete'])
                <li class="{{ request()->routeIs('admin.blog.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-blog" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                    <i data-feather="edit"></i>
                    <span> Blog </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.blog.*') ? 'show' : '' }}" id="sub-blog">
                    <ul class="subpanel-nested-menu">
                      @can('blog-list')
                      <li><a href="{{ route('admin.blog.index') }}"><i data-feather="list"></i> All Blogs</a></li>
                      @endcan
                      @can('blog-create')
                      <li><a href="{{ route('admin.blog.create') }}"><i data-feather="plus-circle"></i> Add New Blog</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @can('sms-send')
                <li class="{{ request()->routeIs('admin.sms.custom.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.sms.custom.page') }}" class="{{ request()->routeIs('admin.sms.custom.*') ? 'active' : '' }}">
                    <i data-feather="send"></i>
                    <span> Send Custom SMS </span>
                  </a>
                </li>
                @endcan
              </ul>
            </div>

          </div>

          <!-- Subpanel Footer -->
          <div class="subpanel-footer">
            <a href="{{ route('home') }}" target="_blank" class="footer-website-link">
              <i data-feather="globe"></i>
              <span>View Website</span>
              <i data-feather="arrow-up-right" class="ms-auto" style="width:13px;height:13px;"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- Mobile Backdrop -->
      <div class="two-column-backdrop" id="two-column-backdrop"></div>
      <!-- Left Sidebar End -->

      <div class="content-page">
        <div class="content">
          @yield('content')
        </div>
        <!-- content -->

        <!-- end Footer -->
      </div>
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    <div class="right-bar">
      <div data-simplebar class="h-100">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-bordered nav-justified" role="tablist">
          <li class="nav-item">
            <a class="nav-link py-2" data-bs-toggle="tab" href="#chat-tab" role="tab">
              <i class="mdi mdi-message-text d-block font-22 my-1"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-2" data-bs-toggle="tab" href="#tasks-tab" role="tab">
              <i class="mdi mdi-format-list-checkbox d-block font-22 my-1"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-2 active" data-bs-toggle="tab" href="#settings-tab" role="tab">
              <i class="mdi mdi-cog-outline d-block font-22 my-1"></i>
            </a>
          </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content pt-0">
          <div class="tab-pane" id="chat-tab" role="tabpanel">
            <form class="search-bar p-3">
              <div class="position-relative">
                <input type="text" class="form-control" placeholder="Search..." />
                <span class="mdi mdi-magnify"></span>
              </div>
            </form>
          </div>

          <div class="tab-pane" id="tasks-tab" role="tabpanel">
            <h6 class="fw-medium p-3 m-0 text-uppercase">Working Tasks</h6>
          </div>
          <div class="tab-pane active" id="settings-tab" role="tabpanel">
            <h6 class="fw-medium px-3 m-0 py-2 font-13 text-uppercase bg-light">
              <span class="d-block py-1">Theme Settings</span>
            </h6>

            <div class="p-3">
              <div class="alert alert-warning" role="alert"><strong>Customize </strong> the overall color scheme, sidebar menu, etc.</div>

              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Color Scheme</h6>
              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="layout-color" value="light" id="light-mode-check" checked />
                <label class="form-check-label" for="light-mode-check">Light Mode</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="layout-color" value="dark" id="dark-mode-check" />
                <label class="form-check-label" for="dark-mode-check">Dark Mode</label>
              </div>

              <!-- Width -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Width</h6>
              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="layout-width" value="fluid" id="fluid-check" checked />
                <label class="form-check-label" for="fluid-check">Fluid</label>
              </div>
              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="layout-width" value="boxed" id="boxed-check" />
                <label class="form-check-label" for="boxed-check">Boxed</label>
              </div>

              <!-- Menu positions -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Menus (Leftsidebar and Topbar) Positon</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="menu-position" value="fixed" id="fixed-check" checked />
                <label class="form-check-label" for="fixed-check">Fixed</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="menu-position" value="scrollable" id="scrollable-check" />
                <label class="form-check-label" for="scrollable-check">Scrollable</label>
              </div>

              <!-- Left Sidebar-->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Left Sidebar Color</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-color" value="light" id="light-check" />
                <label class="form-check-label" for="light-check">Light</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-color" value="dark" id="dark-check" checked />
                <label class="form-check-label" for="dark-check">Dark</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-color" value="brand" id="brand-check" />
                <label class="form-check-label" for="brand-check">Brand</label>
              </div>

              <div class="form-check form-switch mb-3">
                <input type="checkbox" class="form-check-input" name="leftbar-color" value="gradient" id="gradient-check" />
                <label class="form-check-label" for="gradient-check">Gradient</label>
              </div>

              <!-- size -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Left Sidebar Size</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-size" value="default" id="default-size-check" checked />
                <label class="form-check-label" for="default-size-check">Default</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-size" value="condensed" id="condensed-check" />
                <label class="form-check-label" for="condensed-check">Condensed <small>(Extra Small size)</small></label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-size" value="compact" id="compact-check" />
                <label class="form-check-label" for="compact-check">Compact <small>(Small size)</small></label>
              </div>

              <!-- User info -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Sidebar User Info</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="sidebar-user" value="fixed" id="sidebaruser-check" />
                <label class="form-check-label" for="sidebaruser-check">Enable</label>
              </div>

              <!-- Topbar -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Topbar</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="topbar-color" value="dark" id="darktopbar-check" checked />
                <label class="form-check-label" for="darktopbar-check">Dark</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="topbar-color" value="light" id="lighttopbar-check" />
                <label class="form-check-label" for="lighttopbar-check">Light</label>
              </div>

              <div class="d-grid mt-4">
                <button class="btn btn-primary" id="resetBtn">Reset to Default</button>
                <a href="https://1.envato.market/uboldadmin" class="btn btn-danger mt-3" target="_blank"><i class="mdi mdi-basket me-1"></i> Purchase Now</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- end slimscroll-menu-->
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="{{asset('public/backEnd/')}}/assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="{{asset('public/backEnd/')}}/assets/js/app.min.js"></script>
    
    {{-- vendor.min এ Feather থাকলেও সম্পূর্ণ আইকন সেট / টাইমিং ভিন্ন হতে পারে; CDN সংস্করণ লোড করে window.feather নিশ্চিত করা হয়।
         MutationObserver ব্যবহার করবেন না (সেখান থেকেই ট্যাব লোডিং লাগছিল); শুধু নির্দিষ্ট ইভেন্টে replace। --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        (function () {
            function initFeather() {
                if (typeof feather !== 'undefined' && typeof feather.replace === 'function') {
                    try {
                        feather.replace();
                    } catch (e) {
                        console.warn('Feather replace error:', e);
                    }
                }
            }
            function scheduleFeatherPasses() {
                initFeather();
                setTimeout(initFeather, 80);
                setTimeout(initFeather, 250);
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', scheduleFeatherPasses);
            } else {
                scheduleFeatherPasses();
            }
            window.addEventListener('load', function () {
                initFeather();
            });
            if (typeof jQuery !== 'undefined') {
                jQuery(function () {
                    scheduleFeatherPasses();
                });
                jQuery(document).on(
                    'shown.bs.collapse hidden.bs.collapse',
                    '[data-bs-toggle="collapse"]',
                    function () {
                        setTimeout(initFeather, 50);
                    }
                );
            }
        })();
    </script>
    <!-- Two-Column Sidebar Controller JS -->
    <script>
        (function () {
            function initTwoColumnSidebar() {
                var sidebar = document.getElementById('two-column-sidebar');
                if (!sidebar) return;

                var railItems = document.querySelectorAll('.sidebar-icon-rail .rail-item[data-section]');
                var panes = document.querySelectorAll('.sidebar-subpanel .section-pane');
                var collapseBtns = document.querySelectorAll('#sidebar-collapse-btn, #sidebar-toggle-btn, .button-menu-mobile');
                var backdrop = document.getElementById('two-column-backdrop');
                var jumpSearch = document.getElementById('sidebar-jump-search');
                var clearSearch = document.getElementById('clear-jump-search');

                // Restore desktop collapsed state
                if (window.innerWidth >= 992) {
                    var isCollapsed = localStorage.getItem('two_col_sidebar_collapsed') === '1';
                    if (isCollapsed) {
                        document.body.classList.add('sidebar-collapsed');
                    }
                }

                // Rail item click handler
                railItems.forEach(function (item) {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        var targetSection = this.getAttribute('data-section');
                        if (!targetSection) return;

                        // If collapsed on desktop, clicking a section expands it
                        if (document.body.classList.contains('sidebar-collapsed')) {
                            document.body.classList.remove('sidebar-collapsed');
                            localStorage.setItem('two_col_sidebar_collapsed', '0');
                        }

                        // Update rail item active state
                        railItems.forEach(function (r) { r.classList.remove('active'); });
                        this.classList.add('active');

                        // Switch active pane
                        panes.forEach(function (pane) {
                            if (pane.id === 'pane-' + targetSection) {
                                pane.classList.add('active');
                            } else {
                                pane.classList.remove('active');
                            }
                        });

                        // Clear search filter when changing section
                        if (jumpSearch && jumpSearch.value) {
                            jumpSearch.value = '';
                            if (clearSearch) clearSearch.style.display = 'none';
                            resetSearchFilter();
                        }

                        if (typeof feather !== 'undefined' && typeof feather.replace === 'function') {
                            feather.replace();
                        }
                    });
                });

                // Toggle sidebar (Collapse/Expand on desktop, Drawer on mobile)
                collapseBtns.forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (window.innerWidth >= 992) {
                            var willCollapse = !document.body.classList.contains('sidebar-collapsed');
                            document.body.classList.toggle('sidebar-collapsed', willCollapse);
                            localStorage.setItem('two_col_sidebar_collapsed', willCollapse ? '1' : '0');
                        } else {
                            var willOpenMobile = !document.body.classList.contains('sidebar-mobile-open');
                            document.body.classList.toggle('sidebar-mobile-open', willOpenMobile);
                        }
                    });
                });

                // Close mobile drawer on backdrop click
                if (backdrop) {
                    backdrop.addEventListener('click', function () {
                        document.body.classList.remove('sidebar-mobile-open');
                    });
                }

                // Close mobile drawer on direct link click
                document.querySelectorAll('.sidebar-subpanel a:not([data-bs-toggle="collapse"])').forEach(function (link) {
                    link.addEventListener('click', function () {
                        if (window.innerWidth < 992) {
                            document.body.classList.remove('sidebar-mobile-open');
                        }
                    });
                });

                // Jump to... Filter
                function resetSearchFilter() {
                    var activeRail = document.querySelector('.sidebar-icon-rail .rail-item.active');
                    var activeSec = activeRail ? activeRail.getAttribute('data-section') : 'section-main';
                    panes.forEach(function (p) {
                        p.classList.toggle('active', p.id === 'pane-' + activeSec);
                        p.querySelectorAll('li').forEach(function (li) { li.style.display = ''; });
                    });
                }

                if (jumpSearch) {
                    jumpSearch.addEventListener('input', function () {
                        var query = this.value.trim().toLowerCase();
                        if (clearSearch) clearSearch.style.display = query.length ? 'block' : 'none';

                        if (!query) {
                            resetSearchFilter();
                            return;
                        }

                        panes.forEach(function (pane) {
                            var hasMatch = false;
                            var listItems = pane.querySelectorAll('.subpanel-menu > li');
                            listItems.forEach(function (li) {
                                var text = li.innerText.toLowerCase();
                                if (text.indexOf(query) !== -1) {
                                    li.style.display = '';
                                    hasMatch = true;
                                    var collapseEl = li.querySelector('.collapse');
                                    if (collapseEl) {
                                        collapseEl.classList.add('show');
                                    }
                                } else {
                                    li.style.display = 'none';
                                }
                            });

                            if (hasMatch) {
                                pane.classList.add('active');
                            } else {
                                pane.classList.remove('active');
                            }
                        });
                    });

                    if (clearSearch) {
                        clearSearch.addEventListener('click', function () {
                            jumpSearch.value = '';
                            this.style.display = 'none';
                            resetSearchFilter();
                            jumpSearch.focus();
                        });
                    }
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTwoColumnSidebar);
            } else {
                initTwoColumnSidebar();
            }
        })();
    </script>
    <script src="{{asset('public/backEnd/')}}/assets/js/toastr.min.js"></script>
    <script src="{{asset('public/backEnd/')}}/assets/js/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {!! Toastr::message() !!}
	<script>
@if(Session::has('success'))
    toastr.success(@json(Session::get('success')));
@endif
@if(Session::has('error') && !Session::has('demo_mode_blocked'))
    toastr.error(@json(Session::get('error')));
@endif
@if(Session::has('info'))
    toastr.info(@json(Session::get('info')));
@endif
@if(Session::has('warning'))
    toastr.warning(@json(Session::get('warning')));
@endif
@if(Session::has('demo_mode_blocked'))
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: '<strong style="font-size:1.4rem;color:#2c3e50;">ডেমো মুড সক্রিয়</strong>',
            html: '<div style="text-align:center;padding:10px 0;"><div style="width:70px;height:70px;margin:0 auto 15px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fe-eye" style="font-size:32px;color:#fff;"></i></div><p style="font-size:1rem;color:#5a6c7d;margin-bottom:8px;line-height:1.6;">অ্যাডমিন প্যানেল থেকে কোন ডাটা পরিবর্তন বা সংযোজন করা যাবে না।</p><p style="font-size:0.9rem;color:#95a5a6;margin:0;">কাস্টমার সাইটে অর্ডার, ট্রাকিং ও অন্যান্য সেবা স্বাভাবিকভাবে কাজ করবে।</p></div>',
            confirmButtonText: 'বুঝেছি',
            confirmButtonColor: '#667eea',
            customClass: { popup: 'demo-mode-popup', confirmButton: 'demo-mode-btn' },
            width: '420px',
            backdrop: 'rgba(0,0,0,0.5)',
        });
    } else {
        toastr.info("ডেমো মুড চালু আছে। অ্যাডমিন প্যানেল থেকে কোন পরিবর্তন করা যাবে না।");
    }
@endif
</script>
    <style>
    .demo-mode-popup { border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
    .demo-mode-btn { padding: 10px 28px; font-weight: 600; border-radius: 8px; }
    </style>
    <script>
    function showDemoModeAlert(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: '<strong style="font-size:1.4rem;color:#2c3e50;">ডেমো মুড সক্রিয়</strong>',
                html: '<div style="text-align:center;padding:10px 0;"><div style="width:70px;height:70px;margin:0 auto 15px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fe-eye" style="font-size:32px;color:#fff;"></i></div><p style="font-size:1rem;color:#5a6c7d;margin-bottom:8px;line-height:1.6;">' + (msg || 'অ্যাডমিন প্যানেল থেকে কোন ডাটা পরিবর্তন বা সংযোজন করা যাবে না।') + '</p><p style="font-size:0.9rem;color:#95a5a6;margin:0;">কাস্টমার সাইটে অর্ডার, ট্রাকিং ও অন্যান্য সেবা স্বাভাবিকভাবে কাজ করবে।</p></div>',
                confirmButtonText: 'বুঝেছি',
                confirmButtonColor: '#667eea',
                customClass: { popup: 'demo-mode-popup', confirmButton: 'demo-mode-btn' },
                width: '420px',
                backdrop: 'rgba(0,0,0,0.5)',
            });
        }
    }
    $(document).ajaxComplete(function(event, xhr, settings) {
        if (xhr.status === 403) {
            try {
                var data = typeof xhr.responseJSON !== 'undefined' ? xhr.responseJSON : JSON.parse(xhr.responseText || '{}');
                if (data.demo_mode && typeof Swal !== 'undefined') {
                    showDemoModeAlert(data.message || '');
                }
            } catch (e) {}
        }
    });
    </script>
    <script type="text/javascript">
      $(document).on('click', '.delete-confirm', function (event) {
        event.preventDefault();
        var form = $(this).closest("form");
        @if(isset($demoMode) && $demoMode)
        showDemoModeAlert();
        return;
        @endif
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
          }).then(function(result) {
            if (result.isConfirmed) { form.submit(); }
          });
        } else {
          if (confirm('Are you sure you want to delete this record?')) { form.submit(); }
        }
      });
      $(document).on('click', '.change-confirm', function (event) {
        event.preventDefault();
        var form = $(this).closest("form");
        @if(isset($demoMode) && $demoMode)
        showDemoModeAlert();
        return;
        @endif
        swal({
          title: `Are you sure you want to change this record?`,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            form.submit();
          }
        });
      });
      @if(isset($demoMode) && $demoMode)
      $(document).on('submit', 'form', function(e) {
        var action = (this.action || '').toLowerCase();
        if (action.indexOf('logout') !== -1) return;
        var method = ($(this).find('input[name="_method"]').val() || $(this).attr('method') || 'get').toLowerCase();
        if (method === 'get') return;
        e.preventDefault();
        showDemoModeAlert();
        return false;
      });
      document.addEventListener('click', function(e) {
        var el = e.target.closest ? e.target.closest('a[href*="destroy"], a[href*="bulk_destroy"], a[href*="/delete"], a.order_delete') : null;
        if (el && el.href && el.href.indexOf('#') !== 0) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          showDemoModeAlert();
          return false;
        }
      }, true);
      @endif
    </script>
    <!--patho courier-->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.pathaocity').change(function() {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('admin/pathao-city') }}?city_id=" + id,
                        success: function(res) {
                            if (res && res.data && res.data.data) {
                                $(".pathaozone").empty();
                                $(".pathaozone").append('<option value="">Select..</option>');
                                $.each(res.data.data, function(index, zone) {
                                    $(".pathaozone").append('<option value="' + zone.zone_id + '">' + zone.zone_name + '</option>');
                                    $('.pathaozone').trigger("chosen:updated");
                                });
                            } else {
                                 $(".pathaoarea").empty();
                                $(".pathaozone").empty();
                            }
                        }
                    });
                } else {
                     $(".pathaoarea").empty();
                    $(".pathaozone").empty();
                }
            });
        });
    </script>
    <script type="text/javascript"> 
        $(document).ready(function() {
            $('.pathaozone').change(function() {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('admin/pathao-zone') }}?zone_id=" + id,
                        success: function(res) {
                            if (res && res.data && res.data.data) {
                                $(".pathaoarea").empty();
                                $(".pathaoarea").append('<option value="">Select..</option>');
                                $.each(res.data.data, function(index, area) {
                                    $(".pathaoarea").append('<option value="' + area.area_id + '">' + area.area_name + '</option>');
                                    $('.pathaoarea').trigger("chosen:updated");
                                });
                            } else {
                                $(".pathaoarea").empty();
                            }
                        }
                    });
                } else {
                    $(".pathaoarea").empty();
                }
            });
        });
    </script>
    {{-- Admin-wide: সাধারণ চেকবক্স → kill switch / form-switch (টেবিল বাল্ক সেলেক্ট ছাড়া) --}}
    <style>
        .content-page .form-switch .form-check-input[type="checkbox"] {
            cursor: pointer;
            margin-top: 0.2em;
        }
        .content-page .card-body .form-switch + .text-muted,
        .content-page .card-body label.form-check-label ~ .text-muted {
            margin-top: -0.15rem;
        }
    </style>
    <script>
        (function () {
            function applyAdminKillSwitches(root) {
                var container = root && root.nodeType === 1 ? root : document;
                if (!container.querySelector || !document.querySelector('.content-page')) return;
                var nodes = container.querySelectorAll('.content-page input[type="checkbox"]');
                for (var i = 0; i < nodes.length; i++) {
                    var cb = nodes[i];
                    if (cb.getAttribute('data-no-bs-switch') === 'true' || cb.classList.contains('no-bs-switch')) continue;
                    if (cb.closest && (cb.closest('#settings-tab') || cb.closest('.right-bar'))) continue;
                    if (cb.closest && cb.closest('table')) continue;
                    if (cb.closest && cb.closest('label.switch')) continue;
                    if (cb.closest && cb.closest('.switch')) continue;
                    if (cb.matches('.toggle-checkbox')) continue;

                    var wrap = cb.closest('.form-check');
                    if (wrap && !wrap.classList.contains('form-switch')) {
                        wrap.classList.add('form-switch');
                        cb.setAttribute('role', 'switch');
                    }
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () { applyAdminKillSwitches(document); });
            } else {
                applyAdminKillSwitches(document);
            }
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ajaxComplete(function () { applyAdminKillSwitches(document); });
            }
            window.adminApplyKillSwitches = applyAdminKillSwitches;
        })();
    </script>
    @auth('admin')
        @include('backEnd.layouts.partials.admin_order_live_notify')
        @include('backEnd.layouts.partials.gemini_admin_chatbot')
    @endauth
    @yield('script')
  </body>
</html>
