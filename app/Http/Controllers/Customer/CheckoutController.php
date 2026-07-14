<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Setting;
use App\Services\BiteshipService;
use App\Services\MidtransService;
use App\Services\OrderExpiryService;
use App\Services\StockReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    const PAYMENT_WINDOW_MINUTES = 30;

    public function __construct(
        protected StockReservationService $stockReservation,
        protected OrderExpiryService $orderExpiry,
    ) {
    }

    protected function customerId()
    {
        return Auth::guard('customers')->id();
    }

    /**
     * Returns the customer's still-active pending_payment order, if any,
     * after lazily expiring it if it's actually past its payment window.
     * Enforces "one pending order at a time" everywhere checkout is entered.
     */
    protected function activePendingOrder()
    {
        $order = Order::where('customer_id', $this->customerId())
            ->where('status', 'pending_payment')
            ->latest()
            ->first();

        if (!$order) {
            return null;
        }

        $order = $this->orderExpiry->expireIfDue($order);

        return $order->status === 'pending_payment' ? $order : null;
    }

    /**
     * Build the list of { product, qty } lines being checked out — either a
     * single "buy now" product or the customer's full cart. Never trusts a
     * client-submitted price; only product_id/qty are read from the request.
     */
    protected function resolveLines(Request $request): array
    {
        if ($request->filled('product_id')) {
            $product = Product::findOrFail($request->integer('product_id'));
            $qty = max(1, $request->integer('qty', 1));

            if (!$product->isAvailable()) {
                abort(404);
            }

            return [['product' => $product, 'qty' => min($qty, max(1, $product->stock))]];
        }

        return CartItem::with('product')
            ->where('customer_id', $this->customerId())
            ->get()
            ->filter(fn ($item) => $item->product && $item->product->isAvailable())
            ->map(fn ($item) => ['product' => $item->product, 'qty' => min($item->qty, max(1, $item->product->stock))])
            ->values()
            ->all();
    }

    protected function itemsForBiteship(array $lines): array
    {
        return collect($lines)->map(fn ($l) => [
            'name' => $l['product']->name,
            'value' => (int) round($l['product']->selling_price),
            'quantity' => $l['qty'],
            'weight' => $l['product']->weight ?: 1000,
        ])->toArray();
    }

    public function create(Request $request)
    {
        if ($pending = $this->activePendingOrder()) {
            return redirect()->route('checkout.pay', $pending)
                ->with('info', 'Anda masih memiliki pesanan yang menunggu pembayaran. Selesaikan atau tunggu hingga kedaluwarsa sebelum membuat pesanan baru.');
        }

        $lines = $this->resolveLines($request);

        if (empty($lines)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $itemsSubtotal = collect($lines)->sum(fn ($l) => $l['qty'] * $l['product']->selling_price);

        $addresses = CustomerAddress::where('customer_id', $this->customerId())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('checkout.create', [
            'lines' => $lines,
            'itemsSubtotal' => $itemsSubtotal,
            'addresses' => $addresses,
            'buyNowProductId' => $request->integer('product_id') ?: null,
            'buyNowQty' => $request->integer('qty') ?: null,
        ]);
    }

    /**
     * AJAX: given a saved address, return live Biteship courier options for
     * the current cart/buy-now lines.
     */
    public function rates(Request $request, BiteshipService $biteship)
    {
        $data = $request->validate([
            'address_id' => 'required|exists:customer_addresses,id',
            'product_id' => 'nullable|integer',
            'qty' => 'nullable|integer|min:1',
        ]);

        $address = CustomerAddress::where('customer_id', $this->customerId())->findOrFail($data['address_id']);

        if (!$biteship->isConfigured()) {
            return response()->json(['message' => 'Pengiriman belum diaktifkan oleh admin toko.'], 422);
        }

        if (!$address->area_id) {
            return response()->json(['message' => 'Area alamat ini belum bisa dikenali. Coba pilih/ubah alamat lain.'], 422);
        }

        $originAreaId = Setting::get('biteship_origin_area_id');
        if (!$originAreaId) {
            return response()->json(['message' => 'Lokasi asal toko belum diatur oleh admin.'], 422);
        }

        $lines = $this->resolveLines($request);
        if (empty($lines)) {
            return response()->json(['message' => 'Keranjang kosong.'], 422);
        }

        $pricing = collect($biteship->getRates($originAreaId, $address->area_id, $this->itemsForBiteship($lines)))
            ->sortBy(fn ($option) => (float) ($option['price'] ?? PHP_FLOAT_MAX))
            ->values()
            ->all();

        return response()->json(['pricing' => $pricing]);
    }

    protected function checkoutError(Request $request, string $message, ?string $redirectRoute = null)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return $redirectRoute ? redirect()->route($redirectRoute)->with('error', $message) : back()->with('error', $message);
    }

    public function store(Request $request, MidtransService $midtrans, BiteshipService $biteship)
    {
        if ($pending = $this->activePendingOrder()) {
            $message = 'Anda masih memiliki pesanan yang menunggu pembayaran. Selesaikan atau tunggu hingga kedaluwarsa sebelum membuat pesanan baru.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $message, 'redirect' => route('checkout.pay', $pending)], 422);
            }

            return redirect()->route('checkout.pay', $pending)->with('info', $message);
        }

        $data = $request->validate([
            'address_id' => 'required|exists:customer_addresses,id',
            'courier_company' => 'required|string',
            'courier_type' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'product_id' => 'nullable|integer',
            'qty' => 'nullable|integer|min:1',
        ]);

        $address = CustomerAddress::where('customer_id', $this->customerId())->findOrFail($data['address_id']);

        $lines = $this->resolveLines($request);

        if (empty($lines)) {
            return $this->checkoutError($request, 'Keranjang Anda kosong.', 'cart.index');
        }

        $originAreaId = Setting::get('biteship_origin_area_id');

        if (!$biteship->isConfigured() || !$originAreaId || !$address->area_id) {
            return $this->checkoutError($request, 'Pengiriman belum siap digunakan, silakan hubungi admin toko.');
        }

        // Re-fetch rates server-side and only trust the price Biteship itself
        // returns for the submitted courier — never the client's number.
        $pricing = collect($biteship->getRates($originAreaId, $address->area_id, $this->itemsForBiteship($lines)))
            ->sortBy(fn ($option) => (float) ($option['price'] ?? PHP_FLOAT_MAX))
            ->values();
        $chosen = collect($pricing)->first(fn ($p) =>
            $p['courier_code'] === $data['courier_company'] && $p['type'] === $data['courier_type']
        );

        if (!$chosen) {
            return $this->checkoutError($request, 'Opsi kurir yang dipilih tidak lagi tersedia, silakan pilih ulang.');
        }

        $shippingCost = (float) $chosen['price'];
        $itemsSubtotal = collect($lines)->sum(fn ($l) => $l['qty'] * $l['product']->selling_price);
        $grandTotal = $itemsSubtotal + $shippingCost;

        try {
            $order = DB::transaction(function () use ($data, $lines, $request, $address, $chosen, $shippingCost, $itemsSubtotal, $grandTotal, $originAreaId) {
                // Locks and decrements product stock for these lines — must run
                // inside this same transaction so the row locks it takes are
                // held until the order itself commits, closing the window where
                // two simultaneous checkouts could both reserve the last unit.
                $this->stockReservation->reserve($lines);

                $order = Order::create([
                    'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'customer_id' => $this->customerId(),
                    'status' => 'pending_payment',
                    'grand_total' => $grandTotal,
                    'items_subtotal' => $itemsSubtotal,
                    'shipping_cost' => $shippingCost,
                    'recipient_name' => $address->recipient_name,
                    'recipient_phone' => $address->recipient_phone,
                    'province' => $address->province,
                    'city' => $address->city,
                    'district' => $address->district,
                    'address_detail' => $address->address_detail,
                    'notes' => $data['notes'] ?? null,
                    'expires_at' => now()->addMinutes(self::PAYMENT_WINDOW_MINUTES),
                    'courier_company' => $chosen['courier_code'],
                    'courier_type' => $chosen['type'],
                    'courier_service_name' => $chosen['courier_name'] . ' - ' . $chosen['courier_service_name'],
                    'origin_area_id' => $originAreaId,
                    'destination_area_id' => $address->area_id,
                ]);

                foreach ($lines as $line) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $line['product']->id,
                        'product_name' => $line['product']->name,
                        'price' => $line['product']->selling_price,
                        'purchase_price' => $line['product']->purchase_price,
                        'qty' => $line['qty'],
                        'subtotal' => $line['qty'] * $line['product']->selling_price,
                    ]);
                }

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => 'pending_payment',
                    'note' => 'Pesanan dibuat, menunggu pembayaran.',
                ]);

                // "Buy now" never touches the cart. Only clear cart rows when this
                // checkout was built from the cart itself (no explicit product_id).
                if (!$request->filled('product_id')) {
                    CartItem::where('customer_id', $this->customerId())->delete();
                }

                return $order;
            });
        } catch (InsufficientStockException $e) {
            return $this->checkoutError($request, $e->getMessage());
        }

        if ($midtrans->isConfigured()) {
            try {
                $order->update(['snap_token' => $midtrans->createSnapToken($order)]);
            } catch (\Throwable $e) {
                Log::warning('Midtrans snap token failed after order created', [
                    'order_number' => $order->order_number,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['redirect' => route('checkout.pay', $order)]);
        }

        return redirect()->route('checkout.pay', $order);
    }

    public function pay(Order $order, MidtransService $midtrans)
    {
        if ($order->customer_id !== $this->customerId()) {
            abort(404);
        }

        $order = $this->orderExpiry->expireIfDue($order);

        return view('checkout.pay', [
            'order' => $order,
            'clientKey' => $midtrans->getClientKey(),
            'isProduction' => $midtrans->isProductionMode(),
            'midtransConfigured' => $midtrans->isConfigured(),
        ]);
    }
}
