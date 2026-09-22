@extends('backEnd.layouts.master')
@section('title','Customer Profile')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    .customer-profile-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .profile-hero {
        height: 120px;
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        position: relative;
    }
    .profile-avatar-wrap {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        margin: -50px auto 12px auto;
        overflow: hidden;
        background: #f8fafc;
        position: relative;
        z-index: 2;
    }
    .profile-avatar-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-name {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .profile-location {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 16px;
    }
    .quick-action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.12);
    }
    .btn-call { background: #dcfce7; color: #15803d; }
    .btn-call:hover { background: #15803d; color: #ffffff; }
    .btn-mail { background: #fee2e2; color: #b91c1c; }
    .btn-mail:hover { background: #b91c1c; color: #ffffff; }
    .btn-whatsapp { background: #e0e7ff; color: #4338ca; }
    .btn-whatsapp:hover { background: #4338ca; color: #ffffff; }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 11px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label { color: #64748b; font-weight: 500; }
    .info-row .val { color: #0f172a; font-weight: 600; text-align: right; max-width: 60%; word-break: break-word; }

    /* Stat Cards */
    .metric-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        transition: all 0.2s ease;
    }
    .metric-pill:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .metric-pill .metric-title {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
    }
    .metric-pill .metric-val {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
    }

    /* Orders Table */
    .order-table-card .card-header-styled {
        padding: 18px 24px;
        background: #fafbfe;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .order-table-card .card-header-styled h5 {
        margin: 0;
        font-weight: 700;
        font-size: 16px;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 16px;
    }
    .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .invoice-tag {
        font-weight: 700;
        color: #0ea5e9;
        background: rgba(14, 165, 233, 0.08);
        padding: 4px 8px;
        border-radius: 6px;
        text-decoration: none;
    }
    .invoice-tag:hover {
        background: #0ea5e9;
        color: #ffffff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    <!-- Top Action Bar -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h4 class="page-title mb-1" style="font-weight: 700; color: #0f172a;">Customer Profile</h4>
            <p class="text-muted mb-0 font-size-13">Detailed activity, lifetime value, and order history for {{ $profile->name }}.</p>
        </div>
        <div class="col-md-6 text-md-end mt-2 mt-md-0">
            <div class="d-inline-flex flex-wrap gap-2">
                <form method="post" action="{{route('customers.adminlog')}}" target="_blank" class="d-inline">
                    @csrf
                    <input type="hidden" value="{{$profile->id}}" name="hidden_id">        
                    <button type="submit" class="btn btn-info rounded-pill px-3 shadow-sm font-size-13" title="Login directly to front store as this customer">
                        <i class="fe-log-in me-1"></i> Login as Customer
                    </button>
                </form>
                <a href="{{ route('customers.edit', ['id' => $profile->id]) }}" class="btn btn-warning rounded-pill px-3 shadow-sm font-size-13 text-dark fw-semibold">
                    <i class="fe-edit me-1"></i> Edit Customer
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-light rounded-pill px-3 border shadow-sm font-size-13">
                    <i class="fe-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    @php
        $totalOrders = $profile->orders ? $profile->orders->count() : 0;
        $totalSpend = $profile->orders ? $profile->orders->sum('amount') : 0;
        $phoneClean = preg_replace('/[^0-9]/', '', $profile->phone ?? '');
    @endphp

    <div class="row g-3">
        <!-- Left: Customer Profile & Personal Info -->
        <div class="col-lg-4">
            <div class="customer-profile-card text-center mb-3">
                <div class="profile-hero"></div>
                <div class="p-4 pt-0">
                    <div class="profile-avatar-wrap">
                        @if($profile->image)
                            <img src="{{asset($profile->image)}}" alt="{{ $profile->name }}">
                        @else
                            <img src="{{asset('public/avatar-default.png')}}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($profile->name) }}&background=0ea5e9&color=fff&size=100'" alt="{{ $profile->name }}">
                        @endif
                    </div>

                    <h4 class="profile-name">{{$profile->name}}</h4>
                    <p class="profile-location">
                        <i class="fe-map-pin me-1 text-danger"></i> {{ $profile->area ?? ($profile->district ?? 'Bangladesh') }}
                    </p>

                    <!-- Quick Communication Buttons -->
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        @if($profile->phone)
                            <a href="tel:{{$profile->phone}}" class="quick-action-btn btn-call" title="Call Customer">
                                <i class="fe-phone"></i>
                            </a>
                            <a href="https://wa.me/{{ str_starts_with($phoneClean, '88') ? $phoneClean : '88' . $phoneClean }}" target="_blank" class="quick-action-btn btn-whatsapp" title="WhatsApp Message">
                                <i class="fe-message-circle"></i>
                            </a>
                        @endif
                        @if($profile->email)
                            <a href="mailto:{{$profile->email}}" class="quick-action-btn btn-mail" title="Send Email">
                                <i class="fe-mail"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Personal Information -->
                    <div class="text-start">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="text-uppercase text-muted font-size-11 fw-bold letter-spacing mb-0">Contact & Address</h6>
                            <span class="badge {{ $profile->status == 'active' || $profile->status == 1 ? 'bg-success' : 'bg-danger' }} rounded-pill font-size-11">
                                {{ $profile->status == 'active' || $profile->status == 1 ? 'Active Account' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="label"><i class="fe-phone me-1"></i> Phone:</span>
                            <span class="val">{{$profile->phone ?? 'N/A'}}</span>
                        </div>
                        <div class="info-row">
                            <span class="label"><i class="fe-mail me-1"></i> Email:</span>
                            <span class="val">{{$profile->email ?? 'N/A'}}</span>
                        </div>
                        <div class="info-row">
                            <span class="label"><i class="fe-map-pin me-1"></i> Address:</span>
                            <span class="val">{{$profile->address ?? 'N/A'}}</span>
                        </div>
                        <div class="info-row">
                            <span class="label"><i class="fe-navigation me-1"></i> District:</span>
                            <span class="val">{{$profile->district ?? 'N/A'}}</span>
                        </div>
                        <div class="info-row">
                            <span class="label"><i class="fe-globe me-1"></i> Upazila / Area:</span>
                            <span class="val">{{$profile->area ?? 'N/A'}}</span>
                        </div>
                        <div class="info-row">
                            <span class="label"><i class="fe-calendar me-1"></i> Joined:</span>
                            <span class="val">{{ $profile->created_at ? date('d M, Y', strtotime($profile->created_at)) : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Metrics & Order History -->
        <div class="col-lg-8">
            <!-- Metrics Row -->
            <div class="row g-2 mb-3">
                <div class="col-sm-6 col-md-4">
                    <div class="metric-pill">
                        <div class="metric-title">Lifetime Orders</div>
                        <div class="metric-val text-primary">{{ $totalOrders }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="metric-pill">
                        <div class="metric-title">Total Spend</div>
                        <div class="metric-val text-success">৳{{ number_format($totalSpend, 2) }}</div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="metric-pill">
                        <div class="metric-title">Avg. Order Value</div>
                        <div class="metric-val text-info">
                            ৳{{ $totalOrders > 0 ? number_format($totalSpend / $totalOrders, 2) : '0.00' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table Card -->
            <div class="customer-profile-card order-table-card">
                <div class="card-header-styled">
                    <h5><i class="fe-shopping-bag text-primary"></i> Order History</h5>
                    <span class="badge bg-light text-dark border px-2 py-1 font-size-12">{{ $totalOrders }} Orders Found</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-hover align-middle mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Date & Time</th>
                                    <th>Shipping</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($profile->orders as $key => $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="invoice-tag">#{{ $value->invoice_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark">{{ date('d M, Y', strtotime($value->created_at)) }}</span>
                                            <span class="text-muted font-size-11">{{ date('h:i A', strtotime($value->created_at)) }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $value->shipping ? $value->shipping->name : 'Standard Delivery' }}</td>
                                    <td>
                                        <span class="fw-bold text-dark font-size-14">৳{{ number_format($value->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusName = $value->status ? $value->status->name : 'Pending';
                                            $badgeBg = '#fef3c7'; $badgeColor = '#92400e';
                                            $lowerName = strtolower($statusName);
                                            if(str_contains($lowerName, 'deliver') || str_contains($lowerName, 'complete')) {
                                                $badgeBg = '#dcfce7'; $badgeColor = '#15803d';
                                            } elseif(str_contains($lowerName, 'cancel') || str_contains($lowerName, 'return')) {
                                                $badgeBg = '#fee2e2'; $badgeColor = '#b91c1c';
                                            } elseif(str_contains($lowerName, 'process') || str_contains($lowerName, 'courier')) {
                                                $badgeBg = '#e0e7ff'; $badgeColor = '#4338ca';
                                            }
                                        @endphp
                                        <span class="badge rounded-pill px-2 py-1 font-size-11" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-weight: 600;">
                                            {{ $statusName }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.order.invoice', ['invoice_id' => $value->invoice_id]) }}" class="btn btn-sm btn-light rounded border shadow-none px-2 py-1 font-size-12" title="View Invoice">
                                            <i class="fe-file-text"></i> Invoice
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fe-shopping-bag font-size-36 d-block mb-2 text-slate-300"></i>
                                        <p class="mb-0 font-size-14">No order history available for this customer yet.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#datatable-buttons')) {
            $('#datatable-buttons').DataTable().destroy();
        }
        $('#datatable-buttons').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search orders...",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    });
</script>
@endsection