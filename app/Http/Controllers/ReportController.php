<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Expense;
use App\Models\Modal;
use App\Models\ModalCicilan;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleBonus;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private Carbon $cutoff;

    public function __construct()
    {
        $this->cutoff = Carbon::create(2026, 5, 13)->startOfMonth();
    }

    public function index(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->startOfMonth();
        $to   = $request->to ? Carbon::parse($request->to)->endOfDay() : Carbon::now();

        $sales = Sale::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get();

        $metrics = $this->getMetrics($from, $to, $sales);

        return view('reports.index', array_merge(compact('sales', 'from', 'to'), $metrics));
    }
    private function getMetrics(Carbon $from, Carbon $to, $sales): array
    {
        $feeSales   = $this->calcFeeSales($from, $to, $sales);
        $totalSales = $sales->sum('grand_total') - $feeSales;

        $totalDiterima = $sales->sum('paid_amount') - $feeSales;
        $totalPiutang  = $sales->where('payment_status', '!=', 'paid')->sum('remaining_amount');

        $jumlahLunas    = $sales->where('payment_status', 'paid')->count();
        $jumlahSebagian = $sales->where('payment_status', 'partial')->count();
        $jumlahHutang   = $sales->where('payment_status', 'unpaid')->count();

        $sparePartHpp  = $this->serviceSum($from, $to, 'spare_part_hpp');
        $sparePartCost = $this->serviceSum($from, $to, 'spare_part_cost');
        $storeFee      = $this->serviceSum($from, $to, 'store_fee');

        return [
            'totalSales'           => $totalSales,
            'totalDiterima'        => $totalDiterima,
            'totalPiutang'         => $totalPiutang,
            'jumlahLunas'          => $jumlahLunas,
            'jumlahSebagian'       => $jumlahSebagian,
            'jumlahHutang'         => $jumlahHutang,
            'totalFeeSales'        => $sales->sum('fee_sales'),
            'totalProfit'          => $sales->sum('benefit'),
            'bonusLoss'            => SaleBonus::whereBetween('created_at', [$from, $to])->sum('benefit'),
            'totalExpenses'        => Expense::whereBetween('entry_date', [$from, $to])->sum('amount'),
            'totalAsset'           => Product::where('status', 'available')->selectRaw('SUM(purchase_price * GREATEST(stock, 1)) as total')->value('total') ?? 0,
            'totalPenambahanModal' => Modal::whereBetween('tanggal_pencairan', [$from, $to])->sum('nominal_pencairan'),
            'totalCicilan'         => ModalCicilan::whereHas('modal', fn($q) => $q->whereNull('deleted_at'))->whereBetween('tanggal_bayar', [$from, $to])->sum('total_bayar'),
            'totalGajiKaryawan'    => Payroll::whereNotNull('release_date')->whereBetween('release_date', [$from, $to])->sum('total_amount'),
            'totalPurchaseServices' => $sparePartCost,
            'totalJasaService' => Service::whereNotNull('done_at')
                ->whereBetween('done_at', [$from, $to])
                ->selectRaw('SUM(service_cost - COALESCE(store_fee, 0)) as total')
                ->value('total') ?? 0,
            'totalServices'        => $this->serviceSum($from, $to, 'total_cost'),
            'profitService'        => $sparePartCost - $sparePartHpp + $storeFee,
        ];
    }

    private function calcFeeSales(Carbon $from, Carbon $to, $sales): float|int
    {
        if ($from->gte($this->cutoff)) {
            return Sale::whereBetween('created_at', [$from, $to])
                ->whereHas('salesPerson', fn($q) => $q->whereNull('employee_id'))
                ->sum('fee_sales');
        }

        return $sales->sum('fee_sales');
    }

    private function serviceSum(Carbon $from, Carbon $to, string $column): float|int
    {
        return Service::whereNotNull('done_at')->whereBetween('done_at', [$from, $to])->sum($column);
    }

    public function pdf(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        $sales = Sale::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $metrics = $this->getMetrics($from, $to, $sales);

        $pdf = Pdf::loadView('reports.pdf', array_merge(compact('sales', 'from', 'to'), $metrics))->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-keuangan.pdf');
    }

    public function excel(Request $request)
    {
        $from     = Carbon::parse($request->from)->startOfDay();
        $to       = Carbon::parse($request->to)->endOfDay();
        $filename = 'laporan-penjualan_' . $from->format('d-m-Y') . '_sd_' . $to->format('d-m-Y') . '.xlsx';

        return Excel::download(new SalesExport($from, $to), $filename);
    }
}
