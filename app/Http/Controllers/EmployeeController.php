<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalesPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('salesPerson')
            ->orderBy('employee_number', 'desc')
            ->get();

        $salesPersons = SalesPerson::whereNull('employee_id')->get();

        return view('master.employee.index', compact('employees', 'salesPersons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'    => 'required|string|max:255',
            'position'     => 'required|string|max:100',
            'join_date'    => 'required|date',
            'birth_date'   => 'required|date',
            'phone'        => 'required|string|max:20',
            'address'      => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $employee = Employee::create([
                'employee_number' => Employee::generateEmployeeNumber(),
                'sales_person_id' => $request->sales_person_id ?: null,
                'full_name'       => $request->full_name,
                'position'        => $request->position,
                'join_date'       => $request->join_date,
                'birth_date'      => $request->birth_date,
                'phone'           => $request->phone,
                'address'         => $request->address,
                'bank_name'       => $request->bank_name,
                'account_number'  => $request->account_number,
                'basic_salary'    => $request->basic_salary,
                'is_active'       => true,
            ]);

            if ($request->sales_person_id) {
                SalesPerson::where('id', $request->sales_person_id)
                    ->update(['employee_id' => $employee->id]);
            } else {
                $salesPerson = SalesPerson::create([
                    'name'        => $employee->full_name,
                    'phone'       => $employee->phone,
                    'employee_id' => $employee->id,
                ]);

                $employee->update(['sales_person_id' => $salesPerson->id]);
            }
        });

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan dan otomatis terdaftar sebagai Sales');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'join_date' => 'required|date',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($id);

        DB::transaction(function () use ($request, $employee) {
            $oldSalesPersonId = $employee->sales_person_id;

            $employee->update([
                'sales_person_id' => $request->sales_person_id,
                'full_name' => $request->full_name,
                'position' => $request->position,
                'join_date' => $request->join_date,
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
                'address' => $request->address,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'basic_salary' => $request->basic_salary,
                'is_active' => $request->has('is_active'),
            ]);

            if ($oldSalesPersonId && $oldSalesPersonId != $request->sales_person_id) {
                SalesPerson::where('id', $oldSalesPersonId)
                    ->update(['employee_id' => null]);
            }

            if ($request->sales_person_id) {
                SalesPerson::where('id', $request->sales_person_id)
                    ->update(['employee_id' => $employee->id]);
            }
        });

        return redirect()->back()->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        DB::transaction(function () use ($employee) {
            if ($employee->sales_person_id) {
                SalesPerson::where('id', $employee->sales_person_id)->update(['employee_id' => null]);
            }

            $employee->delete();
        });

        return redirect()->back()->with('success', 'Karyawan berhasil dihapus');
    }
}
