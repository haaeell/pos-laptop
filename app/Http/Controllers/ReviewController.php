<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with(['product', 'customer'])->latest()->get();

        return view('master.reviews.index', compact('reviews'));
    }

    public function destroy($id)
    {
        ProductReview::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Ulasan berhasil dihapus');
    }
}
