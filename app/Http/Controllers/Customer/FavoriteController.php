<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    protected function customerId()
    {
        return Auth::guard('customers')->id();
    }

    public function index()
    {
        $products = Product::whereHas('favoritedBy', fn ($q) => $q->where('customer_id', $this->customerId()))
            ->with(['category', 'brand'])
            ->latest()
            ->get();

        return view('customer.favorites.index', ['products' => $products]);
    }

    public function toggle(Request $request, Product $product)
    {
        $customer = Auth::guard('customers')->user();

        $isFavorited = $customer->favoriteProducts()->where('product_id', $product->id)->exists();

        if ($isFavorited) {
            $customer->favoriteProducts()->detach($product->id);
        } else {
            $customer->favoriteProducts()->syncWithoutDetaching([$product->id]);
        }

        return response()->json(['favorited' => !$isFavorited]);
    }
}
