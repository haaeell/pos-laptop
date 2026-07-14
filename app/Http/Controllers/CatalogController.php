<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
            'contacts'   => Contact::where('is_active', true)->get(),
            'settings'   => Setting::pluck('value', 'key'),
        ]);
    }

    public function listing()
    {
        return view('produk.index', [
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
        ]);
    }

    const ALLOWED_PER_PAGE = [10, 20, 50, 100];

    public function data(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images'])
            ->where('status', 'available')
            ->where('is_active', true);

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

        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', (float) $request->max_price);
        }

        if ($request->boolean('in_stock_only')) {
            $query->where('stock', '>', 0);
        }

        // Out-of-stock products always sink to the bottom, regardless of
        // sort order, so shoppers see purchasable products first.
        $query->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')->latest();

        $perPageParam = $request->input('per_page', 10);

        if ($perPageParam === 'all') {
            $all = $query->get();

            return response()->json([
                'data' => $all->map(fn ($p) => $this->transformProduct($p)),
                'meta' => ['current_page' => 1, 'last_page' => 1],
            ]);
        }

        $perPage = in_array((int) $perPageParam, self::ALLOWED_PER_PAGE) ? (int) $perPageParam : 10;
        $products = $query->paginate($perPage);

        return response()->json([
            'data' => $products->map(fn ($p) => $this->transformProduct($p)),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ]
        ]);
    }

    protected function transformProduct(Product $p): array
    {
        return [
            'id'        => $p->id,
            'name'      => $p->name,
            'code'      => $p->product_code,
            'price'     => $p->selling_price,
            'strike_price' => $p->strike_price,
            'stock'     => $p->stock,
            'condition' => $p->condition,
            'category'  => $p->category->name,
            'brand'     => $p->brand?->name,
            'image'     => $p->image,
            'images'    => $p->images,
            'description' => $p->description,
        ];
    }

    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'images', 'reviews' => fn ($q) => $q->with('customer')->latest()])
            ->where('status', 'available')
            ->where('is_active', true)
            ->findOrFail($id);

        $related = Product::with(['category', 'brand'])
            ->where('status', 'available')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        $isFavorited = Auth::guard('customers')->check()
            && $product->favoritedBy()->where('customer_id', Auth::guard('customers')->id())->exists();

        return view('catalog.show', [
            'product'    => $product,
            'related'    => $related,
            'contacts'   => Contact::where('is_active', true)->get(),
            'settings'   => Setting::pluck('value', 'key'),
            'isFavorited' => $isFavorited,
        ]);
    }
}
