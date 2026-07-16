<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Setting;
use App\Models\ShipmentTrackingHistory;
use App\Services\BiteshipService;
use App\Services\StockReservationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct(protected StockReservationService $stock)
    {
    }

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'salesPerson'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('orders.index', [
            'orders' => $query->get(),
            'statusFilter' => $request->status,
        ]);
    }

    public function latestNotifications()
    {
        $orders = Order::with('customer')
            ->where('status', 'paid')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer?->name ?? $order->recipient_name,
                'grand_total' => (float) $order->grand_total,
                'created_at' => $order->created_at->diffForHumans(),
                'url' => route('orders.show', $order->id),
            ]);

        return response()->json([
            'count' => Order::where('status', 'paid')->count(),
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['items', 'statusHistories', 'trackingHistories', 'customer', 'salesPerson'])->findOrFail($id);

        return view('orders.show', ['order' => $order]);
    }

    protected const NEXT_STATUS = [
        'paid' => 'processing',
        'shipped' => 'completed',
    ];

    protected const NEXT_LABELS = [
        'processing' => 'Pesanan mulai diproses.',
        'completed' => 'Pesanan selesai.',
    ];

    public function advance($id)
    {
        $order = Order::findOrFail($id);

        if ($order->delivery_method === 'pickup' && $order->status === 'paid') {
            $next = 'completed';
        } else {
            $next = self::NEXT_STATUS[$order->status] ?? null;
        }

        if (!$next) {
            return back()->with('error', 'Status pesanan ini tidak bisa dilanjutkan.');
        }

        $order->update(array_filter([
            'status' => $next,
            'shipped_at' => $next === 'shipped' ? now() : null,
            'completed_at' => $next === 'completed' ? now() : null,
        ]));

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $next,
            'note' => self::NEXT_LABELS[$next] ?? null,
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (in_array($order->status, ['completed', 'cancelled', 'expired', 'failed'])) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
        }

        $this->stock->release($order);

        $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'note' => $request->input('note', 'Dibatalkan oleh admin.'),
        ]);

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function createShipment($id, BiteshipService $biteship)
    {
        $order = Order::with('items.product')->findOrFail($id);

        if ($order->delivery_method === 'pickup') {
            return back()->with('error', 'Pesanan pickup tidak perlu dibuat resi pengiriman.');
        }

        if (!in_array($order->status, ['paid', 'processing'])) {
            return back()->with('error', 'Pesanan harus berstatus lunas/diproses untuk membuat pengiriman.');
        }

        if ($order->hasShipment()) {
            return back()->with('error', 'Pengiriman untuk pesanan ini sudah dibuat.');
        }

        if (!$biteship->isConfigured()) {
            return back()->with('error', 'Biteship belum dikonfigurasi. Silakan atur API Key di Pengaturan.');
        }

        try {
            $biteship->createOrder($order);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal membuat pengiriman: ' . $e->getMessage());
        }

        $order->refresh();
        $order->update(['status' => 'shipped', 'shipped_at' => now()]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'shipped',
            'note' => 'Pengiriman dibuat via Biteship. No. Resi: ' . ($order->courier_waybill_id ?? '-'),
        ]);

        return back()->with('success', 'Pengiriman berhasil dibuat.');
    }

    public function refreshTracking($id, BiteshipService $biteship)
    {
        $order = Order::findOrFail($id);

        if ($order->delivery_method === 'pickup') {
            return back()->with('error', 'Pesanan pickup tidak memiliki tracking pengiriman.');
        }

        if (!$order->hasShipment()) {
            return back()->with('error', 'Pesanan ini belum memiliki pengiriman.');
        }

        $data = $biteship->trackOrder($order);

        if (empty($data)) {
            return back()->with('error', 'Gagal mengambil data tracking dari Biteship.');
        }

        $status = $data['status'] ?? null;
        $order->update(array_filter([
            'shipment_status' => $status,
            'courier_waybill_id' => $order->courier_waybill_id ?: ($data['courier']['waybill_id'] ?? null),
            'courier_tracking_id' => $order->courier_tracking_id ?: ($data['courier']['tracking_id'] ?? null),
            'courier_routing_code' => $data['courier']['routing_code'] ?? null,
        ]));

        $existingStatuses = $order->trackingHistories()->pluck('status')->all();

        foreach (($data['courier']['history'] ?? []) as $entry) {
            $entryStatus = $entry['status'] ?? null;

            if (!$entryStatus || in_array($entryStatus, $existingStatuses)) {
                continue; // avoid duplicate rows on repeated refreshes
            }

            ShipmentTrackingHistory::create([
                'order_id' => $order->id,
                'status' => $entryStatus,
                'note' => $entry['note'] ?? null,
                'created_at' => $entry['updated_at'] ?? now(),
            ]);

            $existingStatuses[] = $entryStatus;
        }

        if ($status === 'delivered' && $order->status !== 'completed') {
            $order->update(['status' => 'completed', 'completed_at' => now()]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'completed',
                'note' => 'Paket telah diterima (otomatis dari tracking Biteship).',
            ]);
        }

        return back()->with('success', 'Data tracking berhasil diperbarui.');
    }

    public function downloadShipmentLabel($id)
    {
        $order = Order::with(['items.product', 'customer'])->findOrFail($id);

        if ($order->delivery_method === 'pickup') {
            return back()->with('error', 'Pesanan pickup tidak memiliki resi pengiriman.');
        }

        if (!$order->hasShipment()) {
            return back()->with('error', 'Pesanan ini belum memiliki resi pengiriman.');
        }

        $settings = Setting::pluck('value', 'key');
        $logoPath = null;

        if (!empty($settings['logo']) && Storage::disk('public')->exists($settings['logo'])) {
            $logoPath = storage_path('app/public/' . $settings['logo']);
        } elseif (file_exists(public_path('logo.jpeg'))) {
            $logoPath = public_path('logo.jpeg');
        }

        $courier = Courier::where('code', strtolower((string) $order->courier_company))->first();
        $courierLogoPath = null;

        if ($courier?->logo && Storage::disk('public')->exists($courier->logo)) {
            $courierLogoPath = storage_path('app/public/' . $courier->logo);
        }

        $courierMeta = [
            'label' => $courier->name ?? strtoupper((string) ($order->courier_company ?: 'Courier')),
        ];

        $pdf = Pdf::loadView('orders.shipment-label-pdf', [
            'order' => $order,
            'settings' => $settings,
            'logoPath' => $logoPath,
            'courierMeta' => $courierMeta,
            'courierLogoPath' => $courierLogoPath,
        ])->setPaper([0, 0, 298, 420], 'portrait');

        return $pdf->download('resi-' . $order->order_number . '.pdf');
    }
}
