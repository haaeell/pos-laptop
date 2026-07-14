<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderExpiryService;
use Illuminate\Console\Command;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';

    protected $description = 'Auto-cancel pending_payment orders past their 30-minute payment window and release their reserved stock.';

    public function handle(OrderExpiryService $expiry): int
    {
        $orders = Order::where('status', 'pending_payment')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($orders as $order) {
            $expiry->expireIfDue($order);
        }

        $this->info("Expired {$orders->count()} pending order(s).");

        return self::SUCCESS;
    }
}
