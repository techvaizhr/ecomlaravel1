<div class="container-fluid order-index-shell order-index-page">

    <div class="oi-page-header">
        <div>
            <h4>{{ $order_status->name }} অর্ডার @if(($order_status->orders_count ?? 0) > 0)<span class="oi-badge-count">{{ $order_status->orders_count }}</span>@endif</h4>
            <div class="oi-sub">অর্ডার তালিকা · বাল্ক অ্যাকশন · ফ্রড চেক</div>
        </div>
        <div class="oi-header-actions">
            <a href="{{ route('admin.order.create') }}" class="btn btn-sm oi-btn-primary">
                <i class="fas fa-plus me-1"></i> নতুন অর্ডার তৈরি করুন
            </a>
        </div>
    </div>

    <div class="oi-card">
        <div class="oi-card-head">
            <ul class="oi-status-nav">
                {{-- 1. All Orders (Slate Gray) --}}
                @php
                    $isAllActive = (request()->routeIs('admin.orders') && request()->route('slug') === 'all') || request()->is('admin/orders/all') || request()->is('order/all');
                @endphp
                <li>
                    <a href="{{ route('admin.orders', ['slug'=>'all']) }}" 
                       class="oi-status-pill pill-all {{ $isAllActive ? 'active' : '' }}"
                       style="{{ $isAllActive ? 'background:#0f172a; color:#fff; border-color:#020617; box-shadow:0 0 0 2px #fff, 0 0 0 4px #0f172a;' : 'background:#cbd5e1; color:#0f172a; border-color:#94a3b8;' }}">
                        <span class="pill-dot" style="background: {{ $isAllActive ? '#38bdf8' : '#334155' }};"></span>
                        <span>All Orders</span>
                        @if(($all_orders_count ?? 0) > 0)
                            <span class="pill-count" style="{{ $isAllActive ? 'background:rgba(255,255,255,0.25); color:#fff;' : 'background:#fff; color:#0f172a;' }}">{{ $all_orders_count }}</span>
                        @endif
                    </a>
                </li>

                {{-- 2. Reseller Orders (Lavender / Purple) --}}
                @php
                    $isResellerActive = request()->routeIs('admin.reseller-orders.*') || request()->is('admin/reseller-orders*');
                @endphp
                <li>
                    <a href="{{ route('admin.reseller-orders.index') }}" 
                       class="oi-status-pill pill-reseller {{ $isResellerActive ? 'active' : '' }}"
                       style="{{ $isResellerActive ? 'background:#7e22ce; color:#fff; border-color:#581c87; box-shadow:0 0 0 2px #fff, 0 0 0 4px #7e22ce;' : 'background:#e9d5ff; color:#581c87; border-color:#c084fc;' }}">
                        <span class="pill-dot" style="background: {{ $isResellerActive ? '#f0abfc' : '#7e22ce' }};"></span>
                        <span>Reseller</span>
                        @if(($reseller_orders_count ?? 0) > 0)
                            <span class="pill-count" style="{{ $isResellerActive ? 'background:rgba(255,255,255,0.25); color:#fff;' : 'background:#fff; color:#581c87;' }}">{{ $reseller_orders_count }}</span>
                        @endif
                    </a>
                </li>

                {{-- 3. Incomplete Orders (Yellow-Gold / Amber) --}}
                @php
                    $isIncompleteActive = request()->routeIs('admin.incomplete-orders.*') || request()->is('admin/incomplete-orders*');
                @endphp
                <li>
                    <a href="{{ route('admin.incomplete-orders.index') }}" 
                       class="oi-status-pill pill-incomplete {{ $isIncompleteActive ? 'active' : '' }}"
                       style="{{ $isIncompleteActive ? 'background:#d97706; color:#fff; border-color:#92400e; box-shadow:0 0 0 2px #fff, 0 0 0 4px #d97706;' : 'background:#fde68a; color:#78350f; border-color:#f59e0b;' }}">
                        <span class="pill-dot" style="background: {{ $isIncompleteActive ? '#fef08a' : '#b45309' }};"></span>
                        <span>Incomplete</span>
                        @if(($incomplete_orders_count ?? 0) > 0)
                            <span class="pill-count" style="{{ $isIncompleteActive ? 'background:rgba(255,255,255,0.25); color:#fff;' : 'background:#fff; color:#78350f;' }}">{{ $incomplete_orders_count }}</span>
                        @endif
                    </a>
                </li>

                {{-- 4-18. 15 System Order Statuses --}}
                @if(isset($orderstatus))
                    @foreach($orderstatus as $st)
                        @php
                            $isStActive = (request()->routeIs('admin.orders') && request()->route('slug') === $st->slug) || request()->is('admin/orders/'.$st->slug) || request()->is('order/'.$st->slug);
                            $stId = (int) ($st->id ?? 0);
                            
                            // Group Color Mapping
                            if (in_array($stId, [1, 2, 3, 4, 5])) {
                                // 1: New Order, 2: Hold, 3: Confirmed, 4: Packaging, 5: Courier Handover (Sky Blue)
                                $pillBg = $isStActive ? '#0284c7' : '#bae6fd';
                                $pillColor = $isStActive ? '#ffffff' : '#075985';
                                $pillBorder = $isStActive ? '#0369a1' : '#38bdf8';
                                $pillDot = $isStActive ? '#bae6fd' : '#0284c7';
                                $pillTheme = 'pill-pipeline';
                            } elseif ($stId === 6) {
                                // 6: In Courier (Teal / Aqua)
                                $pillBg = $isStActive ? '#0d9488' : '#99f6e4';
                                $pillColor = $isStActive ? '#ffffff' : '#134e4a';
                                $pillBorder = $isStActive ? '#115e59' : '#2dd4bf';
                                $pillDot = $isStActive ? '#a7f3d0' : '#0d9488';
                                $pillTheme = 'pill-incourier';
                            } elseif ($stId === 7) {
                                // 7: Delivered (Mint / Light Green)
                                $pillBg = $isStActive ? '#16a34a' : '#bbf7d0';
                                $pillColor = $isStActive ? '#ffffff' : '#14532d';
                                $pillBorder = $isStActive ? '#14532d' : '#4ade80';
                                $pillDot = $isStActive ? '#bbf7d0' : '#15803d';
                                $pillTheme = 'pill-delivered';
                            } elseif (in_array($stId, [8, 9, 10, 11])) {
                                // 8: Pending Partial, 9: Full Received, 10: Item Received, 11: Charge Only (Apricot Orange)
                                $pillBg = $isStActive ? '#ea580c' : '#fed7aa';
                                $pillColor = $isStActive ? '#ffffff' : '#7c2d12';
                                $pillBorder = $isStActive ? '#7c2d12' : '#fb923c';
                                $pillDot = $isStActive ? '#fde047' : '#ea580c';
                                $pillTheme = 'pill-partial';
                            } elseif (in_array($stId, [12, 13])) {
                                // 12: Pending Return, 13: Returned (Rose / Pink-Red)
                                $pillBg = $isStActive ? '#e11d48' : '#fecdd3';
                                $pillColor = $isStActive ? '#ffffff' : '#881337';
                                $pillBorder = $isStActive ? '#881337' : '#fb7185';
                                $pillDot = $isStActive ? '#fecdd3' : '#e11d48';
                                $pillTheme = 'pill-return';
                            } elseif ($stId === 14) {
                                // 14: Pre Order (Magenta / Light Purple)
                                $pillBg = $isStActive ? '#c026d3' : '#f5d0fe';
                                $pillColor = $isStActive ? '#ffffff' : '#701a75';
                                $pillBorder = $isStActive ? '#701a75' : '#e879f9';
                                $pillDot = $isStActive ? '#f5d0fe' : '#c026d3';
                                $pillTheme = 'pill-preorder';
                            } elseif ($stId === 15) {
                                // 15: Cancelled (Light Crimson Red)
                                $pillBg = $isStActive ? '#dc2626' : '#fecaca';
                                $pillColor = $isStActive ? '#ffffff' : '#7f1d1d';
                                $pillBorder = $isStActive ? '#7f1d1d' : '#f87171';
                                $pillDot = $isStActive ? '#fecaca' : '#dc2626';
                                $pillTheme = 'pill-cancelled';
                            } else {
                                $pillBg = $isStActive ? '#0284c7' : '#bae6fd';
                                $pillColor = $isStActive ? '#ffffff' : '#075985';
                                $pillBorder = $isStActive ? '#0369a1' : '#38bdf8';
                                $pillDot = $isStActive ? '#bae6fd' : '#0284c7';
                                $pillTheme = 'pill-pipeline';
                            }

                            $pillStyle = "background: {$pillBg}; color: {$pillColor}; border: 1px solid {$pillBorder};";
                            if ($isStActive) {
                                $pillStyle .= " box-shadow: 0 0 0 2px #fff, 0 0 0 4px {$pillBg}; transform: translateY(-1.5px);";
                            }
                        @endphp
                        <li>
                            <a href="{{ route('admin.orders', ['slug'=>$st->slug]) }}" 
                               class="oi-status-pill {{ $pillTheme }} {{ $isStActive ? 'active' : '' }}" 
                               style="{{ $pillStyle }}"
                               title="{{ $st->name }}">
                                <span class="pill-dot" style="background: {{ $pillDot }};"></span>
                                <span>{{ $st->name }}</span>
                                @if(($st->orders_count ?? 0) > 0)
                                    <span class="pill-count" style="{{ $isStActive ? 'background:rgba(255,255,255,0.25); color:#fff;' : 'background:#fff; color:'.$pillColor.';' }}">{{ $st->orders_count }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <div class="oi-card-body">
            <div class="oi-toolbar order-index-toolbar mb-3">
                {{-- 1. Search & Filter Section (Single Line) --}}
                <div class="oi-toolbar-search order-index-toolbar-search w-100">
                    <form class="oi-search-form order-search-form mb-0" method="GET">
                        <div class="oi-search-inner order-search-inner d-flex flex-wrap flex-sm-nowrap align-items-center gap-2 w-100">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="ইনভয়েস, ফোন খুঁজুন..." class="form-control flex-grow-1" style="min-width: 140px;">
                            <select name="traffic_source" class="form-select order-traffic-filter flex-shrink-0" aria-label="ট্র্যাফিক উৎস" style="width: auto; min-width: 125px;">
                                @foreach(isset($traffic_source_options) ? $traffic_source_options : ['' => 'সব ট্র্যাফিক'] as $tsVal => $tsLabel)
                                    <option value="{{ $tsVal }}" {{ (string) request('traffic_source', '') === (string) $tsVal ? 'selected' : '' }}>{{ $tsLabel }}</option>
                                @endforeach
                            </select>
                            <select name="per_page" class="form-select order-per-page-select flex-shrink-0" aria-label="প্রতি পেজে" onchange="this.form.submit()" style="width: auto; min-width: 105px;" title="প্রতি পেজে অর্ডারের সংখ্যা">
                                @php 
                                    $sessionPerVal = session('admin_order_per_page', session('admin_per_page', 10));
                                    $reqPerVal = request('per_page', $sessionPerVal);
                                @endphp
                                @foreach([10, 20, 50, 100, 200, 500] as $opt)
                                    <option value="{{ $opt }}" {{ (string)$reqPerVal === (string)$opt ? 'selected' : '' }}>{{ $opt }} ভিউ</option>
                                @endforeach
                                <option value="all" {{ (string)$reqPerVal === 'all' || (int)$reqPerVal >= 5000 ? 'selected' : '' }}>সকল ভিউ</option>
                            </select>
                            <button type="submit" class="btn oi-btn-primary flex-shrink-0"><i class="fas fa-search me-1"></i> খুঁজুন</button>
                        </div>
                    </form>
                </div>

                {{-- 2. Bulk Actions Bar (Always visible with quick action tools) --}}
                <div class="oi-bulk-actions-wrapper" id="bulkActionsWrapper">
                    <div class="p-2 px-3 rounded-3 bg-light border d-flex flex-wrap align-items-center justify-content-between gap-2 shadow-sm">
                        <div class="d-flex align-items-center gap-2 flex-shrink-0" id="selectedOrdersBadgeWrapper" style="display: none;">
                            <span class="badge bg-primary rounded-pill px-2.5 py-1.5" id="selectedOrdersBadge" style="font-size: 11.5px;">
                                <i class="fas fa-check-square me-1"></i> <span id="selectedOrdersCount">0</span> টি সিলেক্টেড
                            </span>
                        </div>
                        <div class="oi-bulk-actions-scroll flex-grow-1">
                            <ul class="oi-action-grid action2-btn d-flex flex-nowrap align-items-center gap-1.5 list-unstyled m-0">
                                <li><a data-bs-toggle="modal" data-bs-target="#asignUser" class="oi-btn-tool oi-btn-assign"><i class="fas fa-user-plus"></i> অ্যাসাইন</a></li>
                                <li><a data-bs-toggle="modal" data-bs-target="#changeStatus" class="oi-btn-tool oi-btn-status"><i class="fas fa-flag"></i> স্ট্যাটাস</a></li>
                                <li><a href="{{ route('admin.order.bulk_destroy') }}" class="oi-btn-tool oi-btn-delete order_delete"><i class="fas fa-trash-alt"></i> ডিলিট</a></li>
                                <li><a href="{{ route('admin.order.order_print') }}" class="oi-btn-tool oi-btn-print multi_order_print"><i class="fas fa-print"></i> প্রিন্ট</a></li>
                                <li><a href="{{ route('admin.order.order_print') }}" class="oi-btn-tool oi-btn-label multi_label_print"><i class="fas fa-tag"></i> লেবেল</a></li>
                                @php
                                    $activeCouriers = [];
                                    if (isset($carrybee_info) && $carrybee_info) $activeCouriers['carrybee'] = 'Carrybee';
                                    if (isset($steadfast) && $steadfast) $activeCouriers['steadfast'] = 'Steadfast';
                                    if (isset($pathao_info) && $pathao_info) $activeCouriers['pathao'] = 'Pathao';
                                    if (isset($redx_info) && $redx_info) $activeCouriers['redx'] = 'RedX';

                                    $courierBtnText = 'Courier Booking';
                                    $defaultCourierCode = '';
                                    if (count($activeCouriers) === 1) {
                                        $defaultCourierCode = array_key_first($activeCouriers);
                                        $courierBtnText = reset($activeCouriers) . ' Booking';
                                    }
                                @endphp
                                @if(count($activeCouriers) > 0)
                                <li>
                                    <a href="javascript:void(0);" class="oi-btn-tool oi-btn-courier open-universal-courier-btn" data-courier="{{ $defaultCourierCode }}" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; font-weight: 700;">
                                        <i class="fas fa-shipping-fast text-primary"></i> {{ $courierBtnText }}
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <p class="d-lg-none oi-scroll-hint" role="note"><i class="fas fa-arrows-alt-h me-1"></i> বাকি কলাম দেখতে বাম–ডানে স্লাইড করুন</p>

            <div class="oi-table-rail order-table-rail table-responsive" role="region" aria-label="অর্ডার টেবিল">
                <table id="datatable-buttons" class="table oi-table order-index-table w-100 mb-0">
                    <thead>
                        <tr>
                            <th style="width: 42px; text-align: center; padding-left: 2px; padding-right: 2px;"><input type="checkbox" class="form-check-input checkall" value="" aria-label="সব সিলেক্ট"></th>
                            <th class="text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 4px;">ইনভয়েস</th>
                            <th class="text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 4px;">তারিখ</th>
                            <th style="padding-left: 6px; padding-right: 6px;">গ্রাহক</th>
                            <th style="padding-left: 6px; padding-right: 6px;">পণ্য</th>
                            <th class="text-end text-nowrap" style="width: 1%; padding-left: 6px; padding-right: 4px;">পরিমাণ</th>
                            <th class="text-center text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 4px;">স্ট্যাটাস</th>
                            <th class="text-center text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 2px;">ফ্রড চেক</th>
                        </tr>
                    </thead>
