<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
        ]);
    }

    public function data(Request $request)
    {
        $query = Product::with(['category', 'brand'])
            ->where('status', 'available');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('product_code', 'like', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->brand) {
            $query->where('brand_id', $request->brand);
        }

        $products = $query->latest()->paginate(10); // ⬅️ jumlah per halaman

        return response()->json([
            'data' => $products->map(fn($p) => [
                'id'        => $p->id,
                'name'      => $p->name,
                'code'      => $p->product_code,
                'price'     => $p->selling_price,
                'condition' => $p->condition,
                'category'  => $p->category->name,
                'brand'     => $p->brand?->name,
                'image'     => $p->image,
                'description' => $p->description,
            ]),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ]
        ]);
    }
}
