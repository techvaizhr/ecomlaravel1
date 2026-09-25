<div class="container-fluid order-index-shell order-index-page">

    <div class="oi-page-header">
        <div>
            <h4>{{ $order_status->name }} অর্ডার <span class="oi-badge-count">{{ $order_status->orders_count }}</span></h4>
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
            <h6><i class="fas fa-list-alt"></i> অর্ডার তালিকা</h6>
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

                {{-- 2. Bulk Actions Bar (Hidden by default, appears right under search when orders are selected) --}}
                <div class="oi-bulk-actions-wrapper" id="bulkActionsWrapper" style="display: none;">
                    <div class="p-2 px-3 rounded-3 bg-light border d-flex flex-wrap align-items-center justify-content-between gap-2 shadow-sm">
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
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
                                @if($steadfast)
                                    <li><a href="{{ route('admin.bulk_courier', 'steadfast') }}?status=5" class="oi-btn-tool oi-btn-courier multi_order_courier"><i class="fas fa-truck"></i> Steadfast</a></li>
                                @endif
                                @if($pathao_info)
                                    <li><a data-bs-toggle="modal" data-bs-target="#pathao" class="oi-btn-tool oi-btn-pathao"><i class="fas fa-truck"></i> Pathao</a></li>
                                @endif
                                @if(isset($redx_info) && $redx_info)
                                    <li><a href="{{ route('admin.bulk_courier', 'redx') }}?status=5" class="oi-btn-tool oi-btn-redx multi_order_courier"><i class="fas fa-truck"></i> RedX</a></li>
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
                            <th style="width: 45px; text-align: center;"><input type="checkbox" class="form-check-input checkall" value="" aria-label="সব সিলেক্ট"></th>
                            <th style="width: 110px;">ইনভয়েস</th>
                            <th style="width: 100px;">তারিখ</th>
                            <th style="min-width: 170px;">গ্রাহক</th>
                            <th style="min-width: 160px;">পণ্য</th>
                            <th class="text-end text-nowrap" style="width: 1%; padding-left: 6px; padding-right: 4px;">পরিমাণ</th>
                            <th class="text-center text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 4px;">স্ট্যাটাস</th>
                            <th class="text-center text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 2px;">ফ্রড চেক</th>
                        </tr>
                    </thead>
