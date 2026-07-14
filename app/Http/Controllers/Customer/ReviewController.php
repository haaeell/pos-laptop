<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $orderItem = OrderItem::with('order')->findOrFail($data['order_item_id']);

        if ($orderItem->order->customer_id !== Auth::guard('customers')->id()) {
            abort(404);
        }

        if ($orderItem->order->status !== 'completed') {
            return response()->json(['message' => 'Pesanan ini belum selesai.'], 422);
        }

        if ($orderItem->review) {
            return response()->json(['message' => 'Anda sudah memberi ulasan untuk produk ini.'], 422);
        }

        $review = ProductReview::create([
            'product_id' => $orderItem->product_id,
            'customer_id' => Auth::guard('customers')->id(),
            'order_item_id' => $orderItem->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return response()->json(['message' => 'Ulasan berhasil dikirim, terima kasih!', 'review' => $review]);
    }
}
