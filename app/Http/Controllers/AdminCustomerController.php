<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::query()
            ->withCount(['orders', 'addresses', 'favoriteProducts', 'reviews'])
            ->withSum('orders', 'grand_total')
            ->latest()
            ->get();

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
