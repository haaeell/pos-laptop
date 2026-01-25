<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        return view('sales.index', [
            'sales' => Sale::with('user')->latest()->get()
        ]);
    }

    public function create()
    {
        return view('sales.create', [
            'products' => Product::where('status', 'available')->get()
        ]);
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            $sale = Sale::create([
                'invoice_number' => 'INV-' . time(),
                'user_id' => Auth::id(),
                'total_amount' => $request->total_amount,
                'discount' => $request->discount ?? 0,
                'grand_total' => $request->grand_total,
                'benefit' => $request->benefit,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid'
            ]);

            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'final_price' => $item['final_price'],
                    'benefit' => $item['benefit']
                ]);

                Product::where('id', $item['product_id'])->update([
                    'status' => 'sold'
                ]);
            }
        });

        return redirect()->route('sales.index')->with('success', 'Transaksi berhasil');
    }

    public function show($id)
    {
        return view('sales.show', [
            'sale' => Sale::with('items.product', 'user')->findOrFail($id)
        ]);
    }

    public function print($id)
    {
        return view('sales.print', [
            'sale' => Sale::with('items.product', 'user')->findOrFail($id)
        ]);
    }
}
