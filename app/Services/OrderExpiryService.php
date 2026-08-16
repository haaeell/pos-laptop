<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;

class OrderExpiryService
{
    public function __construct(protected StockReservationService $stock)
    {
    }

    /**
     * If the given order is pending_payment and past its expires_at, flips
     * it to expired and releases its reserved stock. Called on every read
     * of a pending order (checkout, pay page, order list/detail — admin and
     * customer) so statuses are always correct, with no cron dependency.
     */
    public function expireIfDue(Order $order): Order
    {
        if ($order->status !== 'pending_payment' || !$order->expires_at || $order->expires_at->isFuture()) {
            return $order;
        }

        return DB::transaction(function () use ($order) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();

            if (!$locked || $locked->status !== 'pending_payment' || !$locked->expires_at || $locked->expires_at->isFuture()) {
                return $locked ?? $order;
            }

            $this->stock->release($locked);

            $locked->update([
                'status' => 'expired',
                'cancelled_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $locked->id,
                'status' => 'expired',
                'note' => 'Otomatis dibatalkan karena melebihi batas waktu pembayaran (30 menit).',
            ]);

            return $locked;
        });
    }

    /**
     * Expires every pending_payment order currently past its expires_at.
     * Called from order-list pages (admin/customer) so statuses are already
     * correct the moment someone opens the list.
     */
    public function expireAllDue(): int
    {
        $orders = Order::where('status', 'pending_payment')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($orders as $order) {
            $this->expireIfDue($order);
        }

        return $orders->count();
    }
}
