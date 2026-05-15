<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceTechnician;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('technicians.employee', 'createdBy')
            ->orderByDesc('created_at')
            ->get();

        $spareProducts = Product::whereHas('category', fn($q) => $q->where('name', 'like', '%sparepart%'))
            ->where('status', 'available')
            ->where('stock', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'purchase_price', 'selling_price', 'stock', 'category_id']);

        return view('service.index', compact('services', 'spareProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'device_type'    => 'nullable|string|max:100',
            'device_brand'   => 'nullable|string|max:100',
            'device_sn'      => 'nullable|string|max:100',
            'complaint'      => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        $service = Service::create([
            'service_number' => Service::generateServiceNumber(),
            'customer_name'  => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'device_type'    => $request->device_type,
            'device_brand'   => $request->device_brand,
            'device_sn'      => $request->device_sn,
            'complaint'      => $request->complaint,
            'notes'          => $request->notes,
            'status'         => 'pending',
            'created_by'     => Auth::id(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id'             => $service->id,
                'service_number' => $service->service_number,
                'message'        => 'Data service berhasil ditambahkan',
            ]);
        }

        return redirect()->back()->with('success', 'Data service berhasil ditambahkan');
    }

    public function estimate(Request $request, $id)
    {
        $request->validate([
            'spare_parts'           => 'nullable|array',
            'spare_parts.*.product_id' => 'required_with:spare_parts|exists:products,id',
            'spare_parts.*.qty'        => 'required_with:spare_parts|integer|min:1',
            'service_cost'          => 'required|numeric|min:0',
            'technician_notes'      => 'nullable|string',
            'estimated_done'        => 'nullable|date',
        ]);

        $service = Service::findOrFail($id);

        $spareParts   = [];
        $totalSell    = 0;
        $totalHpp     = 0;

        foreach ($request->spare_parts ?? [] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $qty     = (int) $item['qty'];

            $subtotalSell = (int) ($product->selling_price  * $qty);
            $subtotalHpp  = (int) ($product->purchase_price * $qty);

            $spareParts[] = [
                'product_id'    => $product->id,
                'name'          => $product->name,
                'qty'           => $qty,
                'price_hpp'     => (int) $product->purchase_price,
                'price_sell'    => (int) $product->selling_price,
                'subtotal_hpp'  => $subtotalHpp,
                'subtotal_sell' => $subtotalSell,
            ];

            $totalSell += $subtotalSell;
            $totalHpp  += $subtotalHpp;
        }

        $serviceCost = (int) $request->service_cost;
        $totalCost   = $totalSell + $serviceCost;

        $service->update([
            'spare_parts'      => $spareParts,
            'spare_part_cost'  => $totalSell,
            'spare_part_hpp'   => $totalHpp,
            'service_cost'     => $serviceCost,
            'total_cost'       => $totalCost,
            'technician_notes' => $request->technician_notes,
            'estimated_done'   => $request->estimated_done,
            'status'           => 'estimated',
        ]);

        return redirect()->back()->with('success', 'Estimasi biaya berhasil disimpan');
    }

    public function confirm(Request $request, $id)
    {
        $request->validate([
            'decision'       => 'required|in:approved,rejected',
            'employee_ids'   => 'required_if:decision,approved|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $service = Service::findOrFail($id);

        DB::transaction(function () use ($request, $service) {
            if ($request->decision === 'approved') {
                $techCount  = count($request->employee_ids);
                $feePerTech = $techCount > 0
                    ? round($service->service_cost / $techCount, 2)
                    : 0;

                $service->technicians()->delete();

                foreach ($request->employee_ids as $empId) {
                    ServiceTechnician::create([
                        'service_id'  => $service->id,
                        'employee_id' => $empId,
                        'fee_share'   => $feePerTech,
                    ]);
                }

                $service->update(['status' => 'in_progress']);
            } else {
                $service->update(['status' => 'rejected']);
            }
        });

        $msg = $request->decision === 'approved'
            ? 'Service disetujui dan sedang dikerjakan'
            : 'Service dibatalkan';

        return redirect()->back()->with('success', $msg);
    }

    public function done($id)
    {
        Service::findOrFail($id)->update([
            'status'  => 'done',
            'done_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Service ditandai selesai');
    }

    public function taken($id)
    {
        $service = Service::findOrFail($id);

        DB::transaction(function () use ($service) {
            $service->update([
                'status'   => 'taken',
                'taken_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Barang telah diambil konsumen');
    }

    public function printReceive($id)
    {
        $service  = Service::with('createdBy')->findOrFail($id);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $contacts = \App\Models\Contact::all();

        $pdf = Pdf::loadView('service.nota-terima', compact('service', 'settings', 'contacts'));

        return $pdf->stream('nota-terima-' . $service->service_number . '.pdf');
    }

    /**
     * Cetak nota pengambilan (invoice final).
     */
    public function printPickup($id)
    {
        $service  = Service::with('technicians.employee', 'createdBy')->findOrFail($id);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $contacts = \App\Models\Contact::all();

        $pdf = Pdf::loadView('service.nota-ambil', compact('service', 'settings', 'contacts'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('nota-ambil-' . $service->service_number . '.pdf');
    }

    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data service dihapus');
    }
}
