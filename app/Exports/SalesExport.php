<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\Order;
use App\Models\SaleBonus;
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

        $transactions = $this->transactionRows($sales, $onlineOrders)->sortBy('date')->values();
        $totalOnlineSales = $onlineOrders->sum('grand_total');
        $totalOnlineProfit = $onlineOrders->sum(fn ($order) => $order->items->sum(fn ($item) => ((float) $item->price - (float) $item->purchase_price) * (int) $item->qty));
        $onlineMarketingFee = $onlineOrders->sum(fn ($order) => (float) ($order->marketing_fee_before_discount ?? 0));

        $offlineFeeSales = $this->calcFeeSales($sales);
        $feeSales      = $offlineFeeSales + $onlineMarketingFee;
        $totalSales    = ($sales->sum('grand_total') - $offlineFeeSales) + ($totalOnlineSales - $onlineMarketingFee);
        $totalProfit   = $sales->sum('benefit') + $totalOnlineProfit;
        $bonusLoss     = SaleBonus::whereBetween('created_at', [$this->from, $this->to])->sum('benefit');
        $totalExpenses = Expense::whereBetween('entry_date', [$this->from, $this->to])->sum('amount');
        $totalFeeSales = $feeSales;

        $totalDiterima = $sales->sum('paid_amount') + $totalOnlineSales;
        $totalPiutang  = $sales->where('payment_status', '!=', 'paid')->sum('remaining_amount');

        return view('reports.excel', compact(
            'sales',
            'onlineOrders',
            'transactions',
            'totalSales',
            'totalProfit',
            'totalOnlineSales',
            'totalOnlineProfit',
            'totalFeeSales',
            'bonusLoss',
            'totalExpenses',
            'totalDiterima',
            'totalPiutang',
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
