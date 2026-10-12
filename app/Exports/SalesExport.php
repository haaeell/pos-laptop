<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\Order;
use App\Models\SaleBonus;
use App\Models\Rental;
use App\Models\Modal;
use App\Models\ModalCicilan;
use App\Models\Payroll;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromView, WithColumnWidths, WithStyles
{
    public function __construct(public $from, public $to) {}

    public function view(): View
    {
        $from = $this->from;
        $to   = $this->to;

        $sales = Sale::whereBetween('created_at', [$this->from, $this->to])
            ->orderBy('created_at')
            ->get();

        $onlineOrders = Order::with('items')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$this->from, $this->to])
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->orderBy('paid_at')
            ->get();
        $rentals = Rental::whereBetween('created_at', [$this->from, $this->to])->get();

        $transactions = $this->transactionRows($sales, $onlineOrders, $rentals)->sortBy('date')->values();
        $totalOnlineSales = $onlineOrders->sum('grand_total');
        $totalOnlineProfit = $onlineOrders->sum(fn ($order) => $order->items->sum(fn ($item) => ((float) $item->price - (float) $item->purchase_price) * (int) $item->qty));
        $onlineMarketingFee = $onlineOrders->sum(fn ($order) => (float) ($order->marketing_fee_before_discount ?? 0));

        $offlineFeeSales = $this->calcFeeSales($sales);
        $feeSales      = $offlineFeeSales + $onlineMarketingFee;
        $totalRentalSales = $rentals->sum('rental_total');
        $totalSales    = ($sales->sum('grand_total') - $offlineFeeSales) + ($totalOnlineSales - $onlineMarketingFee) + $totalRentalSales;
        $totalProfit   = $sales->sum('benefit') + $totalOnlineProfit;
        $bonusLoss     = SaleBonus::whereBetween('created_at', [$this->from, $this->to])->sum('benefit');
        $totalExpenses = Expense::whereBetween('entry_date', [$this->from, $this->to])->sum('amount');
        $totalFeeSales = $feeSales;

        $totalDiterima = $sales->sum('paid_amount') + $totalOnlineSales + $totalRentalSales;
        $totalTradeInPurchases = $sales->sum('trade_in_value');
        $totalTradeInPayouts = abs($sales->where('paid_amount', '<', 0)->sum('paid_amount'));
        $totalPiutang = $sales->where('payment_status', '!=', 'paid')->sum('remaining_amount');
        $totalPenambahanModal = Modal::whereBetween('tanggal_pencairan', [$from, $to])->sum('nominal_pencairan');
        $totalServices = Service::whereNotNull('done_at')->whereBetween('done_at', [$from, $to])->sum('total_cost');
        $totalCicilan = ModalCicilan::whereHas('modal', fn ($query) => $query->whereNull('deleted_at'))
            ->whereBetween('tanggal_bayar', [$from, $to])->sum('total_bayar');
        $totalGajiKaryawan = Payroll::whereNotNull('release_date')->whereBetween('release_date', [$from, $to])->sum('total_amount');
        $totalSaldo = $totalSales - $totalTradeInPurchases - $totalPiutang
            - $totalExpenses + $totalPenambahanModal + $totalServices - $totalCicilan - $totalGajiKaryawan;

        return view('reports.excel', compact(
            'sales',
            'onlineOrders',
            'transactions',
            'totalSales',
            'totalProfit',
            'totalOnlineSales',
            'totalRentalSales',
            'totalOnlineProfit',
            'totalFeeSales',
            'bonusLoss',
            'totalExpenses',
            'totalTradeInPurchases',
            'totalTradeInPayouts',
            'totalPenambahanModal',
            'totalServices',
            'totalCicilan',
            'totalGajiKaryawan',
            'totalDiterima',
            'totalPiutang',
            'totalSaldo',
            'from',
            'to'
        ));
    }

    private function calcFeeSales($sales): float|int
    {
        $cutoff = \Carbon\Carbon::create(2026, 5, 13)->startOfMonth();

        if ($this->from->gte($cutoff)) {
            return Sale::whereBetween('created_at', [$this->from, $this->to])
                ->whereHas('salesPerson', fn($q) => $q->whereNull('employee_id'))
                ->sum('fee_sales');
        }

        return $sales->sum('fee_sales');
    }

    private function transactionRows($sales, $onlineOrders, $rentals = null)
    {
        $sales->load(['payments' => fn ($query) => $query->whereDate('paid_at', '<=', $this->to->toDateString())]);
        $sales->loadCount('payments');
        $offlineRows = $sales->map(function ($sale) {
            $paid = $sale->payments_count ? (float) $sale->payments->sum('amount')
                : max((float) $sale->paid_amount, $sale->payment_status === 'paid'
                    ? (float) $sale->grand_total - (float) $sale->trade_in_value : 0);
            $remaining = max(0, (float) $sale->grand_total - (float) $sale->trade_in_value - $paid);
            return (object) [
                'invoice_number' => $sale->invoice_number,
                'source' => $sale->trade_in_product_id ? 'Tukar Tambah' : 'Kasir',
                'date' => $sale->created_at,
                'grand_total' => (float) $sale->grand_total,
                'benefit' => (float) $sale->benefit,
                'payment_method' => $sale->payment_method,
                'payment_status' => $remaining <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'remaining_amount' => $remaining,
            ];
        });

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

        $rentalRows = ($rentals ?? collect())->map(fn ($rental) => (object) ['invoice_number' => $rental->rental_number, 'source' => 'Sewa', 'date' => $rental->created_at, 'grand_total' => (float) $rental->rental_total, 'benefit' => 0, 'payment_method' => 'sewa', 'payment_status' => 'paid', 'remaining_amount' => 0]);
        return $offlineRows->concat($onlineRows)->concat($rentalRows);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // #
            'B' => 20,  // Invoice
            'C' => 12,  // Sumber
            'D' => 22,  // Tanggal
            'E' => 18,  // Grand Total
            'F' => 16,  // Profit
            'G' => 14,  // Pembayaran
            'H' => 14,  // Status
            'I' => 18,  // Sisa Tagihan
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header judul
            1 => ['font' => ['bold' => true, 'size' => 14]],

            // Header kolom tabel (row 4)
            4 => ['font' => ['bold' => true]],
        ];
    }
}
