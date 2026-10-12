<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Rental;
use App\Models\Sale;
use App\Models\ServiceTechnician;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Ambil bulan & tahun dari date_from untuk keperluan label periode
        $year = date('Y', strtotime($dateFrom));
        $month = date('n', strtotime($dateFrom));

        $calculations = $this->calculateEmployees($dateFrom, $dateTo);

        return response()->json([
            'success' => true,
            'data' => $calculations,
            'total_payroll' => collect($calculations)->sum('net_salary'),
            'period_label' => date('d M Y', strtotime($dateFrom)).' – '.date('d M Y', strtotime($dateTo)),
            'period_year' => $year,
            'period_month' => $month,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'release_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $calculations = collect($this->calculateEmployees($request->date_from, $request->date_to));
            if ($calculations->isEmpty()) {
                throw ValidationException::withMessages(['employees' => 'Tidak ada karyawan aktif untuk penggajian.']);
            }

            $year = (int) date('Y', strtotime($request->date_from));
            $month = (int) date('n', strtotime($request->date_from));
            $shareMethod = $calculations->first();

            $payroll = Payroll::create([
                'payroll_number' => Payroll::generatePayrollNumber(
                    $year,
                    $month
                ),

                'period_year' => $year,
                'period_month' => $month,

                'date_from' => $request->date_from,
                'date_to' => $request->date_to,

                'release_date' => $request->release_date,

                'total_amount' => $calculations->sum('net_salary'),
                'total_basic_salary' => $calculations->sum('basic_salary'),
                'total_sales_bonus' => $calculations->sum('sales_bonus'),
                'total_sales_profit_share' => $calculations->sum('sales_profit_share'),
                'total_rental_share' => $calculations->sum('rental_share'),
                'total_technician_fee' => $calculations->sum('technician_fee'),
                'sales_share_mode' => $shareMethod['sales_share_mode'],
                'sales_share_value' => $shareMethod['sales_share_value'],
                'rental_share_mode' => $shareMethod['rental_share_mode'],
                'rental_share_value' => $shareMethod['rental_share_value'],

                'status' => 'draft',
                'notes' => $request->notes,
                'released_by' => Auth::id(),
            ]);

            foreach ($calculations as $empData) {
                PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $empData['employee_id'],
                    'basic_salary' => $empData['basic_salary'],
                    'sales_bonus' => $empData['sales_bonus'],
                    'sales_profit_share' => $empData['sales_profit_share'],
                    'rental_share' => $empData['rental_share'],
                    'sales_transaction_count' => $empData['sales_transaction_count'],
                    'rental_transaction_count' => $empData['rental_transaction_count'],
                    'technician_fee' => $empData['technician_fee'],
                    'other_allowance' => 0,
                    'deduction' => 0,
                    'net_salary' => $empData['net_salary'],
                    'total_transactions' => $empData['total_transactions'],
                ]);
            }
        });

        return redirect()->route('payrolls.index')->with('success', 'Penggajian berhasil dibuat');
    }

    private function calculateEmployees(string $dateFrom, string $dateTo): array
    {
        $from = $dateFrom.' 00:00:00';
        $to = $dateTo.' 23:59:59';
        $settings = Setting::whereIn('key', [
            'payroll_sales_share_mode', 'payroll_sales_share_value',
            'payroll_rental_share_mode', 'payroll_rental_share_value',
        ])->pluck('value', 'key');
        $salesMode = $settings->get('payroll_sales_share_mode') === 'fixed' ? 'fixed' : 'percentage';
        $rentalMode = $settings->get('payroll_rental_share_mode') === 'fixed' ? 'fixed' : 'percentage';
        $salesValue = max(0, (float) $settings->get('payroll_sales_share_value', 0));
        $rentalValue = max(0, (float) $settings->get('payroll_rental_share_value', 0));
        if ($salesMode === 'percentage') {
            $salesValue = min($salesValue, 100);
        }
        if ($rentalMode === 'percentage') {
            $rentalValue = min($rentalValue, 100);
        }

        $sales = Sale::where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to])
            ->get(['sales_person_id', 'fee_sales', 'benefit']);
        $rentals = Rental::whereBetween('created_at', [$from, $to])->get(['rental_total']);

        $salesShare = $this->transactionShare(
            $sales, 'benefit', $salesMode, $salesValue
        );
        $rentalShare = $this->transactionShare(
            $rentals, 'rental_total', $rentalMode, $rentalValue
        );
        $salesByPerson = $sales->groupBy('sales_person_id');

        $calculations = [];
        foreach (Employee::where('is_active', true)->get() as $employee) {
            $marketingSales = $employee->sales_person_id
                ? $salesByPerson->get($employee->sales_person_id, collect())
                : collect();
            $salesBonus = $marketingSales->sum('fee_sales');
            $technicianFee = ServiceTechnician::where('employee_id', $employee->id)
                ->whereHas('service', function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, $to])
                        ->whereIn('status', ['done', 'taken']);
                })
                ->sum('fee_share');

            $calculations[] = [
                'employee_id' => $employee->id,
                'employee_number' => $employee->employee_number,
                'full_name' => $employee->full_name,
                'position' => $employee->position,
                'basic_salary' => $employee->basic_salary,
                'sales_bonus' => $salesBonus,
                'sales_profit_share' => $salesShare,
                'rental_share' => $rentalShare,
                'sales_share_mode' => $salesMode,
                'sales_share_value' => $salesValue,
                'rental_share_mode' => $rentalMode,
                'rental_share_value' => $rentalValue,
                'sales_transaction_count' => $sales->count(),
                'rental_transaction_count' => $rentals->count(),
                'technician_fee' => $technicianFee,
                'net_salary' => round((float) $employee->basic_salary + $salesBonus + $salesShare + $rentalShare + $technicianFee, 2),
                'total_transactions' => $marketingSales->count(),
            ];
        }

        return $calculations;
    }

    private function transactionShare(Collection $transactions, string $amountField, string $mode, float $value): float
    {
        $value = max(0, $value);
        if ($mode === 'fixed') {
            return round($transactions->count() * $value, 2);
        }

        $percentage = min($value, 100);

        return round($transactions->sum(
            fn ($transaction) => round(max(0, (float) $transaction->{$amountField}) * $percentage / 100, 2)
        ), 2);
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
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $contacts = \App\Models\Contact::all();

        $pdf = Pdf::loadView('payroll.slip-pdf', compact('payroll', 'detail', 'settings', 'contacts'));

        return $pdf->stream('slip-gaji-'.$detail->employee->employee_number.'.pdf');
    }

    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        return redirect()->back()->with('success', 'Penggajian berhasil dihapus');
    }
}
