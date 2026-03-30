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

        $totalSales = $sales->sum('grand_total');
        $totalProfit = $sales->sum('benefit');
        $bonusLoss = SaleBonus::whereBetween('created_at', [$from, $to])->sum('benefit');
        $totalExpenses = Expense::whereBetween('entry_date', [$from, $to])->sum('amount');
        $totalAsset = Product::where('status', 'available')->sum('purchase_price');

        return view('reports.index', compact(
            'sales',
            'from',
            'to',
            'totalSales',
            'totalProfit',
            'bonusLoss',
            'totalExpenses',
            'totalAsset',
        ));
    }
    public function pdf(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to = Carbon::parse($request->to)->endOfDay();

        $sales = Sale::whereBetween('created_at', [$from, $to])->orderBy('created_at')->get();

        $totalSales = $sales->sum('grand_total');
        $totalProfit = $sales->sum('benefit');
        $bonusLoss = SaleBonus::whereBetween('created_at', [$from, $to])->sum('benefit');

        $totalExpenses = Expense::whereBetween('entry_date', [$from, $to])->sum('amount');

        $pdf = Pdf::loadView('reports.pdf', compact(
            'sales',
            'from',
            'to',
            'totalSales',
            'totalProfit',
            'bonusLoss',
            'totalExpenses'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-penjualan.pdf');
    }

    public function excel(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        $filename = 'laporan-penjualan_' . $from->format('d-m-Y') . '_sd_' . $to->format('d-m-Y') . '.xlsx';

        return Excel::download(new SalesExport($from, $to), $filename);
    }
}
