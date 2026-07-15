<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
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

        $onlineOrdersToday = Order::with('items')
            ->whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->get();

        $onlineProfitToday = $onlineOrdersToday->sum(fn ($order) => $order->items->sum(
            fn ($item) => ((float) $item->price - (float) $item->purchase_price) * (int) $item->qty
        ));

        // ===== KPI =====
        $totalSales = Sale::whereDate('created_at', $today)->sum('grand_total')
            + $onlineOrdersToday->sum('grand_total');
        $totalProfit = Sale::whereDate('created_at', $today)->sum('benefit') + $onlineProfitToday;
        $totalTransactions = Sale::whereDate('created_at', $today)->count() + $onlineOrdersToday->count();
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

        $onlineChart = Order::select(
            DB::raw('DATE(paid_at) as date'),
            DB::raw('SUM(grand_total) as total')
        )
            ->whereNotNull('paid_at')
            ->whereDate('paid_at', '>=', now()->subDays(6))
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $salesChart = $salesChart->map(function ($row) use ($onlineChart) {
            $date = (string) $row->date;
            $row->total = (float) $row->total + (float) ($onlineChart[$date]->total ?? 0);

            return $row;
        });

        foreach ($onlineChart as $date => $row) {
            if (!$salesChart->contains(fn ($r) => (string) $r->date === (string) $date)) {
                $salesChart->push((object) ['date' => $date, 'total' => (float) $row->total]);
            }
        }

        $salesChart = $salesChart->sortBy('date')->values();

        // ===== PAYMENT METHOD =====
        $paymentMethods = Sale::select('payment_method', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_method')
            ->get();

        $onlinePaymentCount = Order::whereNotNull('paid_at')
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->count();

        if ($onlinePaymentCount > 0) {
            $paymentMethods->push((object) [
                'payment_method' => 'online',
                'total' => $onlinePaymentCount,
            ]);
        }

        // ===== TOP PRODUCTS =====
        $topProducts = SaleItem::select(
            'product_id',
            DB::raw('COUNT(*) as total_sold'),
            DB::raw('SUM(benefit) as total_profit')
        )
            ->with('product')
            ->groupBy('product_id')
            ->get();

        $onlineTopProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotNull('orders.paid_at')
            ->whereIn('orders.status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereNotNull('order_items.product_id')
            ->select(
                'order_items.product_id',
                DB::raw('COUNT(*) as total_sold'),
                DB::raw('SUM((order_items.price - order_items.purchase_price) * order_items.qty) as total_profit')
            )
            ->groupBy('order_items.product_id')
            ->get();

        foreach ($onlineTopProducts as $onlineRow) {
            $existing = $topProducts->firstWhere('product_id', $onlineRow->product_id);

            if ($existing) {
                $existing->total_sold += $onlineRow->total_sold;
                $existing->total_profit += $onlineRow->total_profit;
            } else {
                $topProducts->push((object) [
                    'product_id' => $onlineRow->product_id,
                    'total_sold' => $onlineRow->total_sold,
                    'total_profit' => $onlineRow->total_profit,
                    'product' => Product::find($onlineRow->product_id),
                ]);
            }
        }

        $topProducts = $topProducts->sortByDesc('total_sold')->take(5)->values();

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
            ->get()
            ->keyBy('id');

        $onlineTopBrands = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->whereNotNull('orders.paid_at')
            ->whereIn('orders.status', ['paid', 'processing', 'shipped', 'completed'])
            ->select(
                'brands.id',
                'brands.name',
                DB::raw('COUNT(order_items.id) as total_sold'),
                DB::raw('SUM((order_items.price - order_items.purchase_price) * order_items.qty) as total_profit')
            )
            ->groupBy('brands.id', 'brands.name')
            ->get();

        foreach ($onlineTopBrands as $onlineRow) {
            if ($topBrands->has($onlineRow->id)) {
                $topBrands[$onlineRow->id]->total_sold += $onlineRow->total_sold;
                $topBrands[$onlineRow->id]->total_profit += $onlineRow->total_profit;
            } else {
                $topBrands->put($onlineRow->id, $onlineRow);
            }
        }

        $topBrands = $topBrands->sortByDesc('total_sold')->take(5)->values();


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
