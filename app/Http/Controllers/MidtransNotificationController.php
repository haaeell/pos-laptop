<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\MidtransService;
use App\Services\StockReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    public function __construct(protected StockReservationService $stock)
    {
    }

    public function handle(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');
        $paymentType = (string) ($payload['payment_type'] ?? '');

        if (!$orderId || !$signatureKey || !$midtrans->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans notification: invalid signature', ['order_id' => $orderId]);
            abort(403, 'Invalid signature');
        }

        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            Log::warning('Midtrans notification: order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($order, $transactionStatus, $fraudStatus, $paymentType) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                if ($fraudStatus === 'challenge') {
                    return;
                }

                if ($order->isPaid()) {
                    return; // already processed — idempotent
                }

                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'midtrans_payment_type' => $paymentType,
                ]);

                // Stock was already reserved when the order was created
                // (pending_payment) — paying doesn't touch stock again.

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => 'paid',
                    'note' => 'Pembayaran diterima via Midtrans (' . $paymentType . ').',
                ]);
            } elseif ($transactionStatus === 'pending') {
                // stays pending_payment, nothing to do
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'])) {
                if (in_array($order->status, ['cancelled', 'expired', 'failed']) || $order->isPaid()) {
                    return;
                }

                $newStatus = match ($transactionStatus) {
                    'expire' => 'expired',
                    default => $transactionStatus === 'cancel' ? 'cancelled' : 'failed',
                };

                $this->stock->release($order);

                $order->update([
                    'status' => $newStatus,
                    'cancelled_at' => now(),
                ]);

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $newStatus,
                    'note' => 'Transaksi Midtrans: ' . $transactionStatus,
                ]);
            }
        });

        return response()->json(['message' => 'OK']);
    }
}
