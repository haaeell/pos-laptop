<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\ProductReview;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_item_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $orderItem = OrderItem::with(['order', 'review'])
            ->where('id', $data['order_item_id'])
            ->whereHas('order', fn ($query) => $query->where('customer_id', Auth::guard('customers')->id()))
            ->first();

        if (!$orderItem) {
            return response()->json([
                'message' => 'Item pesanan tidak ditemukan untuk akun Anda. Silakan refresh halaman pesanan lalu coba lagi.',
            ], 422);
        }

        if ($orderItem->order->status !== 'completed') {
            return response()->json(['message' => 'Pesanan ini belum selesai.'], 422);
        }

        if ($orderItem->review) {
            return response()->json(['message' => 'Anda sudah memberi ulasan untuk produk ini.'], 422);
        }

        try {
            $review = ProductReview::create([
                'product_id' => $orderItem->product_id,
                'customer_id' => Auth::guard('customers')->id(),
                'order_item_id' => $orderItem->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);
        } catch (QueryException $e) {
            if ($orderItem->review()->exists()) {
                return response()->json(['message' => 'Anda sudah memberi ulasan untuk produk ini.'], 422);
            }

            report($e);

            return response()->json(['message' => 'Ulasan belum bisa disimpan..'], 500);
        }

        return response()->json(['message' => 'Ulasan berhasil dikirim, terima kasih!', 'review' => $review]);
    }
}
