<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::orderBy('name')->get();

        return view('master.couriers.index', compact('couriers'));
    }

    public function toggleActive($id)
    {
        $courier = Courier::findOrFail($id);
        $courier->update(['is_active' => !$courier->is_active]);

        return redirect()->back()->with('success', 'Status kurir berhasil diperbarui');
    }

    public function updateLogo(Request $request, $id)
    {
        $courier = Courier::findOrFail($id);

        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        $courier->update([
            'logo' => $request->file('logo')->store('couriers', 'public'),
        ]);

        return redirect()->back()->with('success', 'Logo kurir berhasil diperbarui');
    }
}
