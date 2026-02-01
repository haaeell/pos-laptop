<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleBonus;
use App\Models\SaleItem;
use App\Models\SalesPerson;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

        return view('sales.index', [
            'sales' => $query->get()
        ]);
    }


    public function create()
    {
        return view('sales.create', [
            'products' => Product::where('status', 'available')->get(),
            'salesPeople' => SalesPerson::all()
        ]);
    }

    public function detail($id)
    {
        $sale = Sale::with(['items', 'bonuses', 'salesPerson'])->findOrFail($id);
        return response()->json([
            'invoice'      => $sale->invoice_number,
            'date'         => Carbon::parse($sale->created_at)
                ->translatedFormat('d M Y H:i'),

            'grand_total'  => $this->rupiah($sale->grand_total),
            'benefit'      => $this->rupiah($sale->benefit),
            'fee_sales'    => number_format($sale->fee_sales ?? 0, 0, ',', '.'),

            'sales_name'   => $sale->salesPerson?->name,
            'sales_phone' => $sale->salesPerson?->phone,

            'items'        => $sale->items->map(function ($item) {
                return [
                    'name'     => $item->product->name,
                    'price'    => $this->rupiah($item->final_price),
                    'benefit'  => $this->rupiah($item->benefit),
                ];
            }),

            'bonuses'      => $sale->bonuses->map(function ($bonus) {
                return [
                    'name'     => $bonus->product->name,
                    'benefit'  => $this->rupiah($bonus->benefit),
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
        $sale = DB::transaction(function () use ($request) {

            $sale = Sale::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(Sale::count() + 1, 4, '0', STR_PAD_LEFT),
                'user_id'        => Auth::id(),
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'sales_person_id' => $request->sales_person_id,
                'fee_sales'       => $request->fee_sales ?? 0,
                'grand_total'    => $request->grand_total,
                'benefit'        => $request->benefit,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
            ]);

            // ================= SOLD ITEMS =================
            foreach ($request->items as $item) {

                SaleItem::create([
                    'sale_id'         => $sale->id,
                    'product_id'      => $item['product_id'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price'  => $item['selling_price'],
                    'final_price'    => $item['final_price'],
                    'benefit'        => $item['final_price'] - $item['purchase_price'],
                ]);

                Product::where('id', $item['product_id'])
                    ->update(['status' => 'sold']);
            }

            // ================= BONUS ITEMS =================
            if ($request->filled('bonus_products')) {
                foreach ($request->bonus_products as $productId) {

                    $product = Product::findOrFail($productId);

                    SaleBonus::create([
                        'sale_id'        => $sale->id,
                        'product_id'     => $product->id,
                        'purchase_price' => $product->purchase_price,
                        'benefit'        => -$product->purchase_price,
                    ]);

                    Product::where('id', $product->id)
                        ->update(['status' => 'bonus']);
                }
            }

            return $sale;
        });

        return redirect()
            ->route('sales.create')
            ->with('success_sale_id', $sale->id);
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
