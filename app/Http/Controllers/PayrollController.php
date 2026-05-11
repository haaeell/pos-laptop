<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Sale;
use App\Models\Service;
use App\Models\ServiceTechnician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('details.employee', 'releasedBy')
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        return view('payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();

        return view('payroll.create', compact('employees'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        // Ambil bulan & tahun dari date_from untuk keperluan label periode
        $year  = date('Y', strtotime($dateFrom));
        $month = date('n', strtotime($dateFrom));

        $employees    = Employee::where('is_active', true)->get();
        $calculations = [];

        foreach ($employees as $employee) {
            $salesBonus        = 0;
            $totalTransactions = 0;

            if ($employee->salesPerson) {
                $salesData = Sale::where('sales_person_id', $employee->salesPerson->id)
                    ->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                    ->get();

                $salesBonus        = $salesData->sum('fee_sales');
                $totalTransactions = $salesData->count();
            }

            $technicianFee = ServiceTechnician::where('employee_id', $employee->id)
                ->whereHas('service', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                        ->whereIn('status', ['done', 'taken']);
                })
                ->sum('fee_share');
            $netSalary     = $employee->basic_salary + $salesBonus + $technicianFee;

            $calculations[] = [
                'employee_id'       => $employee->id,
                'employee_number'   => $employee->employee_number,
                'full_name'         => $employee->full_name,
                'position'          => $employee->position,
                'basic_salary'      => $employee->basic_salary,
                'sales_bonus'       => $salesBonus,
                'technician_fee'    => $technicianFee,
                'net_salary'        => $netSalary,
                'total_transactions' => $totalTransactions,
            ];
        }

        return response()->json([
            'success'       => true,
            'data'          => $calculations,
            'total_payroll' => collect($calculations)->sum('net_salary'),
            'period_label'  => date('d M Y', strtotime($dateFrom)) . ' – ' . date('d M Y', strtotime($dateTo)),
            'period_year'   => $year,
            'period_month'  => $month,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'period_year'  => 'required|integer',
            'period_month' => 'required|integer|min:1|max:12',
            'date_from'    => 'required|date',
            'date_to'      => 'required|date',
            'release_date' => 'required|date',
            'employees'    => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $totalBasicSalary = collect($request->employees)->sum('basic_salary');
            $totalSalesBonus = collect($request->employees)->sum('sales_bonus');
            $totalTechnicianFee = collect($request->employees)->sum('technician_fee');
            $totalAmount = collect($request->employees)->sum('net_salary');

            $payroll = Payroll::create([
                'payroll_number'       => Payroll::generatePayrollNumber(
                    $request->period_year,
                    $request->period_month
                ),

                'period_year'          => $request->period_year,
                'period_month'         => $request->period_month,

                'date_from'            => $request->date_from,
                'date_to'              => $request->date_to,

                'release_date'         => $request->release_date,

                'total_amount'         => $totalAmount,
                'total_basic_salary'   => $totalBasicSalary,
                'total_sales_bonus'    => $totalSalesBonus,
                'total_technician_fee' => $totalTechnicianFee,

                'status'               => 'draft',
                'notes'                => $request->notes,
                'released_by'          => Auth::id(),
            ]);

            foreach ($request->employees as $empData) {
                PayrollDetail::create([
                    'payroll_id'         => $payroll->id,
                    'employee_id'        => $empData['employee_id'],
                    'basic_salary'       => $empData['basic_salary'],
                    'sales_bonus'        => $empData['sales_bonus'] ?? 0,
                    'technician_fee'     => $empData['technician_fee'] ?? 0,
                    'other_allowance'    => $empData['other_allowance'] ?? 0,
                    'deduction'          => $empData['deduction'] ?? 0,
                    'net_salary'         => $empData['net_salary'],
                    'total_transactions' => $empData['total_transactions'] ?? 0,
                ]);
            }
        });

        return redirect()->route('payrolls.index')->with('success', 'Penggajian berhasil dibuat');
    }

    public function release($id)
    {
        DB::transaction(function () use ($id) {
            $payroll = Payroll::findOrFail($id);

            $payroll->update([
                'status' => 'released',
                'released_by' => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Gaji berhasil dirilis');
    }

    public function printSlip($payrollId, $employeeId)
    {
        $payroll = Payroll::findOrFail($payrollId);
        $detail = PayrollDetail::where('payroll_id', $payrollId)
            ->where('employee_id', $employeeId)
            ->with('employee')
            ->firstOrFail();

        $pdf = Pdf::loadView('payroll.slip-pdf', compact('payroll', 'detail'));

        return $pdf->stream('slip-gaji-' . $detail->employee->employee_number . '.pdf');
    }

    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status === 'released') {
            return redirect()->back()->with('error', 'Gaji yang sudah dirilis tidak dapat dihapus');
        }

        $payroll->delete();

        return redirect()->back()->with('success', 'Penggajian berhasil dihapus');
    }
}
