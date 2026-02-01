<?php

namespace App\Http\Controllers;

use App\Models\SalesPerson;
use Illuminate\Http\Request;

class PenjualController extends Controller
{
    public function index()
    {
        $salesPerson = SalesPerson::withCount([
            'sales as total_penjualan' => function ($q) {
                $q->where('payment_status', 'paid');
            }
        ])->get();

        return view('master.salesPerson.index', compact('salesPerson'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        SalesPerson::create([
            'name' => $request->name,
            'phone' => $request->phone
        ]);

        return redirect()->back()->with('success', 'Sales berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required'
        ]);

        SalesPerson::findOrFail($id)->update([
            'name' => $request->name,
            'phone' => $request->phone
        ]);

        return redirect()->back()->with('success', 'Sales berhasil diperbarui');
    }

    public function destroy($id)
    {
        $salesPerson = SalesPerson::findOrFail($id);
        $salesPerson->delete();

        return redirect()->back()->with('success', 'Sales berhasil dihapus');
    }
}
