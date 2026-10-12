<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\SalesPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TradeInController extends Controller
{
    public function index()
    {
        return view('trade-ins.index', [
            'sales' => Sale::with(['items.product', 'tradeInProduct'])
                ->whereNotNull('trade_in_product_id')
                ->latest()
                ->get(),
        ]);
    }

    public function create()
    {
        return view('trade-ins.create', [
            'products' => Product::where('status', 'available')->where('stock', '>', 0)->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'salesPeople' => SalesPerson::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'sale_price' => 'required|numeric|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'sales_person_id' => 'nullable|exists:sales_peoples,id',
            'fee_sales' => 'nullable|numeric|min:0',
            'trade_in_name' => 'required|string|max:255',
            'trade_in_category_id' => 'required|exists:categories,id',
            'trade_in_brand_id' => 'nullable|exists:brands,id',
            'trade_in_value' => 'required|numeric|min:1',
            'trade_in_selling_price' => 'nullable|numeric|min:0',
            'trade_in_notes' => 'nullable|string',
            'payment_method' => 'required|in:cash,transfer,qris',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'nullable|numeric|min:0',
            'due_date' => 'required_if:payment_status,partial,unpaid|nullable|date|after_or_equal:today',
            'collateral' => [
                in_array($request->payment_status, ['partial', 'unpaid'], true) ? 'required' : 'nullable',
                'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120',
            ],
        ], [
            'collateral.required' => 'Jaminan wajib diunggah untuk pembayaran sebagian atau hutang.',
            'due_date.required_if' => 'Tanggal jatuh tempo wajib diisi.',
        ]);

        $salePrice = round((float) $data['sale_price'], 2);
        $tradeInValue = round((float) $data['trade_in_value'], 2);
        $cashDue = round($salePrice - $tradeInValue, 2);

        $status = $data['payment_status'];
        $paidAmount = $cashDue < 0 ? $cashDue : ($status === 'paid' ? $cashDue : ($status === 'partial' ? round((float) ($data['paid_amount'] ?? 0), 2) : 0));
        if (($status === 'partial' && ($paidAmount <= 0 || $paidAmount >= $cashDue))
            || ($cashDue <= 0 && $status !== 'paid')) {
            throw ValidationException::withMessages(['paid_amount' => 'Jumlah pembayaran sebagian harus lebih dari Rp 0 dan kurang dari sisa setelah tukar tambah.']);
        }
        $status = $cashDue <= 0 ? 'paid' : $status;

        $sale = DB::transaction(function () use ($request, $data, $salePrice, $tradeInValue, $paidAmount, $status) {
            $outgoing = Product::whereKey($data['product_id'])->lockForUpdate()->firstOrFail();
            if ($outgoing->status !== 'available' || $outgoing->stock < 1) {
                throw ValidationException::withMessages(['product_id' => 'Produk yang dijual sudah tidak tersedia.']);
            }

            $sale = Sale::create([
                'invoice_number' => 'TT-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'user_id' => $request->user()->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'sales_person_id' => $data['sales_person_id'] ?? null,
                'fee_sales' => $data['fee_sales'] ?? 0,
                'grand_total' => $salePrice,
                'benefit' => $salePrice - (float) $outgoing->purchase_price - (float) ($data['fee_sales'] ?? 0),
                'payment_method' => $data['payment_method'],
                'payment_status' => $status,
                'paid_amount' => $paidAmount,
                'trade_in_value' => $tradeInValue,
                'due_date' => $status === 'paid' ? null : $data['due_date'],
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $outgoing->id,
                'purchase_price' => $outgoing->purchase_price,
                'selling_price' => $outgoing->selling_price,
                'final_price' => $salePrice,
                'qty' => 1,
                'benefit' => $salePrice - (float) $outgoing->purchase_price,
            ]);

            if ($outgoing->stock > 1) {
                $outgoing->decrement('stock');
            } else {
                $outgoing->update(['stock' => 0, 'status' => 'sold']);
            }

            $received = Product::create([
                'product_code' => 'TT-'.str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT),
                'name' => $data['trade_in_name'],
                'category_id' => $data['trade_in_category_id'],
                'brand_id' => $data['trade_in_brand_id'] ?? null,
                'condition' => 'used',
                'purchase_price' => $tradeInValue,
                'selling_price' => $data['trade_in_selling_price'] ?? $tradeInValue,
                'status' => 'available',
                'stock' => 1,
                'weight' => 1000,
                'is_active' => false,
                'notes' => trim('Tukar tambah '.$sale->invoice_number.' - '.$data['customer_name'].'. '.($data['trade_in_notes'] ?? '')),
            ]);
            $sale->update(['trade_in_product_id' => $received->id]);

            if ($paidAmount !== 0.0) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'user_id' => $request->user()->id,
                    'amount' => $paidAmount,
                    'paid_at' => now()->toDateString(),
                    'note' => $paidAmount < 0 ? 'Toko membayar selisih tukar tambah' : ($status === 'paid' ? 'Pelunasan selisih tukar tambah' : 'DP selisih tukar tambah'),
                ]);
            }

            if ($request->hasFile('collateral')) {
                $sale->update(['collateral_path' => $request->file('collateral')->store('collateral', 'public')]);
            }

            return $sale;
        });

        return redirect()->route('trade-ins.index')->with('success', 'Tukar tambah '.$sale->invoice_number.' berhasil disimpan. Produk pelanggan sudah masuk Master Produk.');
    }
}
