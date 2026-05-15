<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\SaleBonus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Exports\ProfitLossExport;
use App\Models\Modal;
use App\Models\ModalCicilan;
use App\Models\Payroll;
use App\Models\Service;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now();

        $sales = Sale::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get();

        $feeSales = Sale::whereBetween('created_at', [$from, $to])
            ->whereHas('salesPerson', fn($q) => $q->whereNull('employee_id'))
            ->sum('fee_sales');
        $totalSales = $sales->sum('grand_total') - $feeSales;
        $totalProfit = $sales->sum('benefit');
        $bonusLoss = SaleBonus::whereBetween('created_at', [$from, $to])->sum('benefit');
        $totalExpenses = Expense::whereBetween('entry_date', [$from, $to])->sum('amount');
        $totalAsset = Product::where('status', 'available')->sum('purchase_price');
        $totalPenambahanModal = Modal::whereBetween('tanggal_pencairan', [$from, $to])->sum('nominal_pencairan');

        $totalCicilan = ModalCicilan::whereHas('modal', function ($q) {
            $q->whereNull('deleted_at');
        })->whereBetween('tanggal_bayar', [$from, $to])->sum('total_bayar');

        $totalGajiKaryawan = Payroll::whereNotNull('release_date')->whereBetween('release_date', [$from, $to])->sum('total_amount');
        $totalFeeSales = Sale::whereBetween('created_at', [$from, $to])->sum('fee_sales');
        $totalPurchaseServices = Service::whereNotNull('done_at')
            ->whereBetween('done_at', [$from, $to])
            ->sum('spare_part_cost');

        $totalJasaService = Service::whereNotNull('done_at')
            ->whereBetween('done_at', [$from, $to])
            ->sum('service_cost');

        $totalServices = Service::whereNotNull('done_at')
            ->whereBetween('done_at', [$from, $to])
            ->sum('total_cost');

        $sparePartHpp =  Service::whereNotNull('done_at')
            ->whereBetween('done_at', [$from, $to])
            ->sum('spare_part_hpp');

        $sparePartCost =  Service::whereNotNull('done_at')
            ->whereBetween('done_at', [$from, $to])
            ->sum('spare_part_cost');

        $profitService = $sparePartCost - $sparePartHpp;

        return view('reports.index', compact(
            'sales',
            'from',
            'to',
            'totalSales',
            'totalProfit',
            'bonusLoss',
            'totalExpenses',
            'totalAsset',
            'totalPenambahanModal',
            'totalCicilan',
            'totalGajiKaryawan',
            'totalFeeSales',
            'totalPurchaseServices',
            'totalJasaService',
            'totalServices',
            'profitService'
        ));
    }
    public function pdf(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        $sales = Sale::whereBetween('created_at', [$from, $to])->orderBy('created_at')->get();

        $feeSales = Sale::whereBetween('created_at', [$from, $to])
            ->whereHas('salesPerson', fn($q) => $q->whereNull('employee_id'))
            ->sum('fee_sales');

        $totalSales           = $sales->sum('grand_total') - $feeSales;
        $totalProfit          = $sales->sum('benefit');
        $totalFeeSales        = $sales->sum('fee_sales');
        $bonusLoss            = SaleBonus::whereBetween('created_at', [$from, $to])->sum('benefit');
        $totalExpenses        = Expense::whereBetween('entry_date', [$from, $to])->sum('amount');
        $totalAsset           = Product::where('status', 'available')->sum('purchase_price');
        $totalPenambahanModal = Modal::whereBetween('tanggal_pencairan', [$from, $to])->sum('nominal_pencairan');
        $totalCicilan         = ModalCicilan::whereHas('modal', fn($q) => $q->whereNull('deleted_at'))
            ->whereBetween('tanggal_bayar', [$from, $to])->sum('total_bayar');
        $totalGajiKaryawan    = Payroll::whereNotNull('release_date')->whereBetween('release_date', [$from, $to])->sum('total_amount');
        $totalJasaService     = Service::whereNotNull('done_at')->whereBetween('done_at', [$from, $to])->sum('service_cost');
        $totalServices        = Service::whereNotNull('done_at')->whereBetween('done_at', [$from, $to])->sum('total_cost');
        $sparePartHpp         = Service::whereNotNull('done_at')->whereBetween('done_at', [$from, $to])->sum('spare_part_hpp');
        $sparePartCost        = Service::whereNotNull('done_at')->whereBetween('done_at', [$from, $to])->sum('spare_part_cost');
        $profitService        = $sparePartCost - $sparePartHpp;

        $pdf = Pdf::loadView('reports.pdf', compact(
            'sales',
            'from',
            'to',
            'totalSales',
            'totalProfit',
            'totalFeeSales',
            'bonusLoss',
            'totalExpenses',
            'totalAsset',
            'totalPenambahanModal',
            'totalCicilan',
            'totalGajiKaryawan',
            'totalJasaService',
            'totalServices',
            'profitService',
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-keuangan.pdf');
    }

    public function excel(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        $filename = 'laporan-penjualan_' . $from->format('d-m-Y') . '_sd_' . $to->format('d-m-Y') . '.xlsx';

        return Excel::download(new SalesExport($from, $to), $filename);
    }
}
