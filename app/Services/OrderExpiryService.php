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
     * it to expired and releases its reserved stock. Safe to call on every
     * read of a pending order (checkout, pay page, order detail) so
     * correctness never depends on the scheduled command having run yet.
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
}
