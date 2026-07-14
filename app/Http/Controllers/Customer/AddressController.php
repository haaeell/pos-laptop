<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Services\BiteshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    protected function customerId()
    {
        return Auth::guard('customers')->id();
    }

    public function index(Request $request)
    {
        $addresses = CustomerAddress::where('customer_id', $this->customerId())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['addresses' => $addresses]);
        }

        return view('customer.addresses.index', ['addresses' => $addresses]);
    }

    protected function rules(): array
    {
        return [
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:150',
            'recipient_phone' => 'required|string|max:20',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'address_detail' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    protected function resolveAreaId(BiteshipService $biteship, array $data): ?string
    {
        if (!$biteship->isConfigured()) {
            return null;
        }

        $areas = $biteship->searchArea($data['district'] . ' ' . $data['city']);

        return $areas[0]['id'] ?? null;
    }

    public function store(Request $request, BiteshipService $biteship)
    {
        $data = $request->validate($this->rules());
        $data['customer_id'] = $this->customerId();
        $data['area_id'] = $this->resolveAreaId($biteship, $data);

        $isFirst = CustomerAddress::where('customer_id', $this->customerId())->doesntExist();
        $data['is_default'] = $isFirst || $request->boolean('is_default');

        if ($data['is_default']) {
            CustomerAddress::where('customer_id', $this->customerId())->update(['is_default' => false]);
        }

        $address = CustomerAddress::create($data);

        if ($request->wantsJson()) {
            return response()->json(['address' => $address, 'message' => 'Alamat berhasil disimpan.'], 201);
        }

        return back()->with('success', 'Alamat berhasil disimpan.');
    }

    public function update(Request $request, $id, BiteshipService $biteship)
    {
        $address = CustomerAddress::where('customer_id', $this->customerId())->findOrFail($id);

        $data = $request->validate($this->rules());

        if ($data['province'] !== $address->province || $data['city'] !== $address->city || $data['district'] !== $address->district) {
            $data['area_id'] = $this->resolveAreaId($biteship, $data);
        }

        if ($request->boolean('is_default')) {
            CustomerAddress::where('customer_id', $this->customerId())->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address->update($data);

        if ($request->wantsJson()) {
            return response()->json(['address' => $address, 'message' => 'Alamat berhasil diperbarui.']);
        }

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function setDefault(Request $request, $id)
    {
        $address = CustomerAddress::where('customer_id', $this->customerId())->findOrFail($id);

        CustomerAddress::where('customer_id', $this->customerId())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Alamat utama berhasil diubah.']);
        }

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    public function destroy(Request $request, $id)
    {
        $address = CustomerAddress::where('customer_id', $this->customerId())->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = CustomerAddress::where('customer_id', $this->customerId())->first();
            $next?->update(['is_default' => true]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Alamat dihapus.']);
        }

        return back()->with('success', 'Alamat dihapus.');
    }
}
