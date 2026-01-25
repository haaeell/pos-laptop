<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleBonus;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $today = Carbon::today();

        // ===== KPI =====
        $totalSales = Sale::whereDate('created_at', $today)->sum('grand_total');
        $totalProfit = Sale::whereDate('created_at', $today)->sum('benefit');
        $totalTransactions = Sale::whereDate('created_at', $today)->count();
        $totalBonus = SaleBonus::whereDate('created_at', $today)->sum('benefit');

        // ===== SALES CHART (7 days) =====
        $salesChart = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(grand_total) as total')
        )
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ===== PAYMENT METHOD =====
        $paymentMethods = Sale::select('payment_method', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_method')
            ->get();

        // ===== TOP PRODUCTS =====
        $topProducts = SaleItem::select(
            'product_id',
            DB::raw('COUNT(*) as total_sold'),
            DB::raw('SUM(benefit) as total_profit')
        )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // ===== INVENTORY =====
        $inventory = Product::whereIn('status', ['available', 'sold'])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $topBrands = Brand::select('brands.id', 'brands.name')
            ->selectRaw('COUNT(sale_items.id) as total_sold')
            ->selectRaw('SUM(sale_items.benefit) as total_profit')
            ->join('products', 'brands.id', '=', 'products.brand_id')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('brands.id', 'brands.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();


        $stockByBrand = Brand::withCount([
            'products as total_stock' => function ($q) {
                $q->where('status', 'available');
            }
        ])
            ->orderByDesc('total_stock')
            ->limit(6)
            ->get();


        $stockByCategory = Category::withCount([
            'products as total_stock' => function ($q) {
                $q->where('status', 'available');
            }
        ])
            ->orderByDesc('total_stock')
            ->limit(6)
            ->get();

        $brands = Brand::all();

        $categories = Category::all();

        return view('home', compact(
            'totalSales',
            'totalProfit',
            'totalTransactions',
            'totalBonus',
            'salesChart',
            'paymentMethods',
            'topProducts',
            'inventory',
            'topBrands',
            'stockByBrand',
            'stockByCategory',
            'brands',
            'categories'
        ));
    }

    public function inventoryList(Request $request)
    {
        $query = Product::with(['brand', 'category'])
            ->where('status', $request->status);

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json([
            'products' => $query->latest()->get(),
        ]);
    }
}
