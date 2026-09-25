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
    <!-- flatpickr css -->
    <link href="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
    @php
        $brandPrimary = (isset($generalsetting) && !empty($generalsetting->primary_color)) ? $generalsetting->primary_color : '#10b981';
        $brandSecondary = (isset($generalsetting) && !empty($generalsetting->secodery_color)) ? $generalsetting->secodery_color : '#3b82f6';
    @endphp
    <style>
      :root {
        --brand-primary: {{ $brandPrimary }};
        --brand-secondary: {{ $brandSecondary }};
        --rail-active-bg: {{ $brandPrimary }}22;
        --rail-active-color: {{ $brandPrimary }};
        --subpanel-active-bg: {{ $brandPrimary }};
      }
      .rail-item.active {
        background: {{ $brandPrimary }}22 !important;
        color: {{ $brandPrimary }} !important;
        box-shadow: 0 0 15px {{ $brandPrimary }}44 !important;
      }
      .rail-item.active svg {
        stroke: {{ $brandPrimary }} !important;
      }
      .subpanel-menu li.menuitem-active > a,
      .subpanel-menu li a.active {
        background: {{ $brandPrimary }}1f !important;
        color: {{ $brandPrimary }} !important;
      }
      .subpanel-menu li.menuitem-active > a svg,
      .subpanel-menu li a.active svg {
        stroke: {{ $brandPrimary }} !important;
      }
      .subpanel-nested-menu li a.active {
        color: {{ $brandPrimary }} !important;
        font-weight: 700 !important;
        background: {{ $brandPrimary }}18 !important;
        border-radius: 6px !important;
      }
      .subpanel-nested-menu li a.active svg {
        stroke: {{ $brandPrimary }} !important;
      }

      /* 🔔 Modern Notification & Topbar Dropdown Styles (Header-attached & Fully Responsive) */
      .topbar-dropdown {
        position: relative;
      }
      .topbar-dropdown .noti-dropdown-custom {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        margin: 0 !important;
        transform: none !important;
        width: 380px !important;
        max-width: calc(100vw - 20px) !important;
        max-height: calc(100vh - 80px) !important;
        border-top-left-radius: 0 !important;
        border-top-right-radius: 0 !important;
        border-bottom-left-radius: 16px !important;
        border-bottom-right-radius: 16px !important;
        box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.22), 0 0 0 1px rgba(0, 0, 0, 0.06) !important;
        border: none !important;
        display: none;
        flex-direction: column !important;
        overflow: hidden !important;
        z-index: 1055 !important;
        animation: notiFadeIn 0.18s ease-out;
      }
      .topbar-dropdown .noti-dropdown-custom.show {
        display: flex !important;
      }
      @keyframes notiFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .noti-dropdown-custom .noti-header-card {
        flex-shrink: 0;
      }
      .noti-dropdown-custom .noti-scroll-custom {
        flex: 1 1 auto;
        max-height: calc(100vh - 210px) !important;
        overflow-y: auto !important;
        overscroll-behavior: contain;
      }
      .noti-dropdown-custom .noti-footer-bar {
        flex-shrink: 0;
      }
      .noti-item-row {
        transition: background-color 0.15s ease, padding-left 0.15s ease;
        background-color: #ffffff;
      }
      .noti-item-row:hover {
        background-color: #f8fafc;
        padding-left: 18px !important;
      }
      .noti-item-row:last-child {
        border-bottom: none !important;
      }
      .noti-scroll-custom::-webkit-scrollbar {
        width: 5px;
      }
      .noti-scroll-custom::-webkit-scrollbar-track {
        background: #f1f5f9;
      }
      .noti-scroll-custom::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
      }
      .noti-pulse-badge {
        animation: notiBadgePulse 2s infinite;
      }
      @keyframes notiBadgePulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.18); box-shadow: 0 0 10px rgba(239, 68, 68, 0.7); }
        100% { transform: scale(1); }
      }

      /* Profile Dropdown Flush with Header */
      .topbar-dropdown .profile-dropdown-custom {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        margin: 0 !important;
        transform: none !important;
        border-top-left-radius: 0 !important;
        border-top-right-radius: 0 !important;
        border-bottom-left-radius: 16px !important;
        border-bottom-right-radius: 16px !important;
        box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.22), 0 0 0 1px rgba(0, 0, 0, 0.06) !important;
      }

      /* Mobile & Small Screen Responsive */
      @media (max-width: 576px) {
        .topbar-dropdown .noti-dropdown-custom {
          position: fixed !important;
          top: 70px !important;
          left: 10px !important;
          right: 10px !important;
          width: auto !important;
          max-width: calc(100vw - 20px) !important;
          max-height: calc(100vh - 85px) !important;
          border-radius: 0 0 16px 16px !important;
        }
        .noti-dropdown-custom .noti-scroll-custom {
          max-height: calc(100vh - 200px) !important;
        }
      }

      /* ═══ Admin Header Mobile Fix — Logo বামে, Icon ডানে ═══ */
      @media (max-width: 768px) {
        .navbar-custom .container-fluid {
          display: flex !important;
          align-items: center !important;
          justify-content: space-between !important;
          flex-wrap: nowrap !important;
          padding-left: 10px !important;
          padding-right: 10px !important;
        }
        .navbar-custom .logo-box {
          order: 1 !important;
          flex-shrink: 0 !important;
          float: none !important;
          margin: 0 !important;
        }
        .navbar-custom .topnav-menu.topnav-menu-left {
          order: 2 !important;
          float: none !important;
          margin: 0 !important;
          padding: 0 !important;
        }
        .navbar-custom .topnav-menu.float-end {
          order: 3 !important;
          float: none !important;
          margin-left: auto !important;
          display: flex !important;
          align-items: center !important;
          padding: 0 !important;
        }
        .navbar-custom .logo-box img {
          height: 36px !important;
          max-width: 120px !important;
          object-fit: contain !important;
        }
        .navbar-custom .topbar-dropdown .noti-dropdown-custom,
        .navbar-custom .topbar-dropdown .profile-dropdown-custom {
          position: fixed !important;
          top: 62px !important;
        }
      }
      @media (max-width: 480px) {
        .navbar-custom .container-fluid {
          padding-left: 8px !important;
          padding-right: 6px !important;
        }
        .navbar-custom .logo-box img {
          height: 30px !important;
          max-width: 95px !important;
        }
      }
    </style>
    <!-- Page Level CSS -->
    @yield('css')

    <script>
      // Anti-flash Dark Mode Initialization
      (function() {
        var savedTheme = localStorage.getItem('admin_theme');
        var dbDark = {{ (Auth::guard('admin')->user()->dark_mode ?? 0) ? '1' : '0' }};
        var isDark = savedTheme === 'dark' || (savedTheme === null && dbDark === 1);
        if (isDark) {
          document.documentElement.setAttribute('data-theme', 'dark');
          document.documentElement.classList.add('dark-mode');
        } else {
          document.documentElement.setAttribute('data-theme', 'light');
          document.documentElement.classList.remove('dark-mode');
        }
        document.addEventListener('DOMContentLoaded', function() {
          if (isDark) {
            document.body.setAttribute('data-theme', 'dark');
            document.body.classList.add('dark-mode');
          } else {
            document.body.setAttribute('data-theme', 'light');
            document.body.classList.remove('dark-mode');
          }
        });
      })();
    </script>
    <style>
      /* ═══════════════════════════════════════════════════════════════
         🌙 COMPREHENSIVE HIGH-CONTRAST DARK MODE ENGINE (UNIVERSAL)
         ═══════════════════════════════════════════════════════════════ */
      html[data-theme="dark"],
      body[data-theme="dark"],
      html.dark-mode,
      body.dark-mode {
        --bg-main: #0b1329;
        --bg-card: #131d38;
        --bg-card-header: #182343;
        --bg-card-hover: #1a274c;
        --bg-input: #0f172a;
        --border-color: #1e2d52;
        --border-light: #2d3f6d;
        --text-primary: #f8fafc;
        --text-secondary: #94a3b8;
        --text-white: #ffffff;
        --link-color: #60a5fa;
        --link-hover: #93c5fd;
        background-color: #0b1329 !important;
        color: #f8fafc !important;
      }

      [data-theme="dark"] body,
      body.dark-mode,
      [data-theme="dark"] #wrapper,
      body.dark-mode #wrapper,
      [data-theme="dark"] .content-page,
      body.dark-mode .content-page,
      [data-theme="dark"] .content,
      body.dark-mode .content,
      [data-theme="dark"] .order-index-shell,
      body.dark-mode .order-index-shell,
      [data-theme="dark"] .db-wrap,
      body.dark-mode .db-wrap {
        background: #0b1329 !important;
        background-color: #0b1329 !important;
        color: #f8fafc !important;
      }

      /* Topbar & Navbar */
      [data-theme="dark"] .navbar-custom,
      body.dark-mode .navbar-custom {
        background-color: #131d38 !important;
        border-bottom: 1px solid #1e2d52 !important;
      }
      [data-theme="dark"] .navbar-custom .topnav-menu .nav-link,
      body.dark-mode .navbar-custom .topnav-menu .nav-link {
        color: #cbd5e1 !important;
      }

      /* Sidebars */
      [data-theme="dark"] .left-side-menu,
      body.dark-mode .left-side-menu,
      [data-theme="dark"] .two-column-sidebar,
      body.dark-mode .two-column-sidebar,
      [data-theme="dark"] .sidebar-icon-rail,
      body.dark-mode .sidebar-icon-rail,
      [data-theme="dark"] .sidebar-subpanel,
      body.dark-mode .sidebar-subpanel,
      [data-theme="dark"] .subpanel-footer,
      body.dark-mode .subpanel-footer {
        background-color: #0f172a !important;
        border-color: #1e2d52 !important;
      }
      [data-theme="dark"] .subpanel-title,
      body.dark-mode .subpanel-title,
      [data-theme="dark"] .pane-title,
      body.dark-mode .pane-title {
        color: #cbd5e1 !important;
        border-color: #1e2d52 !important;
      }
      [data-theme="dark"] .subpanel-menu > li > a,
      body.dark-mode .subpanel-menu > li > a,
      [data-theme="dark"] .subpanel-nested-menu li a,
      body.dark-mode .subpanel-nested-menu li a {
        color: #94a3b8 !important;
      }
      [data-theme="dark"] .subpanel-nested-menu li a:hover,
      body.dark-mode .subpanel-nested-menu li a:hover,
      [data-theme="dark"] .subpanel-nested-menu li a.active,
      body.dark-mode .subpanel-nested-menu li a.active {
        color: #60a5fa !important;
      }

      /* ── Cards, Containers & Panels across ALL pages ── */
      [data-theme="dark"] .card,
      body.dark-mode .card,
      [data-theme="dark"] .card-modern,
      body.dark-mode .card-modern,
      [data-theme="dark"] .card-custom,
      body.dark-mode .card-custom,
      [data-theme="dark"] .pro-card,
      body.dark-mode .pro-card,
      [data-theme="dark"] .studio-card,
      body.dark-mode .studio-card,
      [data-theme="dark"] .oi-card,
      body.dark-mode .oi-card,
      [data-theme="dark"] .bg-white,
      body.dark-mode .bg-white,
      [data-theme="dark"] .bg-light,
      body.dark-mode .bg-light,
      [data-theme="dark"] .order-table-card,
      body.dark-mode .order-table-card,
      [data-theme="dark"] .modern-card,
      body.dark-mode .modern-card,
      [data-theme="dark"] .card-box,
      body.dark-mode .card-box,
      [data-theme="dark"] .widget-flat,
      body.dark-mode .widget-flat {
        background: #131d38 !important;
        background-color: #131d38 !important;
        border: 1px solid #1e2d52 !important;
        color: #f8fafc !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25) !important;
      }

      [data-theme="dark"] .card-header,
      body.dark-mode .card-header,
      [data-theme="dark"] .card-header-modern,
      body.dark-mode .card-header-modern,
      [data-theme="dark"] .oi-card-head,
      body.dark-mode .oi-card-head,
      [data-theme="dark"] .modal-header,
      body.dark-mode .modal-header,
      [data-theme="dark"] .modal-footer,
      body.dark-mode .modal-footer {
        background: #182343 !important;
        background-color: #182343 !important;
        border-color: #1e2d52 !important;
        color: #ffffff !important;
      }

      [data-theme="dark"] .oi-card-body,
      body.dark-mode .oi-card-body,
      [data-theme="dark"] .card-body,
      body.dark-mode .card-body {
        background: #131d38 !important;
        background-color: #131d38 !important;
        color: #f8fafc !important;
      }

      [data-theme="dark"] .modal-content,
      body.dark-mode .modal-content {
        background-color: #131d38 !important;
        border-color: #1e2d52 !important;
        color: #f8fafc !important;
      }

      /* ── Dashboard Specific Components ── */
      [data-theme="dark"] .db-welcome-banner,
      body.dark-mode .db-welcome-banner,
      [data-theme="dark"] .sc,
      body.dark-mode .sc,
      [data-theme="dark"] .sn,
      body.dark-mode .sn,
      [data-theme="dark"] .fin-item,
      body.dark-mode .fin-item,
      [data-theme="dark"] .qa,
      body.dark-mode .qa,
      [data-theme="dark"] .courier-card,
      body.dark-mode .courier-card,
      [data-theme="dark"] .traffic-card,
      body.dark-mode .traffic-card,
      [data-theme="dark"] .chart-box,
      body.dark-mode .chart-box,
      [data-theme="dark"] .tbl-card,
      body.dark-mode .tbl-card,
      [data-theme="dark"] .tbl-card-head,
      body.dark-mode .tbl-card-head {
        background: #131d38 !important;
        background-color: #131d38 !important;
        border-color: #1e2d52 !important;
        color: #f8fafc !important;
      }

      [data-theme="dark"] .traffic-item,
      body.dark-mode .traffic-item,
      [data-theme="dark"] .db-store-avatar,
      body.dark-mode .db-store-avatar,
      [data-theme="dark"] .qa-ico-box,
      body.dark-mode .qa-ico-box {
        background-color: #0f172a !important;
        border-color: #1e2d52 !important;
        color: #cbd5e1 !important;
      }

      [data-theme="dark"] .traffic-item:hover,
      body.dark-mode .traffic-item:hover {
        background-color: #182343 !important;
        border-color: #2d3f6d !important;
      }

      [data-theme="dark"] .db-welcome-text h1,
      body.dark-mode .db-welcome-text h1,
      [data-theme="dark"] .sc-val,
      body.dark-mode .sc-val,
      [data-theme="dark"] .sn-val,
      body.dark-mode .sn-val,
      [data-theme="dark"] .fin-item-val,
      body.dark-mode .fin-item-val,
      [data-theme="dark"] .courier-main-val,
      body.dark-mode .courier-main-val,
      [data-theme="dark"] .chart-box-title,
      body.dark-mode .chart-box-title,
      [data-theme="dark"] .tbl-card-head h4,
      body.dark-mode .tbl-card-head h4 {
        color: #ffffff !important;
      }

      [data-theme="dark"] .sc-label,
      body.dark-mode .sc-label,
      [data-theme="dark"] .sn-name,
      body.dark-mode .sn-name,
      [data-theme="dark"] .fin-item-label,
      body.dark-mode .fin-item-label,
      [data-theme="dark"] .courier-title,
      body.dark-mode .courier-title,
      [data-theme="dark"] .section-label,
      body.dark-mode .section-label,
      [data-theme="dark"] .chart-box-sub,
      body.dark-mode .chart-box-sub {
        color: #94a3b8 !important;
      }

      [data-theme="dark"] table.clean th,
      body.dark-mode table.clean th {
        background-color: #182343 !important;
        color: #cbd5e1 !important;
        border-color: #1e2d52 !important;
      }
      [data-theme="dark"] table.clean td,
      body.dark-mode table.clean td {
        color: #f8fafc !important;
        border-color: #1e2d52 !important;
      }
      [data-theme="dark"] table.clean tbody tr:hover td,
      body.dark-mode table.clean tbody tr:hover td {
        background-color: #1a274c !important;
      }
      [data-theme="dark"] .cat-item,
      body.dark-mode .cat-item {
        color: #f8fafc !important;
        border-color: #1e2d52 !important;
      }
      [data-theme="dark"] .cat-item:hover,
      body.dark-mode .cat-item:hover {
        background-color: #1a274c !important;
      }

      /* ── Tables & Data across ALL pages ── */
      [data-theme="dark"] table,
      body.dark-mode table,
      [data-theme="dark"] .table,
      body.dark-mode .table,
      [data-theme="dark"] .table-modern,
      body.dark-mode .table-modern,
      [data-theme="dark"] .table-pro,
      body.dark-mode .table-pro,
      [data-theme="dark"] .oi-table,
      body.dark-mode .oi-table,
      [data-theme="dark"] .order-index-table,
      body.dark-mode .order-index-table,
      [data-theme="dark"] .table-bordered,
      body.dark-mode .table-bordered,
      [data-theme="dark"] .table-hover,
      body.dark-mode .table-hover,
      [data-theme="dark"] .table-striped,
      body.dark-mode .table-striped {
        background-color: #131d38 !important;
        color: #f8fafc !important;
        border-color: #1e2d52 !important;
      }

      [data-theme="dark"] table thead th,
      body.dark-mode table thead th,
      [data-theme="dark"] .table thead th,
      body.dark-mode .table thead th,
      [data-theme="dark"] .table-modern th,
      body.dark-mode .table-modern th,
      [data-theme="dark"] .table-pro thead th,
      body.dark-mode .table-pro thead th,
      [data-theme="dark"] .oi-table thead th,
      body.dark-mode .oi-table thead th,
      [data-theme="dark"] .order-index-table thead th,
      body.dark-mode .order-index-table thead th {
        background-color: #182343 !important;
        background: #182343 !important;
        color: #cbd5e1 !important;
        border-bottom: 2px solid #1e2d52 !important;
        border-color: #1e2d52 !important;
        font-weight: 700 !important;
      }

      [data-theme="dark"] table tbody td,
      body.dark-mode table tbody td,
      [data-theme="dark"] .table tbody td,
      body.dark-mode .table tbody td,
      [data-theme="dark"] .table-modern td,
      body.dark-mode .table-modern td,
      [data-theme="dark"] .table-pro tbody td,
      body.dark-mode .table-pro tbody td,
      [data-theme="dark"] .oi-table tbody td,
      body.dark-mode .oi-table tbody td,
      [data-theme="dark"] .order-index-table tbody td,
      body.dark-mode .order-index-table tbody td,
      [data-theme="dark"] table tbody tr,
      body.dark-mode table tbody tr,
      [data-theme="dark"] .table tbody tr,
      body.dark-mode .table tbody tr,
      [data-theme="dark"] .oi-table tbody tr,
      body.dark-mode .oi-table tbody tr,
      [data-theme="dark"] .order-index-table tbody tr,
      body.dark-mode .order-index-table tbody tr {
        background-color: #131d38 !important;
        background: #131d38 !important;
        color: #f8fafc !important;
        border-color: #1e2d52 !important;
        border-bottom: 1px solid #1e2d52 !important;
      }

      [data-theme="dark"] table tbody tr:hover,
      body.dark-mode table tbody tr:hover,
      [data-theme="dark"] table tbody tr:hover td,
      body.dark-mode table tbody tr:hover td,
      [data-theme="dark"] .table-hover tbody tr:hover,
      body.dark-mode .table-hover tbody tr:hover,
      [data-theme="dark"] .table-hover tbody tr:hover td,
      body.dark-mode .table-hover tbody tr:hover td,
      [data-theme="dark"] .oi-table tbody tr:hover,
      body.dark-mode .oi-table tbody tr:hover,
      [data-theme="dark"] .order-index-table tbody tr:hover,
      body.dark-mode .order-index-table tbody tr:hover {
        background-color: #1a274c !important;
        background: #1a274c !important;
        color: #ffffff !important;
      }

      [data-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > *,
      body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
        background-color: #15203d !important;
        color: #f8fafc !important;
      }

      /* ── Typography & High-Contrast Readability ── */
      [data-theme="dark"] h1, body.dark-mode h1,
      [data-theme="dark"] h2, body.dark-mode h2,
      [data-theme="dark"] h3, body.dark-mode h3,
      [data-theme="dark"] h4, body.dark-mode h4,
      [data-theme="dark"] h5, body.dark-mode h5,
      [data-theme="dark"] h6, body.dark-mode h6,
      [data-theme="dark"] .page-title, body.dark-mode .page-title,
      [data-theme="dark"] .oi-page-header h4, body.dark-mode .oi-page-header h4,
      [data-theme="dark"] .card-title, body.dark-mode .card-title,
      [data-theme="dark"] .modal-title, body.dark-mode .modal-title,
      [data-theme="dark"] .oi-card-head h6, body.dark-mode .oi-card-head h6 {
        color: #ffffff !important;
      }

      [data-theme="dark"] p, body.dark-mode p,
      [data-theme="dark"] label, body.dark-mode label,
      [data-theme="dark"] .fw-bold, body.dark-mode .fw-bold,
      [data-theme="dark"] .text-dark, body.dark-mode .text-dark,
      [data-theme="dark"] .fw-bold.text-dark, body.dark-mode .fw-bold.text-dark {
        color: #f8fafc !important;
      }

      [data-theme="dark"] .text-muted, body.dark-mode .text-muted,
      [data-theme="dark"] .text-secondary, body.dark-mode .text-secondary,
      [data-theme="dark"] small, body.dark-mode small,
      [data-theme="dark"] .oi-sub, body.dark-mode .oi-sub {
        color: #94a3b8 !important;
      }

      [data-theme="dark"] a:not(.btn):not(.badge):not(.nav-link):not(.rail-item),
      body.dark-mode a:not(.btn):not(.badge):not(.nav-link):not(.rail-item) {
        color: #60a5fa !important;
      }
      [data-theme="dark"] a:not(.btn):not(.badge):not(.nav-link):not(.rail-item):hover,
      body.dark-mode a:not(.btn):not(.badge):not(.nav-link):not(.rail-item):hover {
        color: #93c5fd !important;
      }
      [data-theme="dark"] .oi-invoice-link,
      body.dark-mode .oi-invoice-link {
        color: #60a5fa !important;
        font-weight: 700 !important;
      }

      /* ── Inputs & Forms ── */
      [data-theme="dark"] input[type="text"], body.dark-mode input[type="text"],
      [data-theme="dark"] input[type="search"], body.dark-mode input[type="search"],
      [data-theme="dark"] input[type="number"], body.dark-mode input[type="number"],
      [data-theme="dark"] input[type="password"], body.dark-mode input[type="password"],
      [data-theme="dark"] input[type="email"], body.dark-mode input[type="email"],
      [data-theme="dark"] input[type="tel"], body.dark-mode input[type="tel"],
      [data-theme="dark"] input[type="url"], body.dark-mode input[type="url"],
      [data-theme="dark"] input[type="date"], body.dark-mode input[type="date"],
      [data-theme="dark"] select, body.dark-mode select,
      [data-theme="dark"] textarea, body.dark-mode textarea,
      [data-theme="dark"] .form-control, body.dark-mode .form-control,
      [data-theme="dark"] .form-select, body.dark-mode .form-select,
      [data-theme="dark"] .form-control-custom, body.dark-mode .form-control-custom,
      [data-theme="dark"] .form-select-custom, body.dark-mode .form-select-custom,
      [data-theme="dark"] .input-group-text, body.dark-mode .input-group-text,
      [data-theme="dark"] .select2-container .select2-selection, body.dark-mode .select2-container .select2-selection {
        background-color: #0f172a !important;
        background: #0f172a !important;
        border: 1px solid #2d3f6d !important;
        color: #f8fafc !important;
      }

      [data-theme="dark"] input::placeholder, body.dark-mode input::placeholder,
      [data-theme="dark"] textarea::placeholder, body.dark-mode textarea::placeholder,
      [data-theme="dark"] .form-control::placeholder, body.dark-mode .form-control::placeholder {
        color: #64748b !important;
      }

      [data-theme="dark"] .form-control:focus, body.dark-mode .form-control:focus,
      [data-theme="dark"] .form-select:focus, body.dark-mode .form-select:focus,
      [data-theme="dark"] input:focus, body.dark-mode input:focus,
      [data-theme="dark"] select:focus, body.dark-mode select:focus,
      [data-theme="dark"] textarea:focus, body.dark-mode textarea:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
        background-color: #0f172a !important;
        color: #ffffff !important;
      }

      /* ── Dropdowns & Modals ── */
      [data-theme="dark"] .dropdown-menu, body.dark-mode .dropdown-menu {
        background-color: #131d38 !important;
        border-color: #1e2d52 !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
      }
      [data-theme="dark"] .dropdown-item, body.dark-mode .dropdown-item {
        color: #cbd5e1 !important;
      }
      [data-theme="dark"] .dropdown-item:hover, body.dark-mode .dropdown-item:hover {
        background-color: #1e2d52 !important;
        color: #ffffff !important;
      }

      [data-theme="dark"] .btn-action-icon, body.dark-mode .btn-action-icon,
      [data-theme="dark"] .action-btn, body.dark-mode .action-btn {
        background-color: #182343 !important;
        border-color: #1e2d52 !important;
        color: #94a3b8 !important;
      }
      [data-theme="dark"] .btn-action-icon:hover, body.dark-mode .btn-action-icon:hover,
      [data-theme="dark"] .action-btn:hover, body.dark-mode .action-btn:hover {
        background-color: #1e2d52 !important;
        color: #ffffff !important;
      }

      [data-theme="dark"] .order-quick-view-btn, body.dark-mode .order-quick-view-btn {
        background-color: #1e2d52 !important;
        color: #60a5fa !important;
      }

      /* ── Order Index Bulk Actions & Quick Views ── */
      [data-theme="dark"] .oi-btn-tool, body.dark-mode .oi-btn-tool {
        background: #182343 !important;
        border-color: #2d3f6d !important;
        color: #e2e8f0 !important;
      }
      [data-theme="dark"] .oi-btn-assign, body.dark-mode .oi-btn-assign { background: rgba(16, 185, 129, 0.15) !important; color: #34d399 !important; border-color: rgba(16, 185, 129, 0.3) !important; }
      [data-theme="dark"] .oi-btn-status, body.dark-mode .oi-btn-status { background: rgba(99, 102, 241, 0.15) !important; color: #818cf8 !important; border-color: rgba(99, 102, 241, 0.3) !important; }
      [data-theme="dark"] .oi-btn-delete, body.dark-mode .oi-btn-delete { background: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; border-color: rgba(239, 68, 68, 0.3) !important; }
      [data-theme="dark"] .oi-btn-print, body.dark-mode .oi-btn-print { background: rgba(14, 165, 233, 0.15) !important; color: #38bdf8 !important; border-color: rgba(14, 165, 233, 0.3) !important; }
      [data-theme="dark"] .oi-btn-label, body.dark-mode .oi-btn-label { background: #182343 !important; color: #cbd5e1 !important; border-color: #2d3f6d !important; }
      [data-theme="dark"] .oi-btn-courier, body.dark-mode .oi-btn-courier { background: rgba(249, 115, 22, 0.15) !important; color: #fb923c !important; border-color: rgba(249, 115, 22, 0.3) !important; }
      [data-theme="dark"] .oi-btn-pathao, body.dark-mode .oi-btn-pathao { background: rgba(234, 179, 8, 0.15) !important; color: #facc15 !important; border-color: rgba(234, 179, 8, 0.3) !important; }
      [data-theme="dark"] .oi-btn-redx, body.dark-mode .oi-btn-redx { background: rgba(249, 115, 22, 0.15) !important; color: #fb923c !important; border-color: rgba(249, 115, 22, 0.3) !important; }

      [data-theme="dark"] .upload-box-dashed, body.dark-mode .upload-box-dashed,
      [data-theme="dark"] .upload-area, body.dark-mode .upload-area {
        background-color: #0f172a !important;
        border-color: #334155 !important;
      }

      [data-theme="dark"] .email-chip, body.dark-mode .email-chip,
      [data-theme="dark"] .tiktok-badge, body.dark-mode .tiktok-badge,
      [data-theme="dark"] .pixel-badge, body.dark-mode .pixel-badge,
      [data-theme="dark"] .gtm-badge, body.dark-mode .gtm-badge {
        background-color: #0f172a !important;
        border-color: #1e2d52 !important;
        color: #f1f5f9 !important;
      }

      [data-theme="dark"] .border-top, body.dark-mode .border-top,
      [data-theme="dark"] .border-bottom, body.dark-mode .border-bottom,
      [data-theme="dark"] .border, body.dark-mode .border {
        border-color: #1e2d52 !important;
      }

      /* ── Smart Date Filter & Dropdowns in Dark Mode ── */
      [data-theme="dark"] .smart-date-btn,
      body.dark-mode .smart-date-btn {
        background: #0f172a !important;
        background-color: #0f172a !important;
        border-color: #2d3f6d !important;
        color: #f8fafc !important;
      }
      [data-theme="dark"] .smart-date-btn:hover,
      body.dark-mode .smart-date-btn:hover {
        background: #182343 !important;
        border-color: #6366f1 !important;
      }
      [data-theme="dark"] .smart-date-dropdown,
      body.dark-mode .smart-date-dropdown {
        background-color: #131d38 !important;
        border: 1px solid #1e2d52 !important;
        color: #f8fafc !important;
      }
      [data-theme="dark"] .smart-date-dropdown .bg-light,
      body.dark-mode .smart-date-dropdown .bg-light {
        background-color: #182343 !important;
        border-color: #1e2d52 !important;
      }
      [data-theme="dark"] .smart-date-dropdown .dropdown-item,
      body.dark-mode .smart-date-dropdown .dropdown-item {
        color: #cbd5e1 !important;
      }
      [data-theme="dark"] .smart-date-dropdown .dropdown-item:hover,
      body.dark-mode .smart-date-dropdown .dropdown-item:hover {
        background-color: #1e2d52 !important;
        color: #ffffff !important;
      }
      [data-theme="dark"] .smart-date-dropdown .dropdown-item.active,
      body.dark-mode .smart-date-dropdown .dropdown-item.active {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
      }

      /* ── Filter Cards, Stat Cards & Report Elements ── */
      [data-theme="dark"] .filter-card,
      body.dark-mode .filter-card,
      [data-theme="dark"] .stat-card,
      body.dark-mode .stat-card {
        background: #131d38 !important;
        background-color: #131d38 !important;
        border-color: #1e2d52 !important;
        color: #f8fafc !important;
      }
      [data-theme="dark"] .stat-label,
      body.dark-mode .stat-label,
      [data-theme="dark"] .form-label-custom,
      body.dark-mode .form-label-custom {
        color: #94a3b8 !important;
      }
      [data-theme="dark"] .stat-value,
      body.dark-mode .stat-value {
        color: #ffffff !important;
      }
      [data-theme="dark"] .bg-light-primary,
      body.dark-mode .bg-light-primary {
        background: rgba(99, 102, 241, 0.2) !important;
        color: #818cf8 !important;
      }
      [data-theme="dark"] .bg-light-success,
      body.dark-mode .bg-light-success {
        background: rgba(16, 185, 129, 0.2) !important;
        color: #34d399 !important;
      }
      [data-theme="dark"] .bg-light-warning,
      body.dark-mode .bg-light-warning {
        background: rgba(245, 158, 11, 0.2) !important;
        color: #fbbf24 !important;
      }
      [data-theme="dark"] .bg-light-info,
      body.dark-mode .bg-light-info {
        background: rgba(14, 165, 233, 0.2) !important;
        color: #38bdf8 !important;
      }

      /* ── Input Groups & Form Addons ── */
      [data-theme="dark"] .input-group-text,
      body.dark-mode .input-group-text,
      [data-theme="dark"] .input-group-text.bg-white,
      body.dark-mode .input-group-text.bg-white {
        background-color: #182343 !important;
        background: #182343 !important;
        border-color: #2d3f6d !important;
        color: #cbd5e1 !important;
      }
      [data-theme="dark"] .form-control.bg-white,
      body.dark-mode .form-control.bg-white {
        background-color: #0f172a !important;
        background: #0f172a !important;
        border-color: #2d3f6d !important;
        color: #f8fafc !important;
      }

      /* ── Badges, Chips & Pills ── */
      [data-theme="dark"] .coupon-badge,
      body.dark-mode .coupon-badge {
        background: #182343 !important;
        border-color: #6366f1 !important;
        color: #818cf8 !important;
      }
      [data-theme="dark"] .coupon-badge:hover,
      body.dark-mode .coupon-badge:hover {
        background: #1e2d52 !important;
        border-color: #818cf8 !important;
      }
      [data-theme="dark"] .type-fixed,
      body.dark-mode .type-fixed {
        background: rgba(14, 165, 233, 0.2) !important;
        color: #38bdf8 !important;
      }
      [data-theme="dark"] .type-percent,
      body.dark-mode .type-percent {
        background: rgba(245, 158, 11, 0.2) !important;
        color: #fbbf24 !important;
      }
      [data-theme="dark"] .badge-soft.badge-active,
      body.dark-mode .badge-soft.badge-active {
        background: rgba(16, 185, 129, 0.2) !important;
        color: #34d399 !important;
      }
      [data-theme="dark"] .badge-soft.badge-inactive,
      body.dark-mode .badge-soft.badge-inactive {
        background: #182343 !important;
        color: #94a3b8 !important;
      }
      [data-theme="dark"] .badge-soft.badge-expired,
      body.dark-mode .badge-soft.badge-expired {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #f87171 !important;
      }
      [data-theme="dark"] .badge.bg-light.text-dark,
      body.dark-mode .badge.bg-light.text-dark {
        background-color: #182343 !important;
        border-color: #2d3f6d !important;
        color: #cbd5e1 !important;
      }
      [data-theme="dark"] .badge.bg-light.text-primary,
      body.dark-mode .badge.bg-light.text-primary {
        background-color: #182343 !important;
        border-color: #2d3f6d !important;
        color: #60a5fa !important;
      }

      /* ── Gemini AI Floating Chatbot in Dark Mode ── */
      [data-theme="dark"] #gemini-admin-widget #gaw-panel,
      body.dark-mode #gemini-admin-widget #gaw-panel {
        background: #131d38 !important;
        box-shadow: -10px 0 50px rgba(0, 0, 0, 0.6) !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-messages,
      body.dark-mode #gemini-admin-widget #gaw-messages {
        background: #0b1329 !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-messages .gaw-msg.bot .gaw-bubble,
      body.dark-mode #gemini-admin-widget #gaw-messages .gaw-msg.bot .gaw-bubble {
        background: #182343 !important;
        border-color: #1e2d52 !important;
        color: #f8fafc !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-empty strong,
      body.dark-mode #gemini-admin-widget #gaw-empty strong {
        color: #ffffff !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-empty p,
      body.dark-mode #gemini-admin-widget #gaw-empty p {
        color: #94a3b8 !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-suggestions button,
      body.dark-mode #gemini-admin-widget #gaw-suggestions button {
        background: #182343 !important;
        border-color: #1e2d52 !important;
        color: #cbd5e1 !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-suggestions button:hover,
      body.dark-mode #gemini-admin-widget #gaw-suggestions button:hover {
        background: #1e2d52 !important;
        color: #818cf8 !important;
        border-color: #6366f1 !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-typing,
      body.dark-mode #gemini-admin-widget #gaw-typing {
        background: #0b1329 !important;
        color: #818cf8 !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-input-wrap,
      body.dark-mode #gemini-admin-widget #gaw-input-wrap {
        background: #131d38 !important;
        border-top-color: #1e2d52 !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-input-form,
      body.dark-mode #gemini-admin-widget #gaw-input-form {
        background: #0f172a !important;
        border-color: #2d3f6d !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-input-form:focus-within,
      body.dark-mode #gemini-admin-widget #gaw-input-form:focus-within {
        background: #0f172a !important;
        border-color: #6366f1 !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-input,
      body.dark-mode #gemini-admin-widget #gaw-input {
        color: #f8fafc !important;
      }
      [data-theme="dark"] #gemini-admin-widget #gaw-input::placeholder,
      body.dark-mode #gemini-admin-widget #gaw-input::placeholder {
        color: #64748b !important;
      }

      /* ── Summernote & Rich Text Editors in Dark Mode ── */
      [data-theme="dark"] .note-editor.note-frame,
      body.dark-mode .note-editor.note-frame {
        border-color: #2d3f6d !important;
        background-color: #0f172a !important;
      }
      [data-theme="dark"] .note-toolbar,
      body.dark-mode .note-toolbar {
        background-color: #182343 !important;
        border-bottom-color: #2d3f6d !important;
      }
      [data-theme="dark"] .note-btn,
      body.dark-mode .note-btn {
        background-color: #131d38 !important;
        border-color: #2d3f6d !important;
        color: #cbd5e1 !important;
      }
      [data-theme="dark"] .note-editable,
      body.dark-mode .note-editable {
        background-color: #0f172a !important;
        color: #f8fafc !important;
      }
      [data-theme="dark"] .note-statusbar,
      body.dark-mode .note-statusbar {
        background-color: #182343 !important;
        border-top-color: #2d3f6d !important;
      }

      [data-theme="dark"] .profile-dropdown-custom, body.dark-mode .profile-dropdown-custom {
        background-color: #131d38 !important;
        border: 1px solid #1e2d52 !important;
      }
      [data-theme="dark"] .profile-item:hover, body.dark-mode .profile-item:hover {
        background-color: #1e2d52 !important;
      }

      .profile-dropdown-custom .profile-item {
        transition: all 0.2s ease;
      }
      .profile-dropdown-custom .profile-item:hover {
        background-color: #f1f5f9;
        transform: translateX(3px);
      }
    </style>
    <!-- Head js -->
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
              <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="fullscreen" href="#" title="Fullscreen">
                <i class="fe-maximize noti-icon"></i>
              </a>
            </li>

            {{-- 🌙 DARK / LIGHT MODE TOGGLE BUTTON --}}
            <li class="dropdown d-inline-block">
              <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" id="header-theme-toggle" href="javascript:void(0);" role="button" title="Toggle Dark / Light Mode">
                <i class="fe-moon noti-icon" id="header-theme-icon-moon"></i>
                <i class="fe-sun noti-icon text-warning d-none" id="header-theme-icon-sun"></i>
              </a>
            </li>

            @if(isset($demoMode) && $demoMode)
            <li class="dropdown d-none d-lg-inline-block">
              <span class="badge bg-warning text-dark px-2 py-1 mt-1" title=".env থেকে DEMO_MODE=true সেট করা আছে"><i class="fe-eye me-1"></i>ডেমো</span>
            </li>
            @endif

            {{-- 🔔 REDESIGNED MODERN NOTIFICATION DROPDOWN --}}
            <li class="dropdown notification-list topbar-dropdown">
              <a class="nav-link dropdown-toggle waves-effect waves-light position-relative noti-bell-link" data-bs-toggle="dropdown" data-bs-display="static" href="#" role="button" aria-haspopup="false" aria-expanded="false" title="নতুন অর্ডার নোটিফিকেশন">
                <i class="fe-bell noti-icon"></i>
                @if($neworder > 0)
                  <span class="badge bg-danger rounded-circle noti-icon-badge noti-pulse-badge">{{ $neworder > 99 ? '99+' : $neworder }}</span>
                @endif
              </a>
              <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 noti-dropdown-custom" style="z-index: 1055;">
                {{-- Header Card --}}
                <div class="p-3 text-white d-flex align-items-center justify-content-between noti-header-card" style="background: linear-gradient(135deg, {{ $brandPrimary }} 0%, #0f172a 100%);">
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center text-white" style="width: 34px; height: 34px;">
                      <i class="fe-bell" style="font-size: 15px;"></i>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-bold text-white" style="font-size: 14px;">নতুন অর্ডার নোটিফিকেশন</h6>
                      <small class="text-white-50" style="font-size: 11px;">{{ $neworder }} টি পেন্ডিং অর্ডার অপেক্ষমাণ</small>
                    </div>
                  </div>
                  @if($neworder > 0)
                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 11px;">{{ $neworder }} টি নতুন</span>
                  @else
                    <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 11px;">সব প্রসেসড</span>
                  @endif
                </div>

                {{-- Notification List --}}
                <div class="noti-scroll-custom" style="max-height: 340px; overflow-y: auto;">
                  @forelse($pendingorder as $porder)
                    <a href="{{ route('admin.order.invoice', ['invoice_id' => $porder->invoice_id]) }}" class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none noti-item-row">
                      <div class="flex-shrink-0">
                        @if($porder->customer && !empty($porder->customer->image))
                          <img src="{{ asset($porder->customer->image) }}" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;" alt="customer" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" />
                          <div class="rounded-circle align-items-center justify-content-center fw-bold" style="display: none; width: 40px; height: 40px; background: rgba(79, 70, 229, 0.1); color: {{ $brandPrimary }}; border: 1px solid rgba(79, 70, 229, 0.2); font-size: 14px;">
                            {{ mb_substr($porder->customer ? $porder->customer->name : 'অর্ডার', 0, 1) }}
                          </div>
                        @else
                          <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; background: rgba(79, 70, 229, 0.1); color: {{ $brandPrimary }}; border: 1px solid rgba(79, 70, 229, 0.2); font-size: 14px;">
                            {{ mb_substr($porder->customer ? $porder->customer->name : 'অর্ডার', 0, 1) }}
                          </div>
                        @endif
                      </div>
                      <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                          <span class="fw-bold text-dark text-truncate" style="max-width: 160px; font-size: 13px;">
                            {{ $porder->customer ? $porder->customer->name : 'গেস্ট কাস্টমার' }}
                          </span>
                          <span class="fw-bold text-success" style="font-size: 12.5px;">
                            ৳{{ number_format($porder->amount) }}
                          </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 11.5px;">
                          <span class="font-monospace text-secondary">
                            <i class="fe-file-text me-1"></i>#{{ $porder->invoice_id }}
                          </span>
                          <span>
                            <i class="fe-clock me-1"></i>{{ $porder->created_at ? $porder->created_at->diffForHumans(null, true) : 'এখন' }}
                          </span>
                        </div>
                      </div>
                    </a>
                  @empty
                    <div class="p-4 text-center">
                      <div class="avatar-md rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #f1f5f9; color: #94a3b8;">
                        <i class="fe-bell-off" style="font-size: 22px;"></i>
                      </div>
                      <h6 class="fw-semibold text-dark mb-1" style="font-size: 13px;">কোনো পেন্ডিং অর্ডার নেই</h6>
                      <p class="text-muted mb-0" style="font-size: 11.5px;">নতুন অর্ডার আসলে এখানে সঙ্গে সঙ্গে দেখতে পাবেন।</p>
                    </div>
                  @endforelse
                </div>

                {{-- Footer Action Bar --}}
                <div class="p-2 bg-light border-top text-center noti-footer-bar">
                  <a href="{{ route('admin.orders', ['slug' => 'pending']) }}" class="btn btn-sm btn-link text-primary fw-semibold text-decoration-none d-flex align-items-center justify-content-center gap-1 w-100 py-1">
                    <span>সকল পেন্ডিং অর্ডার দেখুন</span>
                    <i class="fe-arrow-right fs-12"></i>
                  </a>
                </div>
              </div>
            </li>

            {{-- 👤 REDESIGNED MODERN PROFILE DROPDOWN --}}
            <li class="dropdown notification-list topbar-dropdown">
              <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light d-flex align-items-center" data-bs-toggle="dropdown" data-bs-display="static" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <div>
                  <img src="{{asset(Auth::guard('admin')->user()->image ? Auth::guard('admin')->user()->image : 'public/backEnd/assets/images/users/user-1.jpg')}}" alt="user-image" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);" />
                </div>
                <span class="pro-user-name ms-2 fw-semibold text-white d-none d-sm-inline-block">
                  {{ Auth::guard('admin')->user()->name }} <i class="mdi mdi-chevron-down"></i>
                </span>
              </a>
              <div class="dropdown-menu dropdown-menu-end profile-dropdown-custom p-0 shadow-lg border-0 overflow-hidden" style="width: 270px; z-index: 1055;">
                {{-- Header Card --}}
                <div class="p-3 text-white" style="background: linear-gradient(135deg, {{ $brandPrimary }} 0%, #0f172a 100%);">
                  <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0">
                      <img src="{{asset(Auth::guard('admin')->user()->image ? Auth::guard('admin')->user()->image : 'public/backEnd/assets/images/users/user-1.jpg')}}" alt="user" class="rounded-circle border border-2 border-white shadow-sm" style="width: 46px; height: 46px; object-fit: cover;" />
                    </div>
                    <div class="overflow-hidden">
                      <h6 class="mb-0 fw-bold text-white text-truncate">{{ Auth::guard('admin')->user()->name }}</h6>
                      <small class="text-white-50 text-truncate d-block" style="font-size: 11px;">{{ Auth::guard('admin')->user()->email }}</small>
                      <span class="badge bg-white text-dark mt-1 px-2 py-0.5" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        {{ Auth::guard('admin')->user()->role ?? 'Super Admin' }}
                      </span>
                    </div>
                  </div>
                </div>

                {{-- Action Menu --}}
                <div class="p-2">
                  <a href="{{ url('admin/dashboard') }}" class="dropdown-item profile-item d-flex align-items-center gap-2 py-2 px-3 rounded-3">
                    <i data-feather="grid" style="width: 15px; height: 15px;" class="text-primary"></i>
                    <span class="fw-semibold small">Dashboard</span>
                  </a>

                  <a href="{{ route('users.edit', Auth::guard('admin')->user()->id) }}" class="dropdown-item profile-item d-flex align-items-center gap-2 py-2 px-3 rounded-3">
                    <i data-feather="user" style="width: 15px; height: 15px;" class="text-info"></i>
                    <span class="fw-semibold small">Edit Profile</span>
                  </a>

                  <a href="{{ route('change_password') }}" class="dropdown-item profile-item d-flex align-items-center gap-2 py-2 px-3 rounded-3">
                    <i data-feather="key" style="width: 15px; height: 15px;" class="text-warning"></i>
                    <span class="fw-semibold small">Change Password</span>
                  </a>

                  <a href="{{ route('settings.index') }}" class="dropdown-item profile-item d-flex align-items-center gap-2 py-2 px-3 rounded-3">
                    <i data-feather="settings" style="width: 15px; height: 15px;" class="text-secondary"></i>
                    <span class="fw-semibold small">Site Settings</span>
                  </a>

                  <div class="dropdown-divider my-1"></div>

                  {{-- Dark Mode Switch inside dropdown --}}
                  <div class="d-flex justify-content-between align-items-center px-3 py-2">
                    <span class="small fw-semibold text-muted d-flex align-items-center gap-2">
                      <i data-feather="moon" style="width: 14px; height: 14px;"></i> Dark Mode
                    </span>
                    <div class="form-check form-switch mb-0">
                      <input class="form-check-input" type="checkbox" id="profile-dark-mode-switch" style="cursor:pointer;">
                    </div>
                  </div>

                  <div class="dropdown-divider my-1"></div>

                  {{-- Logout --}}
                  <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item profile-item text-danger d-flex align-items-center gap-2 py-2 px-3 rounded-3">
                    <i data-feather="log-out" style="width: 15px; height: 15px;"></i>
                    <span class="fw-bold small">Log Out</span>
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                  </form>
                </div>
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
              <button class="button-menu-mobile waves-effect waves-light" id="sidebar-toggle-btn" type="button" title="Toggle Sidebar">
                <i class="fe-menu"></i>
              </button>
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
  } elseif (request()->routeIs('inhouse.products.*', 'products.*', 'categories.*', 'subcategories.*', 'childcategories.*', 'brands.*', 'colors.*', 'sizes.*', 'admin.products.wholesale', 'purchases.*', 'admin.suppliers.*', 'admin.media.*')) {
      $activeSection = 'section-catalog';
  } elseif (request()->routeIs('admin.fund.*', 'admin.expenses.*')) {
      $activeSection = 'section-finance';
  } elseif (request()->routeIs('settings.*', 'socialmedias.*', 'contact.*', 'pages.*', 'email_setting*', 'backEnd.complaints.*', 'admin.contact.messages*', 'admin.newsletter.subscribers*', 'admin.seo_settings.*', 'admin.sitemap.*', 'admin.cron.*', 'error-log.*')) {
      $activeSection = 'section-settings';
  } elseif (request()->routeIs('admin.ads_analytics.*', 'tagmanagers.*', 'pixels.*', 'tiktok.pixels.*', 'paymentgeteway.*', 'manual-payment-gateway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.gemini_ai.*', 'admin.fraud.*', 'admin.facebook_page.*', 'admin.reports.*')) {
      $activeSection = 'section-analytics';
  } elseif (request()->routeIs('campaign.*', 'admin.coupons.*', 'banners.*', 'admin.popup.*', 'admin.sale-notification.*', 'reviews.*', 'admin.blog.*', 'admin.sms.custom.*')) {
      $activeSection = 'section-marketing';
  }
@endphp

      <div class="left-side-menu two-column-sidebar" id="two-column-sidebar">
        <!-- 1. LEFT ICON RAIL (Always visible on desktop & in collapsed mode) -->
        <div class="sidebar-icon-rail">
          <div class="icon-rail-nav">
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-main' ? 'active' : '' }}" data-section="section-main" title="Main Menu" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="grid"></i>
            </a>

            @canany(['user-list', 'role-list', 'permission-list', 'customer-list', 'vendor-list', 'reseller-list', 'delivery-boy-list', 'employee-list'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-people' ? 'active' : '' }}" data-section="section-people" title="People & Partners" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="users"></i>
            </a>
            @endcanany

            @canany(['order-list', 'order-edit', 'order-create', 'fraud-check'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-orders' ? 'active' : '' }}" data-section="section-orders" title="Sales & Orders" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="shopping-cart"></i>
              @if(isset($neworder) && $neworder > 0)
                <span class="rail-badge">{{ $neworder }}</span>
              @endif
            </a>
            @endcanany

            @canany(['product-list', 'category-list', 'subcategory-list', 'childcategory-list', 'brand-list', 'color-list', 'size-list', 'purchase-list', 'supplier-list'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-catalog' ? 'active' : '' }}" data-section="section-catalog" title="Catalog & Inventory" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="package"></i>
            </a>
            @endcanany

            @canany(['fund-list', 'fund-create', 'fund-edit', 'expense-list', 'expense-create', 'expense-edit'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-finance' ? 'active' : '' }}" data-section="section-finance" title="Finance & Accounts" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="dollar-sign"></i>
            </a>
            @endcanany

            @canany(['setting-list', 'social-list', 'contact-list', 'api-manage', 'email-setting-list', 'complaint-list', 'seo-manage', 'sitemap-manage', 'cache-clear', 'error-log-view'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-settings' ? 'active' : '' }}" data-section="section-settings" title="System & Settings" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="settings"></i>
            </a>
            @endcanany

            @canany(['pixel-manage', 'report-view', 'order-report', 'purchase-report', 'expense-report', 'stock-report', 'profit-loss-report', 'api-manage', 'fraud-setting-list'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-analytics' ? 'active' : '' }}" data-section="section-analytics" title="Analytics & Reports" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="bar-chart-2"></i>
            </a>
            @endcanany

            @canany(['campaign-list', 'coupon-list', 'banner-list', 'popup-list', 'review-list', 'blog-list', 'sms-send'])
            <a href="javascript:void(0);" class="rail-item {{ $activeSection === 'section-marketing' ? 'active' : '' }}" data-section="section-marketing" title="Marketing & Promotions" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="target"></i>
              @if($pending_reviews > 0)
                <span class="rail-badge">{{ $pending_reviews }}</span>
              @endif
            </a>
            @endcanany
          </div>

          <div class="icon-rail-footer">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('rail-logout-form').submit();" class="rail-item rail-logout-btn" title="Logout" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right">
              <i data-feather="log-out"></i>
            </a>
            <form id="rail-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </div>
        </div>

        <!-- 2. SUBMENU PANEL (Collapsible) -->
        <div class="sidebar-subpanel" id="sidebar-subpanel">
          <!-- Quick Jump Search -->
          <div class="subpanel-search">
            <div class="search-wrap">
              <i data-feather="search"></i>
              <input type="text" id="sidebar-jump-search" placeholder="Jump to..." autocomplete="off" />
              <button type="button" class="clear-search-btn" id="clear-jump-search" style="display:none;">&times;</button>
            </div>
          </div>

          <!-- Submenu Body -->
          <div class="subpanel-body">

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
                    <i data-feather="plus-circle"></i>
                    <span> Add Order </span>
                  </a>
                </li>
                @endcan
              </ul>
            </div>

            {{-- 2. PEOPLE & PARTNERS PANE --}}
            <div class="section-pane {{ $activeSection === 'section-people' ? 'active' : '' }}" id="pane-section-people">
              <div class="pane-title">People & Partners</div>
              <ul class="subpanel-menu">
                @canany(['user-list', 'role-list', 'customer-list'])
                <li class="{{ (request()->routeIs('users.*', 'roles.*', 'customers.*') && !request()->routeIs('customers.ip_block*')) ? 'menuitem-active' : '' }}">
                  <a href="#sub-users" data-bs-toggle="collapse" class="{{ (request()->routeIs('users.*', 'roles.*', 'customers.*') && !request()->routeIs('customers.ip_block*')) ? 'active' : '' }}">
                    <i data-feather="users"></i>
                    <span> Users & Customers </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ (request()->routeIs('users.*', 'roles.*', 'customers.*') && !request()->routeIs('customers.ip_block*')) ? 'show' : '' }}" id="sub-users">
                    <ul class="subpanel-nested-menu">
                      @canany(['customer-list', 'customer-create', 'customer-edit'])
                      <li><a href="{{ route('customers.index') }}" class="{{ (request()->routeIs('customers.*') && !request()->routeIs('customers.ip_block*')) ? 'active' : '' }}"><i data-feather="user-check"></i> Customers</a></li>
                      @endcanany
                      @can('user-list')
                      <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}"><i data-feather="user"></i> Users</a></li>
                      @endcan
                      @can('role-list')
                      <li><a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}"><i data-feather="shield"></i> Roles & Permissions</a></li>
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
                      <li><a href="{{ route('admin.vendors.index') }}" class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> All Vendors</a></li>
                      @endcan
                      @can('vendor-verification')
                      <li>
                        <a href="{{ route('admin.vendor.verification.index') }}" class="{{ request()->routeIs('admin.vendor.verification.*') ? 'active' : '' }}">
                          <i data-feather="shield"></i> Verifications
                          @if($pendingVerificationCount > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ $pendingVerificationCount }}</span>
                          @endif
                        </a>
                      </li>
                      @endcan
                      @can('vendor-withdrawal')
                      <li><a href="{{ route('admin.vendor.withdrawals.index') }}" class="{{ request()->routeIs('admin.vendor.withdrawals.*') ? 'active' : '' }}"><i data-feather="dollar-sign"></i> Withdrawals</a></li>
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
                      <li><a href="{{ route('admin.resellers.index') }}" class="{{ request()->routeIs('admin.resellers.index') || (request()->routeIs('admin.resellers.*') && !request()->routeIs('admin.reseller.verification.*') && !request()->routeIs('admin.reseller.withdrawals.*') && !request()->routeIs('admin.reseller-deposits.*')) ? 'active' : '' }}"><i data-feather="file-plus"></i> All Resellers</a></li>
                      @endcan
                      @can('reseller-withdrawal')
                      <li>
                        <a href="{{ route('admin.reseller-deposits.index') }}" class="{{ request()->routeIs('admin.reseller-deposits.*') ? 'active' : '' }}">
                          <i data-feather="credit-card"></i> Reseller Deposits
                          @if($pendingDepositCount > 0)
                            <span class="badge bg-warning rounded-pill float-end">{{ $pendingDepositCount }}</span>
                          @endif
                        </a>
                      </li>
                      @endcan
                      @can('reseller-verification')
                      <li>
                        <a href="{{ route('admin.reseller.verification.index') }}" class="{{ request()->routeIs('admin.reseller.verification.*') ? 'active' : '' }}">
                          <i data-feather="shield"></i> Verifications
                          @if($pendingResellerVerificationCount > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ $pendingResellerVerificationCount }}</span>
                          @endif
                        </a>
                      </li>
                      @endcan
                      @can('reseller-withdrawal')
                      <li>
                        <a href="{{ route('admin.reseller.withdrawals.index') }}" class="{{ request()->routeIs('admin.reseller.withdrawals.*') ? 'active' : '' }}">
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
                      <li><a href="{{ route('admin.delivery-boys.index') }}" class="{{ request()->routeIs('admin.delivery-boys.index') || (request()->routeIs('admin.delivery-boys.*') && !request()->routeIs('admin.delivery-boys.withdrawals*')) ? 'active' : '' }}"><i data-feather="users"></i> Delivery Persons</a></li>
                      @endcan
                      @can('delivery-withdrawal-list')
                      <li><a href="{{ route('admin.delivery-boys.withdrawals') }}" class="{{ request()->routeIs('admin.delivery-boys.withdrawals*') ? 'active' : '' }}"><i data-feather="dollar-sign"></i> Rider Withdrawals</a></li>
                      @endcan
                      @can('delivery-location-list')
                      <li><a href="{{ route('admin.delivery.divisions.index') }}" class="{{ request()->routeIs('admin.delivery.*') ? 'active' : '' }}"><i data-feather="map-pin"></i> Delivery Locations</a></li>
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
                      <li><a href="{{ route('admin.employees.index') }}" class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"><i data-feather="user"></i> Employees</a></li>
                      @endcan
                      @can('attendance-list')
                      <li><a href="{{ route('admin.attendances.index') }}" class="{{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}"><i data-feather="check-circle"></i> Attendance</a></li>
                      @endcan
                      @can('leave-list')
                      <li><a href="{{ route('admin.leaves.index') }}" class="{{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}"><i data-feather="calendar"></i> Leaves</a></li>
                      @endcan
                      @can('salary-list')
                      <li><a href="{{ route('admin.salaries.index') }}" class="{{ request()->routeIs('admin.salaries.*') ? 'active' : '' }}"><i data-feather="dollar-sign"></i> Salaries</a></li>
                      @endcan
                      @can('bonus-list')
                      <li><a href="{{ route('admin.bonuses.index') }}" class="{{ request()->routeIs('admin.bonuses.*') ? 'active' : '' }}"><i data-feather="gift"></i> Bonuses</a></li>
                      @endcan
                      @can('salary-payment-list')
                      <li><a href="{{ route('admin.salary_payments.index') }}" class="{{ request()->routeIs('admin.salary_payments.*') ? 'active' : '' }}"><i data-feather="credit-card"></i> Salary Payments</a></li>
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
                      <li>
                        <a href="{{ route('admin.orders', ['slug'=>'all']) }}" class="{{ (request()->routeIs('admin.orders') && request()->route('slug') === 'all') || request()->is('admin/orders/all') || request()->is('order/all') ? 'active' : '' }}">
                          <span class="d-inline-flex align-items-center gap-1"><i data-feather="file-plus"></i> All Orders</span>
                          @if(isset($all_orders_count))
                            <span class="subpanel-menu-badge">{{ $all_orders_count }}</span>
                          @endif
                        </a>
                      </li>
                      <li><a href="{{ route('admin.reseller-orders.index') }}" class="{{ request()->routeIs('admin.reseller-orders.*') ? 'active' : '' }}"><i data-feather="users"></i> Reseller Orders</a></li>
                      <li>
                        <a href="{{ route('admin.incomplete-orders.index') }}" class="{{ request()->routeIs('admin.incomplete-orders.*') ? 'active' : '' }}">
                          <span class="d-inline-flex align-items-center gap-1"><i data-feather="file-plus"></i> Incomplete Orders</span>
                          @if(isset($incomplete_orders_count) && $incomplete_orders_count > 0)
                            <span class="subpanel-menu-badge">{{ $incomplete_orders_count }}</span>
                          @endif
                        </a>
                      </li>
                      @if(isset($orderstatus))
                        @foreach($orderstatus as $value)
                          <li>
                            <a href="{{ route('admin.orders', ['slug'=>$value->slug]) }}" class="{{ (request()->routeIs('admin.orders') && request()->route('slug') === $value->slug) || request()->is('admin/orders/'.$value->slug) || request()->is('order/'.$value->slug) ? 'active' : '' }}">
                              <span class="d-inline-flex align-items-center gap-1"><i data-feather="file-plus"></i> {{ $value->name }}</span>
                              <span class="subpanel-menu-badge">{{ $value->orders_count ?? 0 }}</span>
                            </a>
                          </li>
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
                      <li><a href="{{ route('admin.refunds.index') }}" class="{{ request()->routeIs('admin.refunds.index') && !request()->filled('status') ? 'active' : '' }}"><i data-feather="list"></i> All Refunds</a></li>
                      <li><a href="{{ route('admin.refunds.index', ['status' => 'pending']) }}" class="{{ request()->routeIs('admin.refunds.*') && request('status') === 'pending' ? 'active' : '' }}"><i data-feather="clock"></i> Pending Refunds</a></li>
                      <li><a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}" class="{{ request()->routeIs('admin.refunds.*') && request('status') === 'approved' ? 'active' : '' }}"><i data-feather="check-circle"></i> Approved Refunds</a></li>
                      <li><a href="{{ route('admin.refunds.index', ['status' => 'processed']) }}" class="{{ request()->routeIs('admin.refunds.*') && request('status') === 'processed' ? 'active' : '' }}"><i data-feather="check"></i> Processed Refunds</a></li>
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
                      <li><a href="{{ route('inhouse.products.index') }}" class="{{ request()->routeIs('inhouse.products.*') ? 'active' : '' }}"><i data-feather="package"></i> Inhouse Products</a></li>
                      <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}"><i data-feather="shopping-bag"></i> Vendor Products</a></li>
                      <li><a href="{{ route('products.pending') }}" class="{{ request()->routeIs('products.pending') ? 'active' : '' }}"><i data-feather="clock"></i> Pending Products</a></li>
                      <li><a href="{{ route('admin.products.wholesale') }}" class="{{ request()->routeIs('admin.products.wholesale') ? 'active' : '' }}"><i data-feather="layers"></i> Wholesale Products</a></li>
                      @endcan
                      @can('product-create')
                      <li><a href="{{ route('products.create') }}" class="{{ request()->routeIs('products.create') ? 'active' : '' }}"><i data-feather="plus-circle"></i> Add Product</a></li>
                      @endcan
                      @can('category-list')
                      <li><a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*', 'subcategories.*', 'childcategories.*') ? 'active' : '' }}"><i data-feather="grid"></i> Categories</a></li>
                      @endcan
                      @canany(['brand-list', 'brand-create', 'brand-edit'])
                      <li><a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Brands</a></li>
                      @endcanany
                      @canany(['color-list', 'color-create', 'color-edit'])
                      <li><a href="{{ route('colors.index') }}" class="{{ request()->routeIs('colors.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Colors</a></li>
                      @endcanany
                      @canany(['size-list', 'size-create', 'size-edit'])
                      <li><a href="{{ route('sizes.index') }}" class="{{ request()->routeIs('sizes.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Sizes</a></li>
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

                <li class="{{ request()->routeIs('admin.media.*') ? 'menuitem-active' : '' }}">
                  <a href="{{ route('admin.media.index') }}" class="{{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                    <i data-feather="image"></i>
                    <span> Media Manager </span>
                  </a>
                </li>
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
                      <li><a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> General Setting</a></li>
                      @endcan
                      @can('social-list')
                      <li><a href="{{ route('socialmedias.index') }}" class="{{ request()->routeIs('socialmedias.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Social Media</a></li>
                      @endcan
                      @can('contact-list')
                      <li><a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Contact</a></li>
                      @endcan
                      @canany(['page-list', 'page-create', 'page-edit'])
                      <li><a href="{{ route('pages.index') }}" class="{{ request()->routeIs('pages.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Create Page</a></li>
                      @endcanany
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
                      <li><a href="{{ route('backEnd.complaints.index') }}" class="{{ request()->routeIs('backEnd.complaints.*') ? 'active' : '' }}"><i data-feather="alert-circle"></i> Complaints</a></li>
                      @endcanany
                      @can('contact-list')
                      <li><a href="{{ route('admin.contact.messages') }}" class="{{ request()->routeIs('admin.contact.messages*') ? 'active' : '' }}"><i data-feather="mail"></i> Contact Messages</a></li>
                      @endcan
                      @can('newsletter-list')
                      <li><a href="{{ route('admin.newsletter.subscribers') }}" class="{{ request()->routeIs('admin.newsletter.subscribers*') ? 'active' : '' }}"><i data-feather="mail"></i> Subscribers</a></li>
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
                      <li><a href="{{ route('admin.seo_settings.index') }}" class="{{ request()->routeIs('admin.seo_settings.*') ? 'active' : '' }}"><i data-feather="globe"></i> SEO Settings</a></li>
                      @endcan
                      @can('sitemap-manage')
                      <li><a href="{{ route('admin.sitemap.index') }}" class="{{ request()->routeIs('admin.sitemap.*') ? 'active' : '' }}"><i data-feather="map"></i> Sitemap Settings</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

                @canany(['api-manage', 'cache-clear', 'error-log-view'])
                <li class="{{ request()->routeIs('admin.cron.*', 'error-log.*', 'admin.backups.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-maintenance" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.cron.*', 'error-log.*', 'admin.backups.*') ? 'active' : '' }}">
                    <i data-feather="tool"></i>
                    <span> Maintenance </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('admin.cron.*', 'error-log.*', 'admin.backups.*') ? 'show' : '' }}" id="sub-maintenance">
                    <ul class="subpanel-nested-menu">
                      <li><a href="{{ route('admin.backups.index') }}" class="{{ request()->routeIs('admin.backups.*') ? 'active' : '' }}"><i data-feather="hard-drive"></i> Backup & Restore</a></li>
                      @can('api-manage')
                      <li><a href="{{ route('admin.cron.index') }}" class="{{ request()->routeIs('admin.cron.*') ? 'active' : '' }}"><i data-feather="clock"></i> Cron Job</a></li>
                      @endcan
                      @can('cache-clear')
                      <li>
                        <a href="{{ route('admin.clear.cache') }}" onclick="return confirm('Are you sure you want to clear all cache?')">
                          <i data-feather="refresh-cw"></i> Clear Cache
                        </a>
                      </li>
                      @endcan
                      @can('error-log-view')
                      <li><a href="{{ route('error-log.index') }}" class="{{ request()->routeIs('error-log.*') ? 'active' : '' }}"><i data-feather="file-text"></i> Error Log</a></li>
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
                      <li><a href="{{ route('admin.ads_analytics.dashboard') }}" class="{{ request()->routeIs('admin.ads_analytics.dashboard') ? 'active' : '' }}"><i data-feather="layout"></i> Overview</a></li>
                      <li><a href="{{ route('admin.ads_analytics.facebook') }}" class="{{ request()->routeIs('admin.ads_analytics.facebook') ? 'active' : '' }}"><i data-feather="facebook"></i> Facebook Ads</a></li>
                      <li><a href="{{ route('admin.ads_analytics.google') }}" class="{{ request()->routeIs('admin.ads_analytics.google') ? 'active' : '' }}"><i data-feather="globe"></i> Google Ads</a></li>
                      <li><a href="{{ route('admin.ads_analytics.tiktok') }}" class="{{ request()->routeIs('admin.ads_analytics.tiktok') ? 'active' : '' }}"><i data-feather="video"></i> TikTok Ads</a></li>
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
                      <li><a href="{{ route('tagmanagers.index') }}" class="{{ request()->routeIs('tagmanagers.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Tag Manager</a></li>
                      <li><a href="{{ route('pixels.index') }}" class="{{ request()->routeIs('pixels.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Pixel Manage</a></li>
                      <li><a href="{{ route('tiktok.pixels.index') }}" class="{{ request()->routeIs('tiktok.pixels.*') ? 'active' : '' }}"><i data-feather="film"></i> TikTok Pixel</a></li>
                    </ul>
                  </div>
                </li>

                @canany(['api-manage', 'fraud-setting-list'])
                <li class="{{ request()->routeIs('paymentgeteway.*', 'manual-payment-gateway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.gemini_ai.*', 'admin.fraud.*') ? 'menuitem-active' : '' }}">
                  <a href="#sub-api" data-bs-toggle="collapse" class="{{ request()->routeIs('paymentgeteway.*', 'manual-payment-gateway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.gemini_ai.*', 'admin.fraud.*') ? 'active' : '' }}">
                    <i data-feather="cpu"></i>
                    <span> API Integration </span>
                    <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse {{ request()->routeIs('paymentgeteway.*', 'manual-payment-gateway.*', 'smsgeteway.*', 'courierapi.*', 'admin.facebook_capi.*', 'admin.gemini_ai.*', 'admin.fraud.*') ? 'show' : '' }}" id="sub-api">
                    <ul class="subpanel-nested-menu">
                      @can('api-manage')
                      <li><a href="{{ route('paymentgeteway.manage') }}" class="{{ request()->routeIs('paymentgeteway.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Payment Gateway</a></li>
                      <li><a href="{{ route('manual-payment-gateway.manage') }}" class="{{ request()->routeIs('manual-payment-gateway.*') ? 'active' : '' }}"><i data-feather="credit-card"></i> Manual Payment</a></li>
                      <li><a href="{{ route('smsgeteway.manage') }}" class="{{ request()->routeIs('smsgeteway.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> SMS Gateway</a></li>
                      <li><a href="{{ route('courierapi.manage') }}" class="{{ request()->routeIs('courierapi.*') ? 'active' : '' }}"><i data-feather="file-plus"></i> Courier API</a></li>
                      <li><a href="{{ route('admin.facebook_capi.edit') }}" class="{{ request()->routeIs('admin.facebook_capi.*') ? 'active' : '' }}"><i data-feather="facebook"></i> Facebook CAPI</a></li>
                      <li><a href="{{ route('admin.gemini_ai.edit') }}" class="{{ request()->routeIs('admin.gemini_ai.*') ? 'active' : '' }}"><i data-feather="cpu"></i> Gemini AI</a></li>
                      @endcan
                      @can('fraud-setting-list')
                      <li><a href="{{ route('admin.fraud.index') }}" class="{{ request()->routeIs('admin.fraud.*') ? 'active' : '' }}"><i data-feather="key"></i> Manage Fraud API</a></li>
                      @endcan
                    </ul>
                  </div>
                </li>
                @endcanany

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
                      <li><a href="{{ route('admin.reports.orders') }}" class="{{ request()->routeIs('admin.reports.orders') ? 'active' : '' }}"><i data-feather="file-text"></i> Order Report</a></li>
                      @endcanany
                      @canany(['purchase-report','report-view'])
                      <li><a href="{{ route('admin.reports.purchases') }}" class="{{ request()->routeIs('admin.reports.purchases') ? 'active' : '' }}"><i data-feather="shopping-bag"></i> Purchase Report</a></li>
                      @endcanany
                      @canany(['expense-report','report-view'])
                      <li><a href="{{ route('admin.reports.expenses') }}" class="{{ request()->routeIs('admin.reports.expenses') ? 'active' : '' }}"><i data-feather="trending-down"></i> Expense Report</a></li>
                      @endcanany
                      @canany(['stock-report','report-view'])
                      <li><a href="{{ route('admin.reports.stock') }}" class="{{ request()->routeIs('admin.reports.stock') ? 'active' : '' }}"><i data-feather="archive"></i> Stock Report</a></li>
                      @endcanany
                      @canany(['profit-loss-report','report-view'])
                      <li><a href="{{ route('admin.reports.profit_loss') }}" class="{{ request()->routeIs('admin.reports.profit_loss') ? 'active' : '' }}"><i data-feather="activity"></i> Profit & Loss</a></li>
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
                      <li><a href="{{ route('campaign.index') }}" class="{{ request()->routeIs('campaign.index') ? 'active' : '' }}"><i data-feather="file-plus"></i> All Campaigns</a></li>
                      @endcan
                      @can('campaign-create')
                      <li><a href="{{ route('campaign.create') }}" class="{{ request()->routeIs('campaign.create') ? 'active' : '' }}"><i data-feather="file-plus"></i> Create Campaign</a></li>
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
                      <li><a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.index') ? 'active' : '' }}"><i data-feather="list"></i> All Coupons</a></li>
                      @endcan
                      @can('coupon-create')
                      <li><a href="{{ route('admin.coupons.create') }}" class="{{ request()->routeIs('admin.coupons.create') ? 'active' : '' }}"><i data-feather="plus-circle"></i> Add New</a></li>
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
                      <li><a href="{{ route('admin.popup.index') }}" class="{{ request()->routeIs('admin.popup.*') ? 'active' : '' }}"><i data-feather="message-square"></i> Popup Offer</a></li>
                      @endcanany
                      @can('setting-list')
                      <li><a href="{{ route('admin.sale-notification.index') }}" class="{{ request()->routeIs('admin.sale-notification.*') ? 'active' : '' }}"><i data-feather="bell"></i> Sales Notification</a></li>
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
                      <li><a href="{{ route('reviews.pending') }}" class="{{ request()->routeIs('reviews.pending') ? 'active' : '' }}"><i data-feather="file-plus"></i> Pending ({{ $pending_reviews }})</a></li>
                      <li><a href="{{ route('reviews.index') }}" class="{{ request()->routeIs('reviews.index') ? 'active' : '' }}"><i data-feather="file-plus"></i> All Reviews</a></li>
                      @can('review-create')
                      <li><a href="{{ route('reviews.create') }}" class="{{ request()->routeIs('reviews.create') ? 'active' : '' }}"><i data-feather="file-plus"></i> Create Review</a></li>
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
                      <li><a href="{{ route('admin.blog.index') }}" class="{{ request()->routeIs('admin.blog.index') ? 'active' : '' }}"><i data-feather="list"></i> All Blogs</a></li>
                      @endcan
                      @can('blog-create')
                      <li><a href="{{ route('admin.blog.create') }}" class="{{ request()->routeIs('admin.blog.create') ? 'active' : '' }}"><i data-feather="plus-circle"></i> Add New Blog</a></li>
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
                        document.body.setAttribute('data-leftbar-size', 'condensed');
                    } else {
                        document.body.classList.remove('sidebar-collapsed');
                        document.body.setAttribute('data-leftbar-size', 'default');
                    }
                }

                // Rail item click & hover cleanup
                railItems.forEach(function (item) {
                    item.addEventListener('mouseleave', function () {
                        this.blur();
                        try {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                                var tip = bootstrap.Tooltip.getInstance(this);
                                if (tip) tip.hide();
                            }
                            if (typeof jQuery !== 'undefined' && typeof jQuery(this).tooltip === 'function') {
                                jQuery(this).tooltip('hide');
                            }
                        } catch (err) {}
                    });

                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        this.blur();
                        
                        try {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                                var tip = bootstrap.Tooltip.getInstance(this);
                                if (tip) tip.hide();
                            }
                            if (typeof jQuery !== 'undefined' && typeof jQuery(this).tooltip === 'function') {
                                jQuery(this).tooltip('hide');
                            }
                        } catch (err) {}

                        document.querySelectorAll('.tooltip.show, .bs-tooltip-end, .bs-tooltip-right').forEach(function (t) {
                            t.remove();
                        });

                        var targetSection = this.getAttribute('data-section');
                        if (!targetSection) return;

                        // If collapsed on desktop, clicking a section expands it
                        if (document.body.classList.contains('sidebar-collapsed') || document.body.getAttribute('data-leftbar-size') === 'condensed') {
                            document.body.classList.remove('sidebar-collapsed');
                            document.body.setAttribute('data-leftbar-size', 'default');
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
                var toggleBtn = document.getElementById('sidebar-toggle-btn');
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();

                        if (window.innerWidth >= 992) {
                            var isCollapsedNow = document.body.classList.contains('sidebar-collapsed') || document.body.getAttribute('data-leftbar-size') === 'condensed';
                            if (isCollapsedNow) {
                                document.body.classList.remove('sidebar-collapsed');
                                document.body.setAttribute('data-leftbar-size', 'default');
                                localStorage.setItem('two_col_sidebar_collapsed', '0');
                            } else {
                                document.body.classList.add('sidebar-collapsed');
                                document.body.setAttribute('data-leftbar-size', 'condensed');
                                localStorage.setItem('two_col_sidebar_collapsed', '1');
                            }
                        } else {
                            var willOpenMobile = !document.body.classList.contains('sidebar-mobile-open');
                            document.body.classList.toggle('sidebar-mobile-open', willOpenMobile);
                        }
                    });
                }

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
@if($errors->any())
    @foreach ($errors->all() as $error)
        toastr.error(@json($error));
    @endforeach
@endif
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
    @include('backEnd.layouts.partials.image_zoom_modal')
    @auth('admin')
        @include('backEnd.layouts.partials.admin_order_live_notify')
        @include('backEnd.layouts.partials.gemini_admin_chatbot')
    @endauth
    <!-- flatpickr js -->
    <script src="{{asset('public/backEnd/assets/libs/flatpickr/flatpickr.min.js')}}"></script>

    {{-- 🌙 Dark Mode Manager Script --}}
    <script>
      (function() {
        var headerToggle = document.getElementById('header-theme-toggle');
        var profileSwitch = document.getElementById('profile-dark-mode-switch');
        var moonIcon = document.getElementById('header-theme-icon-moon');
        var sunIcon = document.getElementById('header-theme-icon-sun');

        function updateThemeUI(isDark) {
          if (isDark) {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.documentElement.classList.add('dark-mode');
            if (document.body) {
              document.body.setAttribute('data-theme', 'dark');
              document.body.classList.add('dark-mode');
            }
            if (moonIcon) moonIcon.classList.add('d-none');
            if (sunIcon) sunIcon.classList.remove('d-none');
            if (profileSwitch) profileSwitch.checked = true;
          } else {
            document.documentElement.setAttribute('data-theme', 'light');
            document.documentElement.classList.remove('dark-mode');
            if (document.body) {
              document.body.setAttribute('data-theme', 'light');
              document.body.classList.remove('dark-mode');
            }
            if (moonIcon) moonIcon.classList.remove('d-none');
            if (sunIcon) sunIcon.classList.add('d-none');
            if (profileSwitch) profileSwitch.checked = false;
          }
        }

        // Initialize UI state on page load
        var currentIsDark = document.documentElement.getAttribute('data-theme') === 'dark' || document.documentElement.classList.contains('dark-mode');
        updateThemeUI(currentIsDark);

        function toggleTheme() {
          var isDarkNow = document.documentElement.getAttribute('data-theme') === 'dark';
          var newIsDark = !isDarkNow;
          
          updateThemeUI(newIsDark);
          localStorage.setItem('admin_theme', newIsDark ? 'dark' : 'light');

          // Persist in Backend Database
          fetch("{{ route('admin.toggle_dark_mode') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: JSON.stringify({ dark_mode: newIsDark ? 1 : 0 })
          }).catch(function(err) {
            console.log('Theme sync error:', err);
          });
        }

        if (headerToggle) {
          headerToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleTheme();
          });
        }

        if (profileSwitch) {
          profileSwitch.addEventListener('change', function(e) {
            toggleTheme();
          });
        }
      })();
    </script>
    @yield('script')
  </body>
</html>
