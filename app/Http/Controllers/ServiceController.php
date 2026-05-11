<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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

        return view('service.index', compact('services'));
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

        // Return JSON for AJAX (frontend will show SweetAlert with cetak struk)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id'             => $service->id,
                'service_number' => $service->service_number,
                'message'        => 'Data service berhasil ditambahkan',
            ]);
        }

        return redirect()->back()->with('success', 'Data service berhasil ditambahkan');
    }

    /**
     * Teknisi input estimasi biaya.
     * spare_parts dikirim sebagai array: [{ name, price }, ...]
     */
    public function estimate(Request $request, $id)
    {
        $request->validate([
            'spare_parts'           => 'nullable|array',
            'spare_parts.*.name'    => 'nullable|string|max:255',
            'spare_parts.*.price'   => 'nullable|numeric|min:0',
            'service_cost'          => 'required|numeric|min:0',
            'technician_notes'      => 'nullable|string',
            'estimated_done'        => 'nullable|date',
        ]);

        $service = Service::findOrFail($id);

        // Hitung total sparepart
        $spareParts = collect($request->spare_parts ?? [])
            ->filter(fn($sp) => !empty($sp['name']) && !empty($sp['price']))
            ->values()
            ->map(fn($sp) => [
                'name'  => $sp['name'],
                'price' => (int) $sp['price'],
            ])
            ->toArray();

        $spareCost = collect($spareParts)->sum('price');
        $total     = $spareCost + $request->service_cost;

        $service->update([
            'spare_parts'      => json_encode($spareParts),
            'spare_part_cost'  => $spareCost,
            'service_cost'     => $request->service_cost,
            'total_cost'       => $total,
            'technician_notes' => $request->technician_notes,
            'estimated_done'   => $request->estimated_done,
            'status'           => 'estimated',
        ]);

        return redirect()->back()->with('success', 'Estimasi biaya berhasil disimpan');
    }

    /**
     * Admin konfirmasi ke konsumen: approve / reject
     */
    public function confirm(Request $request, $id)
    {
        $request->validate([
            'decision'          => 'required|in:approved,rejected',
            'employee_ids'      => 'required_if:decision,approved|array',
            'employee_ids.*'    => 'exists:employees,id',
        ]);

        $service = Service::findOrFail($id);

        DB::transaction(function () use ($request, $service) {
            if ($request->decision === 'approved') {
                $technicianCount = count($request->employee_ids);
                $feePerTech      = $technicianCount > 0
                    ? round($service->service_cost / $technicianCount, 2)
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

    /**
     * Tandai selesai
     */
    public function done($id)
    {
        Service::findOrFail($id)->update([
            'status'  => 'done',
            'done_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Service ditandai selesai');
    }

    /**
     * Tandai sudah diambil konsumen
     */
    public function taken($id)
    {
        Service::findOrFail($id)->update([
            'status'   => 'taken',
            'taken_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Barang telah diambil konsumen');
    }

    /**
     * Cetak nota awal / tanda terima
     */
    public function printReceive($id)
    {
        $service  = Service::with('createdBy')->findOrFail($id);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $contacts = \App\Models\Contact::all();

        $pdf = Pdf::loadView('service.nota-terima', compact('service', 'settings', 'contacts'));

        return $pdf->stream('nota-terima-' . $service->service_number . '.pdf');
    }

    /**
     * Cetak nota pengambilan
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
