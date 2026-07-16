<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\OrderExpiryService;
use App\Services\StockReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    const STATUS_TABS = [
        'pending_payment' => ['pending_payment'],
        'processing' => ['paid', 'processing', 'shipped'],
        'completed' => ['completed'],
        'cancelled' => ['cancelled', 'expired', 'failed'],
    ];

    public function __construct(
        protected OrderExpiryService $orderExpiry,
        protected StockReservationService $stockReservation,
    ) {
    }

    public function index(Request $request)
    {
        $activeTab = $request->query('status');

        $orders = Order::where('customer_id', Auth::guard('customers')->id())
            ->when($activeTab && isset(self::STATUS_TABS[$activeTab]), fn ($q) => $q->whereIn('status', self::STATUS_TABS[$activeTab]))
            ->latest()
            ->get();

        return view('customer.orders.index', ['orders' => $orders, 'activeTab' => $activeTab]);
    }

    protected function customerOrder(string $orderNumber): Order
    {
        return Order::where('order_number', $orderNumber)
            ->where('customer_id', Auth::guard('customers')->id())
            ->firstOrFail();
    }

    public function show(string $orderNumber)
    {
        $order = $this->customerOrder($orderNumber);
        $order = $this->orderExpiry->expireIfDue($order);

        $order->load(['items.product', 'items.review', 'statusHistories', 'trackingHistories', 'salesPerson']);

        return view('customer.orders.show', ['order' => $order]);
    }

    public function cancel(Request $request, string $orderNumber)
    {
        $order = $this->customerOrder($orderNumber);

        $data = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        if ($order->status !== 'pending_payment') {
            $message = 'Pesanan ini tidak bisa dibatalkan.';

            return $request->wantsJson()
                ? response()->json(['message' => $message], 422)
                : back()->with('error', $message);
        }

        $this->stockReservation->release($order);

        $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'note' => $data['reason'],
        ]);

        $message = 'Pesanan berhasil dibatalkan.';

        return $request->wantsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }
}
