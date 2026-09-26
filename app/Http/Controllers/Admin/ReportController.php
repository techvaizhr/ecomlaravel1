<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Expense;
use App\Support\CourierStatusMapping;

class ReportController extends Controller
{
    /**
     * Common date range helper
     * Supports: today, yesterday, last_7_days, this_month, last_month, this_year, range, all
     */
    protected function getDateRange(Request $request): array
    {
        $type = $request->get('type', 'this_month');
        $now  = Carbon::now();
        $from = null;
        $to   = null;
        $label = '';

        switch ($type) {
            case 'today':
                $from  = $now->copy()->startOfDay();
                $to    = $now->copy()->endOfDay();
                $label = 'আজ (' . $now->format('d M, Y') . ')';
                break;

            case 'yesterday':
                $from  = $now->copy()->subDay()->startOfDay();
                $to    = $now->copy()->subDay()->endOfDay();
                $label = 'গতকাল (' . $from->format('d M, Y') . ')';
                break;

            case 'last_7_days':
                $from  = $now->copy()->subDays(6)->startOfDay();
                $to    = $now->copy()->endOfDay();
                $label = 'গত ৭ দিন (' . $from->format('d M') . ' - ' . $to->format('d M, Y') . ')';
                break;

            case 'last_month':
                $from  = $now->copy()->subMonth()->startOfMonth();
                $to    = $now->copy()->subMonth()->endOfMonth();
                $label = 'গত মাস (' . $from->format('F Y') . ')';
                break;

            case 'this_year':
                $from  = $now->copy()->startOfYear();
                $to    = $now->copy()->endOfYear();
                $label = 'চলতি বছর (' . $from->format('Y') . ')';
                break;

            case 'year':
                $year  = (int) $request->get('year', $now->year);
                $from  = Carbon::create($year, 1, 1)->startOfDay();
                $to    = Carbon::create($year, 12, 31)->endOfDay();
                $label = 'বছর - ' . $year;
                break;

            case 'month':
                $year  = (int) $request->get('year', $now->year);
                $month = (int) $request->get('month', $now->month);
                $from  = Carbon::create($year, $month, 1)->startOfDay();
                $to    = $from->copy()->endOfMonth();
                $label = 'মাস - ' . $from->format('F Y');
                break;

            case 'range':
                $fromInput = $request->get('from_date');
                $toInput   = $request->get('to_date');

                $from = $fromInput
                    ? Carbon::parse($fromInput)->startOfDay()
                    : $now->copy()->startOfMonth();

                $to = $toInput
                    ? Carbon::parse($toInput)->endOfDay()
                    : $now->copy()->endOfDay();

                $label = $from->format('d M, Y') . ' থেকে ' . $to->format('d M, Y');
                break;

            case 'all':
                $from  = Carbon::create(2020, 1, 1)->startOfDay();
                $to    = $now->copy()->endOfDay();
                $label = 'সর্বমোট (শুরু থেকে বর্তমান)';
                break;

            case 'this_month':
            default:
                $type  = 'this_month';
                $from  = $now->copy()->startOfMonth();
                $to    = $now->copy()->endOfMonth();
                $label = 'চলতি মাস (' . $from->format('F Y') . ')';
                break;
        }

        return [$from, $to, $label, $type];
    }

    /**
     * Resolve effective order revenue considering 15 statuses and partial settlements
     */
    protected function resolveOrderEffectiveRevenue(Order $order): float
    {
        $statusId = (int) $order->order_status;

        // Status 7: Delivered -> Full amount
        if ($statusId === CourierStatusMapping::STATUS_DELIVERED) {
            return (float) ($order->amount ?? 0);
        }

        // Status 9: Partial Full Received -> Collected amount or full amount
        if ($statusId === CourierStatusMapping::STATUS_PARTIAL_FULL_RECEIVED) {
            return (float) (($order->partial_collected_amount > 0) ? $order->partial_collected_amount : ($order->amount ?? 0));
        }

        // Status 10: Partial Item Received -> Collected amount or calculated item total
        if ($statusId === CourierStatusMapping::STATUS_PARTIAL_ITEM_RECEIVED) {
            if ($order->partial_collected_amount > 0) {
                return (float) $order->partial_collected_amount;
            }
            $deliveredItemSum = 0;
            if ($order->relationLoaded('orderdetails')) {
                foreach ($order->orderdetails as $od) {
                    $qty = $od->delivered_qty !== null ? $od->delivered_qty : $od->qty;
                    $deliveredItemSum += ((float)$od->sale_price * $qty);
                }
            }
            return (float) ($deliveredItemSum + ($order->shipping_charge ?? 0));
        }

        // Status 11: Partial Delivery Charge Only -> Delivery charge collected
        if ($statusId === CourierStatusMapping::STATUS_PARTIAL_CHARGE_ONLY) {
            return (float) (($order->partial_collected_amount > 0) ? $order->partial_collected_amount : ($order->shipping_charge ?? 0));
        }

        // Status 13 (Returned) / 15 (Cancelled) -> 0 Revenue
        if (in_array($statusId, [CourierStatusMapping::STATUS_RETURNED, CourierStatusMapping::STATUS_CANCELLED], true)) {
            return 0.0;
        }

        // Active pending statuses -> Face amount
        return (float) ($order->amount ?? 0);
    }

    /* =========================================================================
     *  1. ORDER REPORT (Comprehensive Analysis with 15 Statuses & Couriers)
     * ========================================================================= */
    public function orders(Request $request)
    {
        [$from, $to, $label, $type] = $this->getDateRange($request);

        // Base query with eager-loads
        $query = Order::query()
            ->with(['customer', 'status', 'shipping', 'payment', 'orderdetails.product', 'orderdetails.image']);

        // 1. Date Range
        if ($type !== 'all' && $from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        // 2. Status Filter
        $statusFilter = $request->get('order_status');
        if ($statusFilter !== null && $statusFilter !== '' && $statusFilter !== 'all') {
            if ($statusFilter === 'delivered_group') {
                $query->whereIn('order_status', CourierStatusMapping::DELIVERED_GROUP_STATUSES);
            } elseif ($statusFilter === 'returned_group') {
                $query->whereIn('order_status', CourierStatusMapping::RETURNED_GROUP_STATUSES);
            } elseif ($statusFilter === 'pending_action_group') {
                $query->whereIn('order_status', [CourierStatusMapping::STATUS_PENDING_PARTIAL, CourierStatusMapping::STATUS_PENDING_RETURN]);
            } elseif ($statusFilter === 'in_courier_group') {
                $query->whereIn('order_status', [CourierStatusMapping::STATUS_COURIER_HANDOVER, CourierStatusMapping::STATUS_IN_COURIER]);
            } elseif (is_numeric($statusFilter)) {
                $query->where('order_status', (int) $statusFilter);
            }
        }

        // 3. Courier Filter
        $courierFilter = strtolower(trim((string) $request->get('courier')));
        if ($courierFilter !== '' && $courierFilter !== 'all') {
            if ($courierFilter === 'steadfast') {
                $query->where(function ($q) {
                    $q->where('courier', 'LIKE', '%steadfast%')
                      ->orWhereNotNull('steadfast_consignment_id')
                      ->orWhere('courier_tracking_id', 'LIKE', 'CID%');
                });
            } elseif ($courierFilter === 'pathao') {
                $query->where(function ($q) {
                    $q->where('courier', 'LIKE', '%pathao%')
                      ->orWhereNotNull('pathao_consignment_id');
                });
            } elseif ($courierFilter === 'carrybee') {
                $query->where(function ($q) {
                    $q->where('courier', 'LIKE', '%carrybee%')
                      ->orWhereNotNull('carrybee_consignment_id');
                });
            } elseif ($courierFilter === 'redx') {
                $query->where(function ($q) {
                    $q->where('courier', 'LIKE', '%redx%')
                      ->orWhereNotNull('redx_tracking_id');
                });
            } elseif ($courierFilter === 'none') {
                $query->whereNull('courier')->whereNull('courier_tracking_id');
            }
        }

        // 4. Keyword Search (Invoice, Phone, Name)
        $search = trim((string) $request->get('search'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_id', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhere('courier_tracking_id', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('shipping', function ($sq) use ($search) {
                      $sq->where('phone', 'LIKE', "%{$search}%")
                         ->orWhere('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('phone', 'LIKE', "%{$search}%")
                         ->orWhere('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // ── CALCULATE AGGREGATE METRICS ON FILTERED SET ──
        $allFilteredOrders = (clone $query)->get();

        $totalOrders   = $allFilteredOrders->count();
        $totalGMV      = $allFilteredOrders->sum('amount');
        $totalShipping = $allFilteredOrders->sum('shipping_charge');
        $totalDiscount = $allFilteredOrders->sum('discount');

        $deliveredOrders = $allFilteredOrders->whereIn('order_status', CourierStatusMapping::DELIVERED_GROUP_STATUSES);
        $deliveredCount  = $deliveredOrders->count();
        $deliveredRevenue = $deliveredOrders->sum(fn ($o) => $this->resolveOrderEffectiveRevenue($o));

        $returnedOrders  = $allFilteredOrders->whereIn('order_status', CourierStatusMapping::RETURNED_GROUP_STATUSES);
        $returnedCount   = $returnedOrders->count();

        $pendingActionOrders = $allFilteredOrders->whereIn('order_status', [CourierStatusMapping::STATUS_PENDING_PARTIAL, CourierStatusMapping::STATUS_PENDING_RETURN]);
        $pendingActionCount  = $pendingActionOrders->count();

        $inCourierOrders = $allFilteredOrders->whereIn('order_status', [CourierStatusMapping::STATUS_COURIER_HANDOVER, CourierStatusMapping::STATUS_IN_COURIER]);
        $inCourierCount  = $inCourierOrders->count();

        $resolvedTotalRevenue = $allFilteredOrders->sum(fn ($o) => $this->resolveOrderEffectiveRevenue($o));

        // Delivery Success Rate
        $deliveryBase = $deliveredCount + $returnedCount;
        $successRate  = $deliveryBase > 0 ? round(($deliveredCount / $deliveryBase) * 100, 1) : 0;

        // Status Breakdown for Analytics Chart / Badges
        $orderStatuses = OrderStatus::where('status', 1)->orderBy('id', 'ASC')->get();
        $statusBreakdown = [];
        foreach ($orderStatuses as $st) {
            $matching = $allFilteredOrders->where('order_status', $st->id);
            $statusBreakdown[$st->id] = [
                'id'      => $st->id,
                'name'    => $st->name,
                'slug'    => $st->slug,
                'count'   => $matching->count(),
                'amount'  => $matching->sum('amount'),
                'revenue' => $matching->sum(fn ($o) => $this->resolveOrderEffectiveRevenue($o)),
            ];
        }

        // Courier Breakdown
        $courierBreakdown = [
            'Steadfast' => $allFilteredOrders->filter(fn ($o) => stripos($o->courier ?? '', 'steadfast') !== false || !empty($o->steadfast_consignment_id) || str_starts_with($o->courier_tracking_id ?? '', 'CID'))->count(),
            'Pathao'    => $allFilteredOrders->filter(fn ($o) => stripos($o->courier ?? '', 'pathao') !== false || !empty($o->pathao_consignment_id))->count(),
            'Carrybee'  => $allFilteredOrders->filter(fn ($o) => stripos($o->courier ?? '', 'carrybee') !== false || !empty($o->carrybee_consignment_id))->count(),
            'RedX'      => $allFilteredOrders->filter(fn ($o) => stripos($o->courier ?? '', 'redx') !== false || !empty($o->redx_tracking_id))->count(),
            'Direct/Other' => $allFilteredOrders->filter(fn ($o) => empty($o->courier) && empty($o->courier_tracking_id))->count(),
        ];

        // CSV Export
        if ($request->get('export') === 'csv') {
            $fileName = 'order-report-' . now()->format('Ymd_His') . '.csv';
            $headers  = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $callback = function () use ($allFilteredOrders, $label) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel UTF-8
                fputcsv($handle, ['অর্ডার রিপোর্ট (Order Report)', $label]);
                fputcsv($handle, []);
                fputcsv($handle, [
                    'ইনভয়েস #', 'গ্রাহকের নাম', 'মোবাইল নম্বর', 'ঠিকানা',
                    'পণ্য বিবরণ ও পরিমাণ', 'মোট টাকার পরিমাণ', 'ডেলিভারি চার্জ',
                    'ডিসকাউন্ট', 'প্রকৃত আদায় (Net Revenue)', 'কুরিয়ার', 'ট্র্যাকিং আইডি', 'স্ট্যাটাস', 'তারিখ ও সময়'
                ]);

                foreach ($allFilteredOrders as $order) {
                    $customerName  = $order->shipping->name ?? ($order->customer->name ?? $order->customer_name ?? 'Guest');
                    $customerPhone = $order->shipping->phone ?? ($order->customer->phone ?? $order->phone ?? '');
                    $customerAddr  = $order->shipping->address ?? ($order->customer->address ?? '');

                    $itemsStr = '';
                    if ($order->orderdetails) {
                        $itemArr = [];
                        foreach ($order->orderdetails as $od) {
                            $qty = $od->delivered_qty !== null ? $od->delivered_qty : $od->qty;
                            $itemArr[] = ($od->product_name ?? 'পণ্য') . " (x{$qty})";
                        }
                        $itemsStr = implode(', ', $itemArr);
                    }

                    $netRev = $this->resolveOrderEffectiveRevenue($order);
                    $courierName = $order->courier ?: ($order->steadfast_consignment_id ? 'Steadfast' : ($order->pathao_consignment_id ? 'Pathao' : ($order->carrybee_consignment_id ? 'Carrybee' : ($order->redx_tracking_id ? 'RedX' : '-'))));
                    $trackingId  = $order->courier_tracking_id ?: ($order->steadfast_consignment_id ?: ($order->pathao_consignment_id ?: ($order->carrybee_consignment_id ?: ($order->redx_tracking_id ?: '-'))));
                    $statusName  = $order->status->name ?? 'স্ট্যাটাস #' . $order->order_status;

                    fputcsv($handle, [
                        $order->invoice_id ?: $order->id,
                        $customerName,
                        $customerPhone,
                        $customerAddr,
                        $itemsStr,
                        $order->amount ?? 0,
                        $order->shipping_charge ?? 0,
                        $order->discount ?? 0,
                        $netRev,
                        $courierName,
                        $trackingId,
                        $statusName,
                        optional($order->created_at)->format('Y-m-d h:i A'),
                    ]);
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Paginated records for table view
        $orders = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        return view('backEnd.reports.orders', compact(
            'orders',
            'from',
            'to',
            'label',
            'type',
            'totalOrders',
            'totalGMV',
            'resolvedTotalRevenue',
            'deliveredCount',
            'deliveredRevenue',
            'returnedCount',
            'pendingActionCount',
            'inCourierCount',
            'totalShipping',
            'totalDiscount',
            'successRate',
            'orderStatuses',
            'statusBreakdown',
            'courierBreakdown'
        ));
    }

    /* =========================================================================
     *  2. PROFIT & LOSS REPORT (Accurate COGS, Reseller Profit & Expense Analytics)
     * ========================================================================= */
    public function profitLoss(Request $request)
    {
        [$from, $to, $label, $type] = $this->getDateRange($request);

        // 1. DELIVERED SALES REVENUE (Only from Delivered Group: [7 Delivered, 9 Partial Full, 10 Partial Item])
        $deliveredStatuses = CourierStatusMapping::DELIVERED_GROUP_STATUSES;

        $deliveredOrdersQuery = Order::query()
            ->whereIn('order_status', $deliveredStatuses)
            ->with(['orderdetails.product', 'orderdetails.image']);

        if ($type !== 'all' && $from && $to) {
            $deliveredOrdersQuery->whereBetween('created_at', [$from, $to]);
        }

        $deliveredOrders = $deliveredOrdersQuery->get();

        // Placed Orders for comparison (GMV)
        $allOrdersQuery = Order::query();
        if ($type !== 'all' && $from && $to) {
            $allOrdersQuery->whereBetween('created_at', [$from, $to]);
        }
        $totalPlacedOrdersCount = (clone $allOrdersQuery)->count();
        $totalPlacedGMV         = (clone $allOrdersQuery)->sum('amount');

        // Revenue Breakdown
        $deliveredOrderCount = $deliveredOrders->count();
        $grossDeliveredSales = $deliveredOrders->sum('amount');
        $deliveredShippingIncome = $deliveredOrders->sum('shipping_charge');
        $deliveredDiscountGiven  = $deliveredOrders->sum('discount');

        // Net Sales Revenue (accounting for partial reductions)
        $salesRevenue = 0;
        foreach ($deliveredOrders as $order) {
            $salesRevenue += $this->resolveOrderEffectiveRevenue($order);
        }

        // Plus Delivery Charge Only Status 11 (Customer paid delivery fee)
        $chargeOnlyOrdersQuery = Order::where('order_status', CourierStatusMapping::STATUS_PARTIAL_CHARGE_ONLY);
        if ($type !== 'all' && $from && $to) {
            $chargeOnlyOrdersQuery->whereBetween('created_at', [$from, $to]);
        }
        $chargeOnlyRevenue = $chargeOnlyOrdersQuery->sum(fn ($o) => ($o->partial_collected_amount > 0 ? $o->partial_collected_amount : ($o->shipping_charge ?? 0)));
        $salesRevenue += $chargeOnlyRevenue;

        // 2. COGS (Cost of Goods Sold - পণ্যের ক্রয়মূল্য)
        // Calculated ONLY for items actually delivered to customer (returned items have stock restored to inventory)
        $cogs = 0;
        $totalDeliveredProductUnits = 0;
        $productPerformance = [];

        foreach ($deliveredOrders as $order) {
            foreach ($order->orderdetails as $od) {
                // If partial settlement specified delivered_qty, use that. Otherwise use total qty.
                $qty = ($od->delivered_qty !== null) ? (int) $od->delivered_qty : (int) ($od->qty ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $purchasePrice = $od->purchase_price !== null ? (float) $od->purchase_price : (float) ($od->product->purchase_price ?? 0);
                $salePrice     = (float) ($od->sale_price ?? 0);

                $itemCogs    = $purchasePrice * $qty;
                $itemRevenue = $salePrice * $qty;
                $itemProfit  = $itemRevenue - $itemCogs;

                $cogs += $itemCogs;
                $totalDeliveredProductUnits += $qty;

                // Track by Product ID / Name for Top Profitable items
                $prodId = $od->product_id ?: $od->product_name;
                if (!isset($productPerformance[$prodId])) {
                    $productPerformance[$prodId] = [
                        'id'             => $od->product_id,
                        'name'           => $od->product_name ?? optional($od->product)->name ?? 'পণ্য',
                        'image'          => ($od->image && $od->image->image) ? asset($od->image->image) : (($od->product && $od->product->image && $od->product->image->image) ? asset($od->product->image->image) : asset('public/uploads/default/no-image.png')),
                        'units_sold'     => 0,
                        'purchase_price' => $purchasePrice,
                        'avg_sale_price' => $salePrice,
                        'total_revenue'  => 0,
                        'total_cogs'     => 0,
                        'profit'         => 0,
                    ];
                }

                $productPerformance[$prodId]['units_sold']    += $qty;
                $productPerformance[$prodId]['total_revenue'] += $itemRevenue;
                $productPerformance[$prodId]['total_cogs']    += $itemCogs;
                $productPerformance[$prodId]['profit']        += $itemProfit;
            }
        }

        // Sort Top Profitable Products
        uasort($productPerformance, fn ($a, $b) => $b['profit'] <=> $a['profit']);
        $topProducts = array_slice($productPerformance, 0, 10, true);

        // 3. GROSS PROFIT & MARGIN
        $grossProfit = $salesRevenue - $cogs;
        $grossMargin = $salesRevenue > 0 ? round(($grossProfit / $salesRevenue) * 100, 2) : 0;

        // 4. OPERATING EXPENSES
        $expQuery = Expense::query();
        if ($type !== 'all' && $from && $to) {
            if (Schema::hasColumn('expenses', 'expense_date')) {
                $expQuery->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
            } else {
                $expQuery->whereBetween('created_at', [$from, $to]);
            }
        }
        $expenses = $expQuery->orderBy('id', 'desc')->get();
        $totalExpense = $expenses->sum('amount');

        // Expense by Category Breakdown
        $expenseCategories = $expenses->groupBy('category')->map(function ($items, $cat) {
            return [
                'category' => $cat ?: 'সাধারণ খরচ (General)',
                'count'    => $items->count(),
                'amount'   => $items->sum('amount'),
            ];
        })->sortByDesc('amount')->values()->all();

        // 5. RESELLER PROFIT / COMMISSIONS PAID
        $resellerProfit = (float) $deliveredOrders->sum('reseller_profit');

        // 6. NET PROFIT & NET MARGIN
        $netProfit = $grossProfit - $totalExpense - $resellerProfit;
        $netMargin = $salesRevenue > 0 ? round(($netProfit / $salesRevenue) * 100, 2) : 0;

        // CSV Export
        if ($request->get('export') === 'csv') {
            $fileName = 'profit-loss-report-' . now()->format('Ymd_His') . '.csv';
            $headers  = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $callback = function () use ($label, $salesRevenue, $cogs, $grossProfit, $grossMargin, $totalExpense, $resellerProfit, $netProfit, $netMargin, $deliveredOrderCount, $totalDeliveredProductUnits) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['লাভ ও ক্ষতি রিপোর্ট (Profit & Loss Statement)', $label]);
                fputcsv($handle, []);
                fputcsv($handle, ['আইটেম / হিসাব বিবরণী', 'টাকার পরিমাণ (BDT)']);
                fputcsv($handle, ['মোট ডেলিভার্ড অর্ডার সংখ্যা', $deliveredOrderCount]);
                fputcsv($handle, ['মোট বিক্রিত পণ্য ইউনিট', $totalDeliveredProductUnits]);
                fputcsv($handle, ['মোট বিক্রয় আয় (Net Sales Revenue)', $salesRevenue]);
                fputcsv($handle, ['পণ্যের ক্রয়মূল্য (COGS)', $cogs]);
                fputcsv($handle, ['মোট মুনাফা (Gross Profit)', $grossProfit]);
                fputcsv($handle, ['গ্রস মার্জিন (%)', $grossMargin . '%']);
                fputcsv($handle, ['দোকান/অফিস পরিচালন খরচ (Operating Expenses)', $totalExpense]);
                fputcsv($handle, ['রিসেলার কমিশন/লভ্যাংশ (Reseller Profit Paid)', $resellerProfit]);
                fputcsv($handle, ['নিট লাভ / ক্ষতি (Net Profit / Loss)', $netProfit]);
                fputcsv($handle, ['নিট মার্জিন (%)', $netMargin . '%']);
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('backEnd.reports.profit_loss', compact(
            'from',
            'to',
            'label',
            'type',
            'totalPlacedOrdersCount',
            'totalPlacedGMV',
            'deliveredOrderCount',
            'totalDeliveredProductUnits',
            'grossDeliveredSales',
            'deliveredShippingIncome',
            'deliveredDiscountGiven',
            'salesRevenue',
            'cogs',
            'grossProfit',
            'grossMargin',
            'totalExpense',
            'expenseCategories',
            'resellerProfit',
            'netProfit',
            'netMargin',
            'topProducts'
        ));
    }

    /* =========================================================================
     *  3. PURCHASE REPORT
     * ========================================================================= */
    public function purchases(Request $request)
    {
        [$from, $to, $label, $type] = $this->getDateRange($request);

        $query = Purchase::query()->with('supplier');

        if ($type !== 'all' && $from && $to) {
            if (Schema::hasColumn('purchases', 'purchase_date')) {
                $query->whereBetween('purchase_date', [$from->toDateString(), $to->toDateString()]);
            } else {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        $purchases = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        $totalPurchaseAmount = $purchases->sum(fn ($p) => (float)($p->total ?? $p->grand_total ?? $p->amount ?? 0));
        $totalPaid           = $purchases->sum(fn ($p) => (float)($p->paid ?? $p->paid_amount ?? 0));
        $totalDue            = $purchases->sum(fn ($p) => (float)($p->due ?? $p->due_amount ?? 0));

        return view('backEnd.reports.purchases', compact(
            'purchases',
            'from',
            'to',
            'label',
            'type',
            'totalPurchaseAmount',
            'totalPaid',
            'totalDue'
        ));
    }

    /* =========================================================================
     *  4. EXPENSE REPORT
     * ========================================================================= */
    public function expenses(Request $request)
    {
        [$from, $to, $label, $type] = $this->getDateRange($request);

        $query = Expense::query();

        if ($type !== 'all' && $from && $to) {
            if (Schema::hasColumn('expenses', 'expense_date')) {
                $query->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
            } else {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        $expenses     = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();
        $totalExpense = $expenses->sum('amount');

        return view('backEnd.reports.expenses', compact(
            'expenses',
            'from',
            'to',
            'label',
            'type',
            'totalExpense'
        ));
    }

    /* =========================================================================
     *  5. STOCK REPORT
     * ========================================================================= */
    public function stock(Request $request)
    {
        $query = Product::query()->with('category');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('sku', 'LIKE', '%' . $request->keyword . '%');
        }

        $products = $query->orderBy('stock', 'asc')->paginate(25)->withQueryString();

        $totalStockQty   = Product::sum('stock');
        $totalStockValue = Product::selectRaw('SUM(COALESCE(purchase_price, 0) * COALESCE(stock, 0)) as total_val')->value('total_val') ?? 0;
        $totalSaleValue  = Product::selectRaw('SUM(COALESCE(new_price, old_price, 0) * COALESCE(stock, 0)) as total_val')->value('total_val') ?? 0;

        return view('backEnd.reports.stock', compact(
            'products',
            'totalStockQty',
            'totalStockValue',
            'totalSaleValue'
        ));
    }
}
