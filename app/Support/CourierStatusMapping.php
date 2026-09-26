<?php

namespace App\Support;

/**
 * Universal Courier Status Mapper & Transition Manager
 * Maps Steadfast, Pathao, Carrybee, and RedX status events to the 15 system order statuses.
 */
class CourierStatusMapping
{
    // 15 System Order Status Constants
    public const STATUS_NEW_ORDER            = 1;
    public const STATUS_HOLD                 = 2;
    public const STATUS_CONFIRMED            = 3;
    public const STATUS_PACKAGING            = 4;
    public const STATUS_COURIER_HANDOVER     = 5;
    public const STATUS_IN_COURIER           = 6;
    public const STATUS_DELIVERED            = 7;
    public const STATUS_PENDING_PARTIAL      = 8;
    public const STATUS_PARTIAL_FULL_RECEIVED = 9;
    public const STATUS_PARTIAL_ITEM_RECEIVED = 10;
    public const STATUS_PARTIAL_CHARGE_ONLY  = 11;
    public const STATUS_PENDING_RETURN       = 12;
    public const STATUS_RETURNED             = 13;
    public const STATUS_PRE_ORDER            = 14;
    public const STATUS_CANCELLED            = 15;

    /**
     * Protected Statuses:
     * Webhook or background courier sync can NEVER auto-override orders in these statuses.
     */
    public const PROTECTED_STATUSES = [
        self::STATUS_HOLD,                  // 2
        self::STATUS_PARTIAL_FULL_RECEIVED, // 9
        self::STATUS_PARTIAL_ITEM_RECEIVED, // 10
        self::STATUS_PARTIAL_CHARGE_ONLY,   // 11
        self::STATUS_RETURNED,              // 13
        self::STATUS_PRE_ORDER,             // 14
        self::STATUS_CANCELLED,             // 15
    ];

    /**
     * Active stock holding statuses (stock is deducted):
     * 1 - New Order, 2 - Hold, 3 - Confirmed, 4 - Packaging, 5 - Courier Handover,
     * 6 - In Courier, 7 - Delivered, 8 - Pending Partial, 9 - Partial (Full Received),
     * 12 - Pending Return (parcel physically returning, verified upon Return).
     */
    public const STOCK_ACTIVE_STATUSES = [
        self::STATUS_NEW_ORDER,            // 1
        self::STATUS_HOLD,                 // 2
        self::STATUS_CONFIRMED,            // 3
        self::STATUS_PACKAGING,            // 4
        self::STATUS_COURIER_HANDOVER,     // 5
        self::STATUS_IN_COURIER,           // 6
        self::STATUS_DELIVERED,            // 7
        self::STATUS_PENDING_PARTIAL,      // 8
        self::STATUS_PARTIAL_FULL_RECEIVED,// 9
        self::STATUS_PENDING_RETURN,       // 12
    ];

    /**
     * Stock restoration statuses (all product stock returns to inventory):
     * 11 - Partial (Delivery Charge Only), 13 - Returned, 14 - Pre Order, 15 - Cancelled
     */
    public const STOCK_RESTORE_STATUSES = [
        self::STATUS_PARTIAL_CHARGE_ONLY,  // 11
        self::STATUS_RETURNED,             // 13
        self::STATUS_PRE_ORDER,            // 14
        self::STATUS_CANCELLED,            // 15
    ];

    /**
     * Delivered / Successful Sales statuses for counts, revenues & dashboards:
     * 7 - Delivered + 9 - Partial (Full Received) + 10 - Partial (Item Received)
     */
    public const DELIVERED_GROUP_STATUSES = [
        self::STATUS_DELIVERED,            // 7
        self::STATUS_PARTIAL_FULL_RECEIVED,// 9
        self::STATUS_PARTIAL_ITEM_RECEIVED,// 10
    ];

    /**
     * Returned statuses for reports and dashboards:
     * 13 - Returned + 10 - Partial (Item Received) + 11 - Partial (Delivery Charge Only)
     */
    public const RETURNED_GROUP_STATUSES = [
        self::STATUS_PARTIAL_ITEM_RECEIVED,// 10
        self::STATUS_PARTIAL_CHARGE_ONLY,  // 11
        self::STATUS_RETURNED,             // 13
    ];

    /**
     * Check if a status is locked against automatic courier webhook transitions.
     */
    public static function isProtected(int|string|null $statusId): bool
    {
        return in_array((int) $statusId, self::PROTECTED_STATUSES, true);
    }

    /**
     * Check if status is a delivered / completed sales state.
     */
    public static function isDelivered(int|string|null $statusId): bool
    {
        return in_array((int) $statusId, self::DELIVERED_GROUP_STATUSES, true);
    }

    /**
     * Check if status is fully cancelled or returned.
     */
    public static function isFinalCancelledOrReturned(int|string|null $statusId): bool
    {
        return in_array((int) $statusId, [self::STATUS_CANCELLED, self::STATUS_RETURNED, self::STATUS_PARTIAL_CHARGE_ONLY], true);
    }

    /**
     * Clean and normalize any status string (handles dashes, underscores, spaces, prefix dots).
     */
    public static function normalize(mixed $status): string
    {
        $s = strtolower(trim((string) $status));
        $s = preg_replace('/^order[\.\_\-\s]+/i', '', $s); // strip order. prefix
        $s = str_replace(['-', '_', '.'], ' ', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }

    /**
     * Map any courier event/status string to the 15 system order status IDs.
     */
    public static function map(string $courierType, mixed $rawStatus): ?int
    {
        $clean = self::normalize($rawStatus);
        if ($clean === '') {
            return null;
        }

        $type = strtolower(trim($courierType));

        return match ($type) {
            'steadfast' => self::mapSteadfast($clean),
            'pathao'    => self::mapPathao($clean),
            'carrybee'  => self::mapCarrybee($clean),
            'redx'      => self::mapRedX($clean),
            default     => self::mapGeneric($clean),
        };
    }

    /**
     * Steadfast Status Mapping:
     * - 6 (In Courier): pending, hold, in_transit, picked_up, in_review
     * - 7 (Delivered): delivered, delivered_approval_pending, delivery_success
     * - 8 (Pending Partial): partial_delivered, partial_delivered_approval_pending
     * - 12 (Pending Return): returned, return, cancelled, cancelled_approval_pending, delivery_failed
     */
    protected static function mapSteadfast(string $s): ?int
    {
        // 7: Delivered
        if (str_starts_with($s, 'delivered') || in_array($s, ['delivered', 'delivered approval pending', 'delivery success', 'successfully delivered'], true)) {
            return self::STATUS_DELIVERED;
        }

        // 8: Pending Partial
        if (str_contains($s, 'partial')) {
            return self::STATUS_PENDING_PARTIAL;
        }

        // 12: Pending Return
        if (str_contains($s, 'return') || str_contains($s, 'cancel') || str_contains($s, 'failed')) {
            return self::STATUS_PENDING_RETURN;
        }

        // 6: In Courier
        if (in_array($s, ['pending', 'hold', 'in transit', 'picked up', 'in review', 'picked', 'transit'], true)) {
            return self::STATUS_IN_COURIER;
        }

        return null;
    }

    /**
     * Pathao Status Mapping:
     * - 6 (In Courier): pickup, at the sorting hub, in transit, received at last mile hub, assigned for delivery
     * - 7 (Delivered): delivered
     * - 8 (Pending Partial): paid return, partial delivery
     * - 12 (Pending Return): return, returned, delivery failed, pickup cancelled, returned to merchant, return in transit
     */
    protected static function mapPathao(string $s): ?int
    {
        // 7: Delivered
        if ($s === 'delivered') {
            return self::STATUS_DELIVERED;
        }

        // 8: Pending Partial
        if ($s === 'paid return' || $s === 'partial delivery' || str_contains($s, 'partial')) {
            return self::STATUS_PENDING_PARTIAL;
        }

        // 12: Pending Return
        if (str_contains($s, 'return') || str_contains($s, 'failed') || str_contains($s, 'cancel')) {
            return self::STATUS_PENDING_RETURN;
        }

        // 6: In Courier
        if (in_array($s, [
            'pickup', 'picked', 'at the sorting hub', 'in transit',
            'received at last mile hub', 'assigned for delivery',
            'assigned for pickup', 'pickup requested', 'created', 'updated'
        ], true)) {
            return self::STATUS_IN_COURIER;
        }

        return null;
    }

    /**
     * Carrybee Status Mapping:
     * - 6 (In Courier): picked, on the way to central warehouse, at central warehouse, in transit, received at last mile hub, assigned for delivery
     * - 7 (Delivered): delivered
     * - 8 (Pending Partial): paid return, partial delivery
     * - 12 (Pending Return): returned, returned at sorting, returned in transit, returned to merchant, pickup cancelled, delivery failed
     */
    protected static function mapCarrybee(string $s): ?int
    {
        // 7: Delivered
        if ($s === 'delivered') {
            return self::STATUS_DELIVERED;
        }

        // 8: Pending Partial (handles 'paid return', 'paid-return', 'partial delivery', 'partial-delivery')
        if ($s === 'paid return' || str_contains($s, 'partial')) {
            return self::STATUS_PENDING_PARTIAL;
        }

        // 12: Pending Return
        if (str_contains($s, 'return') || str_contains($s, 'failed') || str_contains($s, 'cancel')) {
            return self::STATUS_PENDING_RETURN;
        }

        // 6: In Courier
        if (in_array($s, [
            'picked', 'on the way to central warehouse', 'at central warehouse',
            'in transit', 'received at last mile hub', 'assigned for delivery',
            'pickup requested', 'assigned for pickup', 'created'
        ], true)) {
            return self::STATUS_IN_COURIER;
        }

        return null;
    }

    /**
     * RedX Status Mapping:
     * - 6 (In Courier): picked up, in transit, sorting hub, last mile hub, out for delivery, ready for pickup, pickup pending
     * - 7 (Delivered): delivered, delivery confirmed, delivery completed
     * - 8 (Pending Partial): partial delivered, partial delivery
     * - 12 (Pending Return): returned, delivery failed, cancelled, return in transit, return completed
     */
    protected static function mapRedX(string $s): ?int
    {
        // 7: Delivered
        if (in_array($s, ['delivered', 'delivery confirmed', 'delivery completed'], true)) {
            return self::STATUS_DELIVERED;
        }

        // 8: Pending Partial
        if (str_contains($s, 'partial')) {
            return self::STATUS_PENDING_PARTIAL;
        }

        // 12: Pending Return
        if (str_contains($s, 'return') || str_contains($s, 'failed') || str_contains($s, 'cancel')) {
            return self::STATUS_PENDING_RETURN;
        }

        // 6: In Courier
        if (in_array($s, [
            'picked up', 'in transit', 'sorting hub', 'last mile hub',
            'out for delivery', 'ready for pickup', 'pickup pending'
        ], true)) {
            return self::STATUS_IN_COURIER;
        }

        return null;
    }

    /**
     * Generic Fallback Mapping
     */
    protected static function mapGeneric(string $s): ?int
    {
        if (str_contains($s, 'deliver') && !str_contains($s, 'partial') && !str_contains($s, 'fail')) {
            return self::STATUS_DELIVERED;
        }
        if (str_contains($s, 'partial') || $s === 'paid return') {
            return self::STATUS_PENDING_PARTIAL;
        }
        if (str_contains($s, 'return') || str_contains($s, 'cancel') || str_contains($s, 'fail')) {
            return self::STATUS_PENDING_RETURN;
        }
        if (str_contains($s, 'transit') || str_contains($s, 'picked') || str_contains($s, 'hub')) {
            return self::STATUS_IN_COURIER;
        }
        return null;
    }
}
