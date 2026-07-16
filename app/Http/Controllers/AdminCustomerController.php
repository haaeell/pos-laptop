<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()
            ->withCount(['orders', 'addresses', 'favoriteProducts', 'reviews'])
            ->withSum('orders', 'grand_total');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'oldest' => $query->oldest(),
                'most_orders' => $query->orderByDesc('orders_count')->latest(),
                'highest_spent' => $query->orderByDesc('orders_sum_grand_total')->latest(),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $customers = $query->paginate(12)->withQueryString();

        $statsBase = Customer::query();

        return view('master.customers.index', [
            'customers' => $customers,
            'stats' => [
                'totalCustomers' => (clone $statsBase)->count(),
                'activeCustomers' => (clone $statsBase)->where('is_active', true)->count(),
                'customersWithOrders' => Customer::has('orders')->count(),
                'totalCustomerOrders' => Order::count(),
                'totalCustomerRevenue' => Order::whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->sum('grand_total'),
            ],
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'sort' => $request->string('sort')->toString(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $customer = Customer::with([
            'addresses',
            'cartItems.product',
            'favoriteProducts' => fn ($query) => $query->latest()->take(8),
            'reviews' => fn ($query) => $query->with('product')->latest()->take(8),
            'orders' => fn ($query) => $query->withCount('items')->latest(),
        ])
            ->withCount(['orders', 'addresses', 'favoriteProducts', 'reviews'])
            ->findOrFail($id);

        $paidStatuses = ['paid', 'processing', 'shipped', 'completed'];

        $orderStats = [
            'totalSpent' => $customer->orders()->whereIn('status', $paidStatuses)->sum('grand_total'),
            'averageOrderValue' => $customer->orders()->whereIn('status', $paidStatuses)->avg('grand_total') ?? 0,
            'pendingOrders' => $customer->orders()->where('status', 'pending_payment')->count(),
            'completedOrders' => $customer->orders()->where('status', 'completed')->count(),
            'cancelledOrders' => $customer->orders()->whereIn('status', ['cancelled', 'expired', 'failed'])->count(),
            'pickupOrders' => $customer->orders()->where('delivery_method', 'pickup')->count(),
            'shippingOrders' => $customer->orders()->where('delivery_method', 'shipping')->count(),
            'reviewAverage' => round((float) ($customer->reviews()->avg('rating') ?? 0), 1),
        ];

        return view('master.customers.show', [
            'customer' => $customer,
            'orderStats' => $orderStats,
        ]);
    }
}
