<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\Expense;
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

        $totalSales    = $sales->sum('grand_total');
        $totalProfit   = $sales->sum('benefit');
        $bonusLoss     = SaleBonus::whereBetween('created_at', [$this->from, $this->to])->sum('benefit');
        $totalExpenses = Expense::whereBetween('entry_date', [$this->from, $this->to])->sum('amount');

        return view('reports.excel', compact(
            'sales',
            'totalSales',
            'totalProfit',
            'bonusLoss',
            'totalExpenses',
            'from',
            'to'
        ));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // #
            'B' => 20,  // Invoice
            'C' => 22,  // Tanggal
            'D' => 18,  // Grand Total
            'E' => 16,  // Profit
            'F' => 14,  // Pembayaran
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
