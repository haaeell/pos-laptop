<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesExport implements FromView
{
    public function __construct(public $from, public $to) {}

    public function view(): View
    {
        return view('reports.excel', [
            'sales' => Sale::whereBetween('created_at', [$this->from, $this->to])
                ->orderBy('created_at')
                ->get()
        ]);
    }
}
