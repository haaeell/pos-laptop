<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Expense;
use App\Models\Modal;
use App\Models\ModalCicilan;
use App\Models\Order;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleBonus;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        $trend = in_array($request->input('trend'), ['daily', 'monthly', 'yearly'], true)
            ? $request->input('trend')
            : 'daily';

        $sales = Sale::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get();

        $onlineOrders = $this->onlineOrders($from, $to);
        $transactions = $this->transactionRows($sales, $onlineOrders)->sortByDesc('date')->values();
        $metrics = $this->getMetrics($from, $to, $sales, $onlineOrders);
        $chartData = $this->chartData($from, $to, $transactions, $metrics, $trend);

        return view('reports.index', array_merge(compact('sales', 'onlineOrders', 'transactions', 'chartData', 'from', 'to', 'trend'), $metrics));
    }

    private function getMetrics(Carbon $from, Carbon $to, $sales, $onlineOrders = null): array
    {
        $onlineOrders ??= collect();
        $offlineFeeSales = $this->calcFeeSales($from, $to, $sales);
        $onlineMarketingFee = $this->onlineMarketingFee($onlineOrders);
        $feeSales   = $offlineFeeSales + $onlineMarketingFee;
        $totalOnlineSales = $onlineOrders->sum('grand_total');
        $totalOnlineProfit = $this->onlineProfit($onlineOrders);
        $totalOnlineShipping = $onlineOrders->sum('shipping_cost');
        $totalSales = ($sales->sum('grand_total') - $offlineFeeSales) + ($totalOnlineSales - $onlineMarketingFee);

        $totalDiterima = $sales->sum('paid_amount') + $totalOnlineSales;
        $totalPiutang  = $sales->where('payment_status', '!=', 'paid')->sum('remaining_amount');

        $jumlahLunas    = $sales->where('payment_status', 'paid')->count() + $onlineOrders->count();
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
            'jumlahOnline'         => $onlineOrders->count(),
            'totalOnlineSales'     => $totalOnlineSales,
            'totalOnlineProfit'    => $totalOnlineProfit,
            'totalOnlineShipping'  => $totalOnlineShipping,
            'totalOnlineMarketingFee' => $onlineMarketingFee,
            'totalFeeSales'        => $feeSales,
            'totalProfit'          => $sales->sum('benefit') + $totalOnlineProfit,
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

    private function onlineOrders(Carbon $from, Carbon $to)
    {
        return Order::with('items')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->orderByDesc('paid_at')
            ->get();
    }

    private function onlineProfit($onlineOrders): float|int
    {
        return $onlineOrders->sum(fn ($order) => $order->items->sum(function ($item) {
            return ((float) $item->price - (float) $item->purchase_price) * (int) $item->qty;
        }));
    }

    private function onlineMarketingFee($onlineOrders): float|int
    {
        return $onlineOrders->sum(fn ($order) => (float) ($order->marketing_fee_before_discount ?? 0));
    }

    private function transactionRows($sales, $onlineOrders)
    {
        $offlineRows = $sales->map(fn ($sale) => (object) [
            'invoice_number' => $sale->invoice_number,
            'source' => 'Kasir',
            'date' => $sale->created_at,
            'grand_total' => (float) $sale->grand_total,
            'benefit' => (float) $sale->benefit,
            'payment_method' => $sale->payment_method,
            'payment_status' => $sale->payment_status,
            'remaining_amount' => $sale->remaining_amount,
        ]);

        $onlineRows = $onlineOrders->map(fn ($order) => (object) [
            'invoice_number' => $order->order_number,
            'source' => 'Online',
            'date' => $order->paid_at ?? $order->created_at,
            'grand_total' => (float) $order->grand_total,
            'benefit' => (float) $order->items->sum(fn ($item) => ((float) $item->price - (float) $item->purchase_price) * (int) $item->qty),
            'payment_method' => $order->midtrans_payment_type ?: 'midtrans',
            'payment_status' => 'paid',
            'remaining_amount' => 0,
        ]);

        return $offlineRows->concat($onlineRows);
    }

    private function chartData(Carbon $from, Carbon $to, $transactions, array $metrics, string $trend = 'daily'): array
    {
        [$buckets, $keyFormat, $label, $unit] = $this->trendBuckets($from, $to, $trend);

        $dailyRows = $buckets->map(function (array $bucket) use ($transactions, $keyFormat) {
            $rows = $transactions->filter(fn ($row) => $row->date->format($keyFormat) === $bucket['key']);

            return [
                'label' => $bucket['label'],
                'kasir' => (float) $rows->where('source', 'Kasir')->sum('grand_total'),
                'online' => (float) $rows->where('source', 'Online')->sum('grand_total'),
                'profit' => (float) $rows->sum('benefit'),
            ];
        });

        return [
            'trend' => [
                'mode' => $trend,
                'label' => $label,
                'unit' => $unit,
            ],
            'daily' => [
                'labels' => $dailyRows->pluck('label')->values(),
                'kasir' => $dailyRows->pluck('kasir')->values(),
                'online' => $dailyRows->pluck('online')->values(),
                'profit' => $dailyRows->pluck('profit')->values(),
            ],
            'sources' => [
                'labels' => ['Kasir', 'Online'],
                'values' => [
                    (float) $transactions->where('source', 'Kasir')->sum('grand_total'),
                    (float) $transactions->where('source', 'Online')->sum('grand_total'),
                ],
            ],
            'cashflow' => [
                'labels' => ['Total Penjualan', 'Services', 'Modal', 'Pengeluaran', 'Cicilan', 'Gaji'],
                'values' => [
                    (float) $metrics['totalSales'],
                    (float) $metrics['totalServices'],
                    (float) $metrics['totalPenambahanModal'],
                    -1 * (float) $metrics['totalExpenses'],
                    -1 * (float) $metrics['totalCicilan'],
                    -1 * (float) $metrics['totalGajiKaryawan'],
                ],
            ],
        ];
    }

    private function trendBuckets(Carbon $from, Carbon $to, string $trend): array
    {
        if ($trend === 'yearly') {
            $years = collect(range((int) $from->format('Y'), (int) $to->format('Y')))
                ->map(fn (int $year) => [
                    'key' => (string) $year,
                    'label' => (string) $year,
                ]);

            return [$years, 'Y', 'Tahunan', 'tahun'];
        }

        if ($trend === 'monthly') {
            $months = collect(CarbonPeriod::create($from->copy()->startOfMonth(), '1 month', $to->copy()->startOfMonth()))
                ->map(fn (Carbon $date) => [
                    'key' => $date->format('Y-m'),
                    'label' => $date->format('M Y'),
                ]);

            return [$months, 'Y-m', 'Bulanan', 'bulan'];
        }

        $days = collect(CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay()))
            ->map(fn (Carbon $date) => [
                'key' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
            ]);

        return [$days, 'Y-m-d', 'Harian', 'hari'];
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

        $onlineOrders = $this->onlineOrders($from, $to);
        $transactions = $this->transactionRows($sales, $onlineOrders)->sortBy('date')->values();
        $metrics = $this->getMetrics($from, $to, $sales, $onlineOrders);

        $pdf = Pdf::loadView('reports.pdf', array_merge(compact('sales', 'onlineOrders', 'transactions', 'from', 'to'), $metrics))->setPaper('a4', 'portrait');

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
