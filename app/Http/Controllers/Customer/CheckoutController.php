<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Courier;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\SalesPerson;
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

    protected function resolveBuyNowProduct(Request $request): ?Product
    {
        $buyNow = $request->session()->get('checkout_buy_now');

        if (is_array($buyNow) && !empty($buyNow['product_id'])) {
            return Product::findOrFail((int) $buyNow['product_id']);
        }

        if ($request->filled('product_id')) {
            return Product::findOrFail($request->integer('product_id'));
        }

        return null;
    }

    protected function requestedQty(Request $request): int
    {
        $buyNow = $request->session()->get('checkout_buy_now');
        if (is_array($buyNow) && !empty($buyNow['qty'])) {
            return max(1, (int) $buyNow['qty']);
        }

        return max(
            1,
            (int) $request->integer('qty', 1)
        );
    }

    public function buyNow(Request $request)
    {
        $data = $request->validate([
            'product_slug' => 'required|string',
            'qty' => 'nullable|integer|min:1',
        ]);

        $product = Product::where('slug', $data['product_slug'])->firstOrFail();

        if (!$product->isAvailable()) {
            abort(404);
        }

        $request->session()->flash('checkout_buy_now', [
            'product_id' => $product->id,
            'qty' => min(max(1, (int) ($data['qty'] ?? 1)), max(1, $product->stock)),
        ]);

        return redirect()->route('checkout.create');
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
        if ($product = $this->resolveBuyNowProduct($request)) {
            $qty = $this->requestedQty($request);

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
            return redirect()->route('checkout.pay', $pending->order_number)
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
            'buyNowProductId' => $this->resolveBuyNowProduct($request)?->id,
            'buyNowQty' => $this->resolveBuyNowProduct($request) ? $this->requestedQty($request) : null,
            'referralDiscountSetting' => (float) Setting::get('referral_discount_amount', 0),
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

        $courierLogos = Courier::pluck('logo', 'code');

        $pricing = collect($biteship->getRates($originAreaId, $address->area_id, $this->itemsForBiteship($lines)))
            ->sortBy(fn ($option) => (float) ($option['price'] ?? PHP_FLOAT_MAX))
            ->values()
            ->map(function ($option) use ($courierLogos) {
                $logo = $courierLogos[strtolower((string) ($option['courier_code'] ?? ''))] ?? null;
                $option['courier_logo_url'] = $logo ? asset('storage/' . $logo) : null;

                return $option;
            })
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

    protected function resolveReferral(?string $referralCode, float $currentTotal): array
    {
        $normalizedCode = Str::upper(trim((string) $referralCode));

        if ($normalizedCode === '') {
            return [
                'valid' => false,
                'message' => null,
                'salesPerson' => null,
                'referral_code' => null,
                'discount_setting' => (float) Setting::get('referral_discount_amount', 0),
                'discount' => 0,
                'fee_before_discount' => 0,
                'fee_cut' => 0,
                'fee_after_discount' => 0,
            ];
        }

        $salesPerson = SalesPerson::query()
            ->where('referral_code', $normalizedCode)
            ->where('active', true)
            ->first();

        if (!$salesPerson) {
            return [
                'valid' => false,
                'message' => 'Kode referral tidak valid atau sudah tidak aktif.',
                'salesPerson' => null,
                'referral_code' => $normalizedCode,
                'discount_setting' => (float) Setting::get('referral_discount_amount', 0),
                'discount' => 0,
                'fee_before_discount' => 0,
                'fee_cut' => 0,
                'fee_after_discount' => 0,
            ];
        }

        $discountSetting = max(0, (float) Setting::get('referral_discount_amount', 0));
        $feeBeforeDiscount = max(0, (float) $salesPerson->fee);
        $discount = min($discountSetting, $feeBeforeDiscount, max(0, $currentTotal));
        $feeAfterDiscount = max(0, $feeBeforeDiscount - $discount);

        return [
            'valid' => true,
            'message' => $discount > 0
                ? 'Kode referral aktif dan diskon berhasil diterapkan.'
                : 'Kode referral aktif, tetapi diskon saat ini bernilai Rp 0.',
            'salesPerson' => $salesPerson,
            'referral_code' => $normalizedCode,
            'discount_setting' => $discountSetting,
            'discount' => $discount,
            'fee_before_discount' => $feeBeforeDiscount,
            'fee_cut' => $discount,
            'fee_after_discount' => $feeAfterDiscount,
        ];
    }

    public function validateReferral(Request $request)
    {
        $data = $request->validate([
            'referral_code' => 'nullable|string|max:100',
            'delivery_method' => 'nullable|in:shipping,pickup',
            'address_id' => 'nullable|exists:customer_addresses,id',
            'courier_company' => 'nullable|string',
            'courier_type' => 'nullable|string',
            'product_id' => 'nullable|integer',
            'qty' => 'nullable|integer|min:1',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        $lines = $this->resolveLines($request);
        if (empty($lines)) {
            return response()->json(['valid' => false, 'message' => 'Keranjang Anda kosong.'], 422);
        }

        $itemsSubtotal = collect($lines)->sum(fn ($line) => $line['qty'] * $line['product']->selling_price);
        $shippingCost = max(0, (float) ($data['shipping_cost'] ?? 0));
        $currentTotal = $itemsSubtotal + $shippingCost;
        $referral = $this->resolveReferral($data['referral_code'] ?? null, $currentTotal);

        if (!$referral['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $referral['message'],
                'discount' => 0,
                'grand_total' => $currentTotal,
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => $referral['message'],
            'referral_code' => $referral['referral_code'],
            'marketing_name' => $referral['salesPerson']->name,
            'discount_setting' => $referral['discount_setting'],
            'discount' => $referral['discount'],
            'fee_before_discount' => $referral['fee_before_discount'],
            'fee_cut' => $referral['fee_cut'],
            'fee_after_discount' => $referral['fee_after_discount'],
            'grand_total' => max(0, $currentTotal - $referral['discount']),
        ]);
    }

    public function store(Request $request, MidtransService $midtrans, BiteshipService $biteship)
    {
        if ($pending = $this->activePendingOrder()) {
            $message = 'Anda masih memiliki pesanan yang menunggu pembayaran. Selesaikan atau tunggu hingga kedaluwarsa sebelum membuat pesanan baru.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $message, 'redirect' => route('checkout.pay', $pending->order_number, false)], 422);
            }

            return redirect()->route('checkout.pay', $pending->order_number)->with('info', $message);
        }

        $data = $request->validate([
            'delivery_method' => 'required|in:shipping,pickup',
            'address_id' => 'nullable|exists:customer_addresses,id',
            'courier_company' => 'nullable|string',
            'courier_type' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'referral_code' => 'nullable|string|max:100',
            'product_id' => 'nullable|integer',
            'qty' => 'nullable|integer|min:1',
        ]);

        $lines = $this->resolveLines($request);

        if (empty($lines)) {
            return $this->checkoutError($request, 'Keranjang Anda kosong.', 'cart.index');
        }

        $itemsSubtotal = collect($lines)->sum(fn ($l) => $l['qty'] * $l['product']->selling_price);
        $shippingCost = 0;
        $chosen = null;
        $address = null;
        $originAreaId = null;

        if ($data['delivery_method'] === 'shipping') {
            $address = CustomerAddress::where('customer_id', $this->customerId())->findOrFail($data['address_id']);
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
        }

        $beforeDiscountTotal = $itemsSubtotal + $shippingCost;
        $referral = $this->resolveReferral($data['referral_code'] ?? null, $beforeDiscountTotal);

        if (filled($data['referral_code']) && !$referral['valid']) {
            return $this->checkoutError($request, $referral['message'] ?? 'Kode referral tidak valid.');
        }

        $grandTotal = max(0, $beforeDiscountTotal - $referral['discount']);
        $customer = Auth::guard('customers')->user();
        $storeName = Setting::get('nama_toko', 'Barokah Computer');
        $storeAddress = Setting::get('alamat', 'Alamat toko belum diatur');

        try {
            $order = DB::transaction(function () use ($data, $lines, $request, $address, $chosen, $shippingCost, $itemsSubtotal, $grandTotal, $originAreaId, $customer, $storeName, $storeAddress, $referral) {
                // Locks and decrements product stock for these lines — must run
                // inside this same transaction so the row locks it takes are
                // held until the order itself commits, closing the window where
                // two simultaneous checkouts could both reserve the last unit.
                $this->stockReservation->reserve($lines);

                $isPickup = $data['delivery_method'] === 'pickup';

                $order = Order::create([
                    'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'customer_id' => $this->customerId(),
                    'sales_person_id' => $referral['salesPerson']?->id,
                    'delivery_method' => $data['delivery_method'],
                    'status' => 'pending_payment',
                    'grand_total' => $grandTotal,
                    'items_subtotal' => $itemsSubtotal,
                    'shipping_cost' => $shippingCost,
                    'referral_code' => $referral['referral_code'],
                    'marketing_name' => $referral['salesPerson']?->name,
                    'referral_discount' => $referral['discount'],
                    'marketing_fee_before_discount' => $referral['fee_before_discount'],
                    'marketing_fee_discount' => $referral['fee_cut'],
                    'marketing_fee_after_discount' => $referral['fee_after_discount'],
                    'recipient_name' => $isPickup ? $customer->name : $address->recipient_name,
                    'recipient_phone' => $isPickup ? ($customer->phone ?: $address->recipient_phone) : $address->recipient_phone,
                    'province' => $isPickup ? 'Pickup di Toko' : $address->province,
                    'city' => $isPickup ? $storeName : $address->city,
                    'district' => $isPickup ? 'Pickup Sendiri' : $address->district,
                    'address_detail' => $isPickup ? $storeAddress : $address->address_detail,
                    'notes' => $data['notes'] ?? null,
                    'expires_at' => now()->addMinutes(self::PAYMENT_WINDOW_MINUTES),
                    'courier_company' => $isPickup ? null : $chosen['courier_code'],
                    'courier_type' => $isPickup ? null : $chosen['type'],
                    'courier_service_name' => $isPickup ? 'Pickup Sendiri' : ($chosen['courier_name'] . ' - ' . $chosen['courier_service_name']),
                    'origin_area_id' => $isPickup ? null : $originAreaId,
                    'destination_area_id' => $isPickup ? null : $address->area_id,
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
                    'note' => $data['delivery_method'] === 'pickup'
                        ? 'Pesanan pickup dibuat, menunggu pembayaran.'
                        : 'Pesanan dibuat, menunggu pembayaran.',
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
            return response()->json(['redirect' => route('checkout.pay', $order->order_number, false)]);
        }

        return redirect()->route('checkout.pay', $order->order_number);
    }

    public function pay(string $orderNumber, MidtransService $midtrans)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('customer_id', $this->customerId())
            ->firstOrFail();

        $order = $this->orderExpiry->expireIfDue($order);

        return view('checkout.pay', [
            'order' => $order,
            'clientKey' => $midtrans->getClientKey(),
            'isProduction' => $midtrans->isProductionMode(),
            'midtransConfigured' => $midtrans->isConfigured(),
        ]);
    }
}
