<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalesPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $request->validate(['name' => 'required']);

        SalesPerson::create([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Sales berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);

        SalesPerson::findOrFail($id)->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Sales berhasil diperbarui');
    }

    public function destroy($id)
    {
        SalesPerson::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Sales berhasil dihapus');
    }

    // ✅ METHOD BARU
    public function promoteToEmployee(Request $request, $id)
    {
        $request->validate([
            'position'     => 'required|string|max:100',
            'join_date'    => 'required|date',
            'birth_date'   => 'required|date',
            'address'      => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'bank_name'    => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
        ]);

        $salesPerson = SalesPerson::findOrFail($id);

        // Cegah promosi ulang jika sudah jadi karyawan
        if ($salesPerson->employee_id) {
            return redirect()->back()->with('error', 'Sales ini sudah terdaftar sebagai karyawan.');
        }

        DB::transaction(function () use ($request, $salesPerson) {
            $employee = Employee::create([
                'employee_number' => Employee::generateEmployeeNumber(),
                'sales_person_id' => $salesPerson->id,
                'full_name'       => $salesPerson->name,
                'position'        => $request->position,
                'join_date'       => $request->join_date,
                'birth_date'      => $request->birth_date,
                'phone'           => $salesPerson->phone,
                'address'         => $request->address,
                'bank_name'       => $request->bank_name,
                'account_number'  => $request->account_number,
                'basic_salary'    => $request->basic_salary,
                'is_active'       => true,
            ]);

            $salesPerson->update(['employee_id' => $employee->id]);
        });

        return redirect()->back()->with('success', 'Sales berhasil diangkat menjadi karyawan.');
    }
}
