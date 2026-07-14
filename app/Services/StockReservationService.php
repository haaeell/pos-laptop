<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;

class StockReservationService
{
    /**
     * Reserves stock for the given checkout lines. Must be called inside the
     * same DB transaction that creates the Order, so the row locks taken
     * here hold until the order (and its stock decrement) is committed
     * together — this is what stops two simultaneous checkouts from both
     * reserving the last unit of a product.
     *
     * @param array $lines [{ product: Product, qty: int }, ...]
     */
    public function reserve(array $lines): void
    {
        $productIds = collect($lines)->pluck('product.id')->all();

        $lockedProducts = Product::whereIn('id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($lines as $line) {
            $product = $lockedProducts->get($line['product']->id);
            $qty = $line['qty'];

            if (!$product || $product->stock < $qty) {
                throw new InsufficientStockException($line['product']->name);
            }

            if ($product->stock == $qty) {
                $product->update(['status' => 'sold', 'stock' => 0]);
            } else {
                $product->decrement('stock', $qty);
            }
        }
    }

    /**
     * Releases stock reserved by reserve() when an order leaves the
     * pending_payment state without being paid (cancelled/expired/failed).
     */
    public function release(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

            if (!$product) {
                continue;
            }

            $wasSold = $product->status === 'sold';
            $product->increment('stock', $item->qty);

            if ($wasSold && $product->stock > 0) {
                $product->update(['status' => 'available']);
            }
        }
    }
}
