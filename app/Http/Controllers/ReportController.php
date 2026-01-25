<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index', [
            'sales' => Sale::latest()->get()
        ]);
    }

    public function exportPdf()
    {
        return view('reports.pdf', [
            'sales' => Sale::latest()->get()
        ]);
    }

    public function exportExcel()
    {
        return view('reports.excel', [
            'sales' => Sale::latest()->get()
        ]);
    }
}
