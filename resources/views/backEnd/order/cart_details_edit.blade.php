@php
    $cartItems = Cart::instance('pos_shopping')->content();
    $subtotalNum = 0;
    $itemDiscounts = 0;
    foreach ($cartItems as $item) {
        $subtotalNum += ((float)$item->price * (int)$item->qty);
        $itemDiscounts += ((float)($item->options->product_discount ?? 0) * (int)$item->qty);
    }
    Session::put('product_discount', $itemDiscounts);

    $shippingNum = (float) (Session::get('pos_shipping') ?? 0);
    $couponDiscount = (float) (Session::get('pos_discount') ?? 0);
    $totalDiscount = $itemDiscounts + $couponDiscount;
    $grandTotal = max(0, ($subtotalNum + $shippingNum) - $totalDiscount);

    $orderId = request()->get('order_id');
    $advancePaid = 0;
    $dueAmount = $grandTotal;
    if ($orderId) {
        $paidAmount = \App\Models\Payment::where('order_id', $orderId)->sum('amount');
        if ($paidAmount > 0 && $paidAmount < $grandTotal) {
            $advancePaid = $paidAmount;
            $dueAmount = $grandTotal - $advancePaid;
        }
    }
@endphp
<tr>
    <td class="text-muted">পণ্য সাবটোটাল</td>
    <td class="text-end fw-semibold">৳{{ number_format($subtotalNum, 2) }}</td>
</tr>
@if($itemDiscounts > 0)
<tr>
    <td class="text-muted">পণ্য ছাড় (Item Discount)</td>
    <td class="text-end text-danger fw-semibold">−৳{{ number_format($itemDiscounts, 2) }}</td>
</tr>
@endif
@if($couponDiscount > 0)
<tr>
    <td class="text-muted">কুপন ডিস্কাউন্ট</td>
    <td class="text-end text-danger fw-semibold">−৳{{ number_format($couponDiscount, 2) }}</td>
</tr>
@endif
<tr>
    <td class="text-muted">ডেলিভারি চার্জ</td>
    <td class="text-end fw-semibold">৳{{ number_format($shippingNum, 2) }}</td>
</tr>
<tr class="border-top" style="border-top: 2px solid #e2e8f0 !important;">
    <td class="fw-bold" style="font-size: 15px;">সর্বমোট প্রদেয়</td>
    <td class="text-end fw-bold text-success" style="font-size: 18px;">৳{{ number_format($grandTotal, 2) }}</td>
</tr>
@if($advancePaid > 0)
<tr>
    <td class="text-muted">অগ্রিম পরিশোধ</td>
    <td class="text-end text-success fw-semibold">৳{{ number_format($advancePaid, 2) }}</td>
</tr>
<tr class="oe-summary-due">
    <td class="fw-bold text-danger">বাকি (Due)</td>
    <td class="text-end fw-bold text-danger">৳{{ number_format($dueAmount, 2) }}</td>
</tr>
@endif
