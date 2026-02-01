<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        // Mengambil data pengeluaran milik user yang sedang login
        $expenses = Expense::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('master.expenses.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'entry_date' => 'required|date',
            'category' => 'nullable|string'
        ]);

        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'entry_date' => $request->entry_date,
            'description' => $request->description,
            'user_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
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
}
