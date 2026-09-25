@extends('frontEnd.layouts.master')
@section('title','Invoice #' . $order->invoice_id)
@section('content')

@php
    // ১. পেমেন্ট ইনফো নেওয়া
    $payment = \App\Models\Payment::where('order_id', $order->id)->orderBy('id','desc')->first();

    $gateway_status = $payment ? strtolower(trim($payment->payment_status)) : ''; 
    $payment_method = $payment ? strtolower(trim($payment->payment_method)) : strtolower(trim($order->payment_method ?? ''));
    
    $admin_status   = strtolower(trim($order->payment_status ?? ''));
    $order_status   = strtolower(trim($order->status ?? ''));

    $grand_total = $order->amount;
    $paid_amount = 0;

    // পেমেন্ট রেকর্ড থেকে আসল টাকাটা বের করি
    if ($payment && !in_array($gateway_status, ['failed','cancel','cancelled','rejected'])) {
        $paid_amount = $payment->amount;
    }

    // COD FIX
    $is_cod = in_array($payment_method, ['cod','cash','cash_on_delivery','hand cash','hand_cash']);

    $is_order_completed =
        in_array($order_status, ['completed','delivered']) ||
        in_array($admin_status, ['completed','delivered']);

    if ($is_cod && !$is_order_completed) {
        if ($paid_amount >= $grand_total) {
            $paid_amount = 0;
        }
    }

    // ADMIN PRIORITY
    if ($is_order_completed) {
        $paid_amount = $grand_total;
    }
    elseif (($paid_amount == 0 || !$payment) && in_array($admin_status, ['paid','success','approved'])) {
        $paid_amount = $grand_total;
    }

    // Due
    $due_amount = max(0, $grand_total - $paid_amount);
    $subtotal = ($order->amount + $order->discount) - $order->shipping_charge;

    // ⭐ ডিজিটাল ডাউনলোড লজিক — যদি ফুল পেইড হয় তবেই ডাউনলোড লিংক দেখাবে
    $is_fully_paid = ($paid_amount >= $grand_total);
    $downloads = $is_fully_paid ? \App\Models\DigitalDownload::where('order_id', $order->id)->get() : collect();
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;600;700&display=swap');

    .invoice-wrapper { background: #f1f5f9; padding: 30px 15px; font-family: 'Plus Jakarta Sans', 'Hind Siliguri', sans-serif; }
    .success-hero-card {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #fff;
        max-width: 850px;
        margin: 40px auto 25px;
        position: relative;
        border-radius: 16px;
        padding: 48px 24px 28px !important;
        box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.25);
    }
    .success-icon-floating {
        position: absolute;
        top: -36px;
        left: 50%;
        transform: translateX(-50%);
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: 4px solid #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #ffffff;
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.35), 0 0 0 4px rgba(255, 255, 255, 0.25);
        z-index: 5;
    }
    #invoice-pdf-area { 
        background: #fff; 
        max-width: 850px; 
        margin: 0 auto; 
        border-radius: 14px; 
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08); 
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .inv-container { padding: 40px; }
    .inv-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; }
    .inv-logo img { max-width: 170px; max-height: 60px; object-fit: contain; margin-bottom: 12px; }
    .inv-title h1 { font-size: 30px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: 0.5px; }
    .inv-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px; margin-bottom: 25px; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; }
    .info-label { font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 6px; }
    .info-val { font-size: 14px; color: #1e293b; line-height: 1.6; }
    .table-responsive { margin: 25px 0; }
    .inv-table { width: 100%; border-collapse: collapse; }
    .inv-table th { background: #0f172a; color: #ffffff; padding: 12px 14px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .inv-table th:first-child { border-top-left-radius: 6px; }
    .inv-table th:last-child { border-top-right-radius: 6px; }
    .inv-table td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b; }
    .inv-table tbody tr:nth-child(even) { background-color: #fafbfc; }
    .sum-wrapper { display: flex; justify-content: flex-end; margin-top: 10px; }
    .sum-box { width: 100%; max-width: 350px; }
    .sum-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: #334155; }
    .total-row { border-top: 2px solid #0f172a; margin-top: 10px; padding-top: 14px; font-weight: 800; font-size: 19px; color: #0f172a; }
    .payment-badge-box { background: #0f172a; color: #fff; padding: 16px; border-radius: 10px; margin-top: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .status-tag { display: inline-block; padding: 5px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; margin-top: 8px; }
    .bg-paid-light { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .bg-due-light { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* ডিজিটাল আইটেম ডাউনলোড বক্স */
    .digital-download-box {
        max-width: 850px;
        margin: 20px auto;
        background: #f0f9ff;
        border: 1px dashed #0ea5e9;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .dl-btn {
        display: inline-block;
        background: #0284c7;
        color: #fff;
        padding: 10px 25px;
        border-radius: 50px;
        text-decoration: none !important;
        font-weight: 700;
        margin-top: 10px;
        transition: 0.3s;
    }
    .dl-btn:hover { background: #0369a1; transform: translateY(-2px); }

    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .invoice-wrapper { padding: 0; background: #fff; }
        #invoice-pdf-area { box-shadow: none; border: none; width: 100%; max-width: 100%; border-radius: 0; }
        .inv-container { padding: 15px; }
    }
</style>

<div class="invoice-wrapper">
    {{-- Celebration Banner --}}
    <div class="container no-print mb-4">
        <div class="success-hero-card text-center rounded-4 shadow-sm mb-4">
            <div class="success-icon-floating">
                <i class="fa fa-check text-white"></i>
            </div>
            <h3 class="fw-bold mb-2">অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে!</h3>
            <p class="mb-3 opacity-95 text-center mx-auto" style="font-size: 16px; line-height: 1.6; max-width: 680px;">ধন্যবাদ! আপনার ইনভয়েস নম্বর <strong>#{{$order->invoice_id}}</strong>। আমাদের প্রতিনিধি খুব শীঘ্রই আপনার ঠিকানায় পণ্যটি প্রেরণের ব্যবস্থা করবেন।</p>
            <div class="d-flex justify-content-center flex-wrap gap-2 pt-1">
                <a href="{{ url('/') }}" class="btn btn-light btn-sm rounded-pill px-4 fw-bold text-success shadow-sm">
                   <i class="fa fa-shopping-bag me-1"></i> কেনাকাটা চালিয়ে যান
                </a>
                <a href="{{ route('customer.order_track') }}?phone={{ $order->shipping?->phone }}&invoice_id={{ $order->invoice_id }}" class="btn btn-outline-light btn-sm rounded-pill px-4 fw-bold">
                   <i class="fa fa-truck me-1"></i> অর্ডার ট্র্যাক করুন
                </a>
                <button onclick="downloadPDF()" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold shadow-sm text-dark">
                    <i class="fa fa-download me-1"></i> ডাউনলোড ইনভয়েস (PDF)
                </button>
            </div>
        </div>
    </div>

    {{-- ⭐ ডিজিটাল আইটেম ডাউনলোড সেকশন (শুধুমাত্র পেইড হলে দেখাবে) ⭐ --}}
    @if($is_fully_paid && $downloads->count() > 0)
    <div class="digital-download-box no-print">
        <h6 class="fw-bold text-dark mb-1"><i class="fa fa-cloud-download me-2"></i> ডিজিটাল আইটেম প্রস্তুত!</h6>
        <p class="small text-muted mb-3">পেমেন্ট সফল হওয়ায় আপনার ফাইলগুলো ডাউনলোডের জন্য উন্মুক্ত করা হয়েছে।</p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            @foreach($downloads as $dl)
                <a href="{{ route('digital.download', $dl->token) }}" class="dl-btn">
                    Download: {{ $dl->product->name }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <div id="invoice-pdf-area">
        <div class="inv-container">
            <div class="inv-header">
                <div class="inv-logo">
                    <img src="{{asset($generalsetting->white_logo)}}" alt="Logo">
                    <div class="info-val">
                        <strong class="text-dark">{{$generalsetting->name}}</strong><br>
                        <span class="text-muted small">{{$contact->address}}</span><br>
                        <span class="text-muted small">হটলাইন: {{$contact->phone}}</span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="inv-title"><h1>INVOICE</h1></div>
                    <p class="mb-0 fw-bold fs-5 text-primary">#{{$order->invoice_id}}</p>
                    <p class="text-muted small mb-0">তারিখ: {{$order->created_at->format('d M, Y')}}</p>
                    <p class="text-muted small">সময়: {{$order->created_at->format('h:i A')}}</p>
                </div>
            </div>

            <div class="inv-grid">
                <div>
                    <span class="info-label">Customer Details</span>
                    <div class="info-val">
                        <strong class="d-block mb-1 fs-6 text-dark">{{$order->shipping ? $order->shipping->name : 'N/A'}}</strong>
                        <span class="d-block text-secondary"><i class="fa fa-phone me-1 small"></i> {{$order->shipping ? $order->shipping->phone : ''}}</span>
                        @php
                            $successAddr = trim($order->shipping->address ?? '');
                            $successArea = trim($order->shipping->area ?? '');
                            $successFullAddr = implode(', ', array_filter([$successAddr, $successArea]));
                        @endphp
                        @if($successFullAddr)
                            <span class="d-block text-secondary mt-1"><i class="fa fa-map-marker-alt me-1 small text-danger"></i> {{ $successFullAddr }}</span>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <span class="info-label">Payment Information</span>
                    <div class="info-val text-uppercase fw-bold fs-6 text-dark">{{ $payment_method }}</div>
                    <div>
                        <span class="status-tag {{ $paid_amount >= $grand_total ? 'bg-paid-light' : 'bg-due-light' }}">
                            {{ $paid_amount >= $grand_total ? '✔ Verified Paid' : '✘ Payment Outstanding' }}
                        </span>
                    </div>
                    @if($order->order_status == 1)
                        <div class="mt-2"><span class="badge bg-primary px-3 py-1 rounded-pill">অর্ডার প্রক্রিয়াধীন</span></div>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>Product Description</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderdetails as $item)
                        <tr>
                            <td>
                                <span class="fw-bold d-block">{{$item->product_name}}</span>
                                @php
                                    $sizeDisplay = $item->size ? ($item->size->sizeName ?? $item->size->size_name ?? $item->size->name ?? null) : null;
                                    $colorDisplay = $item->color ? ($item->color->getDisplayName() ?? $item->color->colorName ?? $item->color->color_name ?? $item->color->name ?? null) : null;
                                    if (!$sizeDisplay && $item->product_size) {
                                        $s = \App\Models\Size::find($item->product_size);
                                        $sizeDisplay = $s ? ($s->sizeName ?? $s->size_name ?? null) : null;
                                    }
                                    if (!$colorDisplay && $item->product_color) {
                                        $c = \App\Models\Color::find($item->product_color);
                                        $colorDisplay = $c ? ($c->getDisplayName() ?? $c->colorName ?? $c->color_name ?? null) : null;
                                    }
                                @endphp
                                @if($sizeDisplay)<small class="text-muted d-block">Size: {{ $sizeDisplay }}</small>@endif
                                @if($colorDisplay)<small class="text-muted d-block">Color: {{ $colorDisplay }}</small>@endif
                            </td>
                            <td class="text-center">৳{{number_format($item->sale_price, 0)}}</td>
                            <td class="text-center">{{$item->qty}}</td>
                            <td class="text-end fw-bold">৳{{number_format($item->sale_price * $item->qty, 0)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="sum-wrapper">
                <div class="sum-box">
                    <div class="sum-row">
                        <span class="text-muted">Subtotal</span>
                        <span>৳{{number_format($subtotal, 0)}}</span>
                    </div>
                    <div class="sum-row">
                        <span class="text-muted">Shipping Fee</span>
                        <span>৳{{number_format($order->shipping_charge, 0)}}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="sum-row text-danger">
                        <span>Discount</span>
                        <span>-৳{{number_format($order->discount, 0)}}</span>
                    </div>
                    @endif

                    <div class="sum-row total-row">
                        <span>Grand Total</span>
                        <span>৳{{number_format($grand_total, 0)}}</span>
                    </div>

                    <div class="payment-badge-box">
                        <div class="sum-row border-0 p-0 mb-2">
                            <span style="color: #4ade80;">Paid Amount</span>
                            <span class="fw-bold" style="color: #ffffff;">৳{{ number_format($paid_amount, 0) }}</span>
                        </div>
                        <div class="sum-row border-0 p-0">
                            <span class="fw-bold" style="color: #ff4d4d;">Remaining Due</span>
                            <span class="fw-bold" style="color: #ff4d4d; font-size: 1.1em;">৳{{ number_format($due_amount, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 text-center border-top">
                <p class="small text-muted mb-0">Thank you for your business! This is a computer-generated invoice.</p>
                <p class="fw-bold small text-uppercase mt-1">{{$generalsetting->name}}</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
@php
    $purchaseItems = [];
    foreach ($order->orderdetails as $item) {
        $purchaseItems[] = [
            'id'       => (string) ($item->product_id ?? $item->id),
            'name'     => $item->product_name,
            'price'    => (float) $item->sale_price,
            'qty'      => (int) $item->qty,
        ];
    }
    $purchaseTrackingUser = \App\Support\EcommerceTrackingUser::fromOrder($order);
@endphp
<script>
(function () {
    function firePurchase() {
        if (typeof window.EcomTracking === 'undefined') return;

        var orderId = '{{ $order->invoice_id }}';
        var storageKey = 'purchase_fired_' + orderId;
        if (localStorage.getItem(storageKey)) return;
        localStorage.setItem(storageKey, '1');

        var user = @json($purchaseTrackingUser);
        user.fbp = window.EcomTracking.getCookie('_fbp');
        user.fbc = window.EcomTracking.getCookie('_fbc') || window.EcomTracking.getCookie('fbc');
        user.ttclid = window.EcomTracking.getCookie('ttclid');
        user.address = user.address || @json($order->shipping?->address ?? '');
        user.area = user.area || user.city || @json($order->shipping?->area ?? '');

        window.EcomTracking.purchase({
            transaction_id: orderId,
            order_id: orderId,
            event_id: 'purchase_' + orderId,
            value: parseFloat("{{ $order->amount }}") || 0,
            shipping: parseFloat("{{ $order->shipping_charge }}") || 0,
            tax: 0,
            coupon: @json($order->coupon_code),
            payment_method: @json($payment_method),
            items: @json($purchaseItems),
            user: user,
            order_info: {
                invoice_id: orderId,
                order_id: '{{ $order->id }}',
                payment_method: @json($payment_method),
                payment_status: @json($payment ? $payment->payment_status : ''),
                grand_total: parseFloat("{{ $order->amount }}") || 0,
                shipping: parseFloat("{{ $order->shipping_charge }}") || 0,
                discount: parseFloat("{{ $order->discount }}") || 0,
                item_count: @json(count($purchaseItems))
            }
        });
    }

    // Fire immediately without waiting for DOMContentLoaded (since EcomTracking is initialized in <head>)
    if (typeof window.EcomTracking !== 'undefined') {
        firePurchase();
    } else {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', firePurchase);
        } else {
            firePurchase();
        }
        setTimeout(firePurchase, 200);
    }
})();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF() {
    const element = document.getElementById('invoice-pdf-area');
    const invoice_id = "{{ $order->invoice_id }}";
    const opt = {
        margin: [10, 10, 10, 10],
        filename: 'Invoice-' + invoice_id + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
@endpush