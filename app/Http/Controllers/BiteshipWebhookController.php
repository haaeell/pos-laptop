<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ShipmentTrackingHistory;
use App\Services\BiteshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BiteshipWebhookController extends Controller
{
    public function handle(Request $request, string $token, BiteshipService $biteship)
    {
        if (!hash_equals($biteship->webhookToken(), $token)) {
            Log::warning('Biteship webhook: invalid token');
            abort(403, 'Invalid token');
        }

        $payload = $request->all();
        $biteshipOrderId = $payload['order_id'] ?? null;

        if (!$biteshipOrderId) {
            return response()->json(['message' => 'Missing order_id'], 422);
        }

        $order = Order::where('biteship_order_id', $biteshipOrderId)->first();

        if (!$order) {
            Log::warning('Biteship webhook: order not found', ['order_id' => $biteshipOrderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $status = $payload['status'] ?? null;

        if ($status && $status !== $order->shipment_status) {
            $order->update(['shipment_status' => $status]);

            ShipmentTrackingHistory::create([
                'order_id' => $order->id,
                'status' => $status,
                'note' => $payload['courier_waybill_id'] ?? null
                    ? 'No. Resi: ' . $payload['courier_waybill_id']
                    : null,
            ]);
        }

        if (isset($payload['courier_waybill_id']) && !$order->courier_waybill_id) {
            $order->update(['courier_waybill_id' => $payload['courier_waybill_id']]);
        }

        if ($status === 'delivered' && $order->status !== 'completed') {
            $order->update(['status' => 'completed', 'completed_at' => now()]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'completed',
                'note' => 'Paket telah diterima (otomatis dari webhook Biteship).',
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}
