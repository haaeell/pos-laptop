<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::where('user_id', Auth::id())->latest();

        // Filter tanggal
        if ($request->filled('from')) {
            $query->whereDate('entry_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('entry_date', '<=', $request->to);
        }

        $expenses = $query->get();

        return view('master.expenses.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'amount'     => 'required|numeric',
            'entry_date' => 'required|date',
            'category'   => 'nullable|string',
        ]);

        Expense::create([
            'title'       => $request->title,
            'amount'      => $request->amount,
            'category'    => $request->category,
            'entry_date'  => $request->entry_date,
            'description' => $request->description,
            'user_id'     => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'amount'     => 'required|numeric',
            'entry_date' => 'required|date',
        ]);

        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        $expense->update($request->all());

        return redirect()->back()->with('success', 'Pengeluaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        $expense->delete();

        return redirect()->back()->with('success', 'Data pengeluaran dihapus');
    }

    /**
     * Export data pengeluaran ke PDF (dengan filter tanggal opsional).
     */
    public function exportPdf(Request $request)
    {
        $query = Expense::where('user_id', Auth::id())->orderBy('entry_date');

        if ($request->filled('from')) {
            $query->whereDate('entry_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('entry_date', '<=', $request->to);
        }

        $expenses = $query->get();
        $total    = $expenses->sum('amount');
        $from     = $request->from;
        $to       = $request->to;

        $pdf = Pdf::loadView('master.expenses.pdf', compact('expenses', 'total', 'from', 'to'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-pengeluaran.pdf');
    }
}
