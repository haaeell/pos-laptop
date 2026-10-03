<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalRateTier;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with('items')->latest()->get();
        $tiers = RentalRateTier::orderBy('min_qty')->get();

        return view('rentals.index', compact('rentals', 'tiers'));
    }

    public function create()
    {
        $products = Product::where('status', 'available')->where('stock', '>', 0)->orderBy('name')->get(['id', 'name', 'product_code', 'stock']);
        $tiers = RentalRateTier::orderBy('min_qty')->get();

        return view('rentals.create', compact('products', 'tiers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'renter_name' => 'required|string|max:255', 'address' => 'required|string', 'purpose' => 'required|string|max:255',
            'person_in_charge' => 'required|string|max:255', 'phone' => 'required|string|max:30',
            'rental_dates' => 'required|array|min:1', 'rental_dates.*' => 'required|date_format:Y-m-d|distinct', 'planned_return_date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1', 'items.*.product_id' => 'required|integer|distinct|exists:products,id', 'items.*.qty' => 'required|integer|min:1',
            'items.*.accessories' => 'nullable|string|max:255', 'items.*.condition_out' => 'nullable|string|max:255',
        ]);

        sort($data['rental_dates']);
        if ($data['planned_return_date'] < max($data['rental_dates'])) {
            throw ValidationException::withMessages(['planned_return_date' => 'Rencana pengembalian tidak boleh sebelum tanggal sewa terakhir.']);
        }

        $rental = DB::transaction(function () use ($data) {
            $items = collect($data['items'])->groupBy('product_id')->map(fn ($rows) => $rows->sum('qty'));
            $totalQty = $items->sum();
            $tier = RentalRateTier::where('min_qty', '<=', $totalQty)->orderByDesc('min_qty')->first();
            if (! $tier) {
                throw ValidationException::withMessages(['items' => 'Tarif sewa untuk jumlah unit ini belum diatur.']);
            }

            $products = Product::whereIn('id', $items->keys())->lockForUpdate()->get()->keyBy('id');
            foreach ($items as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product || $product->status !== 'available' || $product->stock < $qty) {
                    throw ValidationException::withMessages(['items' => "Stok {$product?->name} tidak mencukupi. Periksa kembali jumlah unit."]);
                }
            }

            $rental = Rental::create([
                ...$data,
                'rental_number' => Rental::generateNumber(), 'user_id' => Auth::id(), 'total_qty' => $totalQty,
                'daily_rate' => $tier->daily_rate, 'rental_total' => $totalQty * count($data['rental_dates']) * $tier->daily_rate,
            ]);
            foreach ($items as $productId => $qty) {
                $product = $products->get($productId);
                $input = collect($data['items'])->firstWhere('product_id', $productId);
                $rental->items()->create([
                    'product_id' => $product->id, 'product_name' => $product->name, 'product_code' => $product->product_code, 'qty' => $qty,
                    'accessories' => $input['accessories'] ?? 'Laptop dan Charger',
                    'condition_out' => $input['condition_out'] ?? 'Normal',
                ]);
                $product->decrement('stock', $qty);
            }

            return $rental;
        });

        return redirect()->route('rentals.index')->with('success', "Sewa {$rental->rental_number} berhasil dibuat.");
    }

    public function returnForm(Rental $rental)
    {
        abort_if($rental->status === 'returned', 422, 'Sewa sudah dikembalikan.');
        $rental->load('items');

        return view('rentals.return', compact('rental'));
    }

    public function markReturned(Request $request, Rental $rental)
    {
        abort_if($rental->status === 'returned', 422, 'Sewa sudah dikembalikan.');
        $data = $request->validate(['fine_amount' => 'nullable|numeric|min:0', 'return_notes' => 'nullable|string', 'items' => 'required|array', 'items.*.condition_in' => 'required|string|max:255', 'items.*.return_issue' => 'nullable|string']);
        DB::transaction(function () use ($rental, $data) {
            $rental->load('items');
            foreach ($rental->items as $item) {
                $input = $data['items'][$item->id] ?? abort(422, 'Data kondisi barang tidak lengkap.');
                $item->update(['condition_in' => $input['condition_in'], 'return_issue' => $input['return_issue'] ?? null]);
                Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail()->increment('stock', $item->qty);
            }
            $rental->update(['fine_amount' => $data['fine_amount'] ?? 0, 'return_notes' => $data['return_notes'] ?? null, 'status' => 'returned', 'returned_at' => now()]);
        });

        return redirect()->route('rentals.index')->with('success', 'Laptop telah ditandai dikembalikan.');
    }

    public function invoice(Rental $rental)
    {
        $rental->load('items', 'user');
        $settings = Setting::pluck('value', 'key')->toArray();
        $contacts = Contact::where('is_active', true)->get();

        return Pdf::loadView('rentals.invoice-pdf', compact('rental', 'settings', 'contacts'))->setPaper('a4')->stream("invoice-{$rental->rental_number}.pdf");
    }

    public function destroy(Rental $rental)
    {
        DB::transaction(function () use ($rental) {
            if ($rental->status === 'active') {
                $rental->load('items');
                foreach ($rental->items as $item) {
                    Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail()->increment('stock', $item->qty);
                }
            }

            $rental->delete();
        });

        return redirect()->route('rentals.index')->with('success', 'Data sewa dihapus. Stok dikembalikan untuk sewa yang dibatalkan.');
    }

    public function saveRates(Request $request)
    {
        $data = $request->validate(['tiers' => 'required|array|min:1', 'tiers.*.min_qty' => 'required|integer|min:1|distinct', 'tiers.*.daily_rate' => 'required|numeric|min:0']);
        RentalRateTier::query()->delete();
        foreach ($data['tiers'] as $tier) {
            RentalRateTier::create($tier);
        }

        return back()->with('success', 'Tarif sewa diperbarui.');
    }
}
