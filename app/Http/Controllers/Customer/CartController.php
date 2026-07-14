<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected function customerId()
    {
        return Auth::guard('customers')->id();
    }

    public function index()
    {
        $items = CartItem::with('product')
            ->where('customer_id', $this->customerId())
            ->latest()
            ->get()
            ->filter(fn ($item) => $item->product && $item->product->isAvailable());

        return view('cart.index', ['items' => $items]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (!$product->isAvailable()) {
            return back()->with('error', 'Produk ini sudah tidak tersedia.');
        }

        $qty = $data['qty'] ?? 1;
        $maxQty = max(1, $product->stock);

        $item = CartItem::firstOrNew([
            'customer_id' => $this->customerId(),
            'product_id' => $product->id,
        ]);

        $item->qty = min($maxQty, ($item->exists ? $item->qty : 0) + $qty);
        $item->save();

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function updateQty(Request $request, $id)
    {
        $data = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $item = CartItem::where('customer_id', $this->customerId())->findOrFail($id);
        $maxQty = max(1, $item->product->stock);

        $item->update(['qty' => min($data['qty'], $maxQty)]);

        return back();
    }

    public function remove($id)
    {
        CartItem::where('customer_id', $this->customerId())->where('id', $id)->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
