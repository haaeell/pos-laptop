<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleBonus;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\SalesPerson;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('user')->latest();

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        return view('sales.index', [
            'sales' => $query->get(),
            'paymentStatusFilter' => $request->payment_status,
        ]);
    }


    public function create()
    {
        return view('sales.create', [
            'products' => Product::where('status', 'available')->get(),
            'productBonus' => Product::where('status', 'bonus')->where('stock', '>', 0)->get(),
            'salesPeople' => SalesPerson::all()
        ]);
    }

    public function detail($id)
    {
        $sale = Sale::with(['items', 'bonuses', 'salesPerson', 'payments.user'])->findOrFail($id);
        return response()->json([
            'invoice' => $sale->invoice_number,
            'date' => Carbon::parse($sale->created_at)
                ->translatedFormat('d M Y H:i'),

            'grand_total' => $this->rupiah($sale->grand_total),
            'benefit' => $this->rupiah($sale->benefit),
            'fee_sales' => number_format($sale->fee_sales ?? 0, 0, ',', '.'),

            'payment_method' => $sale->payment_method,
            'payment_status' => $sale->payment_status,
            'paid_amount' => $this->rupiah($sale->paid_amount),
            'remaining_amount' => $this->rupiah($sale->remaining_amount),
            'due_date' => $sale->due_date?->translatedFormat('d M Y'),
            'collateral_url' => $sale->collateral_url,

            'payments' => $sale->payments->sortByDesc('paid_at')->values()->map(function ($payment) {
                return [
                    'date' => $payment->paid_at->translatedFormat('d M Y'),
                    'amount' => $this->rupiah($payment->amount),
                    'note' => $payment->note,
                    'by' => $payment->user?->name,
                ];
            }),

            'sales_name' => $sale->salesPerson?->name,
            'sales_phone' => $sale->salesPerson?->phone,

            'customer_name' => $sale->customer_name,
            'customer_phone' => $sale->customer_phone,

            'items' => $sale->items->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'price' => $this->rupiah($item->final_price),
                    'benefit' => $this->rupiah($item->benefit),
                ];
            }),

            'bonuses' => $sale->bonuses->map(function ($bonus) {
                return [
                    'name' => $bonus->product->name,
                    'benefit' => $this->rupiah($bonus->benefit),
                ];
            }),
        ]);
    }

    protected function rupiah($value): string
    {
        return number_format($value, 0, ',', '.');
    }


    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,transfer,qris',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->payment_status === 'partial' && (float) $value <= 0) {
                        $fail('Jumlah bayar wajib diisi untuk transaksi bayar sebagian.');
                    }
                },
            ],
            'due_date' => 'required_if:payment_status,partial,unpaid|nullable|date|after_or_equal:today',
            'collateral' => [
                in_array($request->payment_status, ['partial', 'unpaid']) ? 'required' : 'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ], [
            'collateral.required' => 'Upload jaminan (KTP) wajib diisi untuk transaksi bayar sebagian/hutang.',
            'due_date.required_if' => 'Tanggal jatuh tempo wajib diisi untuk transaksi bayar sebagian/hutang.',
        ]);

        $sale = DB::transaction(function () use ($request) {

            $grandTotal = (float) $request->grand_total;

            if ($request->payment_status === 'partial') {
                $paidAmount = min(max((float) $request->paid_amount, 0), $grandTotal);
            } elseif ($request->payment_status === 'unpaid') {
                $paidAmount = 0;
            } else {
                $paidAmount = $grandTotal;
            }

            $paymentStatus = $paidAmount >= $grandTotal ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');
            $dueDate = $paymentStatus !== 'paid' ? $request->due_date : null;

            $collateralPath = $request->hasFile('collateral')
                ? $request->file('collateral')->store('collateral', 'public')
                : null;

            $sale = Sale::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(Sale::count() + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'sales_person_id' => $request->sales_person_id,
                'fee_sales' => $request->fee_sales ?? 0,
                'grand_total' => $grandTotal,
                'benefit' => $request->benefit,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'paid_amount' => $paidAmount,
                'collateral_path' => $collateralPath,
                'due_date' => $dueDate,
            ]);

            if ($paidAmount > 0) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'user_id' => Auth::id(),
                    'amount' => $paidAmount,
                    'paid_at' => now()->toDateString(),
                    'note' => $paymentStatus === 'paid' ? 'Pembayaran lunas' : 'Pembayaran awal (DP)',
                ]);
            }

            // ================= SOLD ITEMS =================
            foreach ($request->items as $item) {
                $qty = max(1, (int) ($item['qty'] ?? 1));

                SaleItem::create([
                    'sale_id'        => $sale->id,
                    'product_id'     => $item['product_id'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price'  => $item['selling_price'],
                    'final_price'    => $item['final_price'],
                    'qty'            => $qty,
                    'benefit'        => ($item['final_price'] - $item['purchase_price']) * $qty,
                ]);

                $product = Product::findOrFail($item['product_id']);

                if ($product->stock > $qty) {
                    $product->decrement('stock', $qty);
                } else {
                    $product->update([
                        'status' => 'sold',
                        'stock'  => 0,
                    ]);
                }
            }
            // ================= BONUS ITEMS =================
            if ($request->filled('bonus_products')) {
                foreach ($request->bonus_products as $productId) {

                    $product = Product::findOrFail($productId);

                    SaleBonus::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'purchase_price' => $product->purchase_price,
                        'benefit' => -$product->purchase_price,
                    ]);

                    $product->decrement('stock', 1);
                }
            }

            return $sale;
        });

        return redirect()
            ->route('sales.create')
            ->with('success_sale_id', $sale->id);
    }

    public function pay(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        if ($sale->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Transaksi ini sudah lunas.');
        }

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $sale->remaining_amount],
            'paid_at' => 'nullable|date',
        ], [
            'amount.max' => 'Jumlah bayar tidak boleh melebihi sisa tagihan (Rp ' . $this->rupiah($sale->remaining_amount) . ').',
        ]);

        DB::transaction(function () use ($request, $sale) {
            SalePayment::create([
                'sale_id' => $sale->id,
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'paid_at' => $request->paid_at ?? now()->toDateString(),
                'note' => $request->note,
            ]);

            $newPaidAmount = $sale->paid_amount + $request->amount;

            $sale->update([
                'paid_amount' => $newPaidAmount,
                'payment_status' => $newPaidAmount >= $sale->grand_total ? 'paid' : 'partial',
                'due_date' => $newPaidAmount >= $sale->grand_total ? null : $sale->due_date,
            ]);
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Pembayaran cicilan berhasil dicatat.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $sale = Sale::with(['items', 'bonuses'])->findOrFail($id);

            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if (!$product) continue;

                $qty = $item->qty ?? 1;

                if ($product->status === 'available') {
                    $product->increment('stock', $qty);
                } else {
                    $product->update([
                        'status' => 'available',
                        'stock'  => $qty,
                    ]);
                }
            }

            foreach ($sale->bonuses as $bonus) {
                Product::where('id', $bonus->product_id)
                    ->increment('stock', 1);
            }

            if ($sale->collateral_path) {
                Storage::disk('public')->delete($sale->collateral_path);
            }

            $sale->items()->delete();
            $sale->bonuses()->delete();
            $sale->payments()->delete();
            $sale->delete();
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Transaksi berhasil dihapus dan stok produk telah dikembalikan.');
    }

    public function invoicePdf($id)
    {
        $sale = Sale::with(['items.product', 'bonuses.product', 'user'])
            ->findOrFail($id);

        $contacts = Contact::where('is_active', 1)->get();
        $settings = Setting::pluck('value', 'key');

        return view('sales.invoice-pdf', compact('sale', 'contacts', 'settings'));
    }
}
