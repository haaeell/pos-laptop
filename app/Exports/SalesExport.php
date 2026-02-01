<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\SaleBonus;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesExport implements FromView
{
    public function __construct(public $from, public $to) {}

    public function view(): View
    {
        $from = $this->from;
        $to = $this->to;
        $sales = Sale::whereBetween('created_at', [$this->from, $this->to])
            ->orderBy('created_at')
            ->get();

        $totalSales = $sales->sum('grand_total');
        $totalProfit = $sales->sum('benefit');
        $bonusLoss = SaleBonus::whereBetween('created_at', [$this->from, $this->to])->sum('benefit');
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
}

