@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
    <div class="bg-white rounded-xl space-y-6">

        <h1 class="text-2xl font-semibold text-slate-800">Laporan Keuangan</h1>

        {{-- FILTER PANEL --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <form class="flex flex-wrap items-end gap-3">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Dari Tanggal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-calendar-day text-xs"></i>
                            </div>
                            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                                class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-700">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Sampai Tanggal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-calendar-check text-xs"></i>
                            </div>
                            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                                class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-700">
                        </div>
                    </div>

                    <button type="submit" class="h-[40px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Terapkan
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <a href="/reports/pdf?from={{ $from->format('Y-m-d') }}&to={{ $to->format('Y-m-d') }}" target="_blank"
                        class="flex-1 lg:flex-none h-[40px] px-4 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-100 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </a>
                    <a href="/reports/excel?from={{ $from->format('Y-m-d') }}&to={{ $to->format('Y-m-d') }}"
                        class="flex-1 lg:flex-none h-[40px] px-4 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-100 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Penjualan --}}
            <div class="p-5 bg-white rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-indigo-500">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Penjualan</p>
                <h3 class="text-lg font-black text-slate-800 mt-1">Rp{{ number_format($totalSales, 0, ',', '.') }}</h3>
            </div>

            {{-- Pengeluaran --}}
            <div class="p-5 bg-white rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-orange-500">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Pengeluaran</p>
                <h3 class="text-lg font-black text-orange-600 mt-1">Rp{{ number_format($totalExpenses, 0, ',', '.') }}</h3>
            </div>

            {{-- Keuntungan Kotor --}}
            <div class="p-5 bg-white rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-emerald-500">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Profit (Kotor)</p>
                <h3 class="text-lg font-black text-emerald-600 mt-1">Rp{{ number_format($totalProfit, 0, ',', '.') }}</h3>
            </div>

            {{-- Bonus/Loss --}}
            <div class="p-5 bg-white rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-rose-500">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bonus / (Loss)</p>
                <h3 class="text-lg font-black text-rose-600 mt-1">Rp{{ number_format($bonusLoss, 0, ',', '.') }}</h3>
            </div>
        </div>

        {{-- CALCULATION BOX --}}
        <div class="bg-indigo-900 rounded-2xl p-6 text-white flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <p class="text-indigo-200 text-sm font-medium">Estimasi Profit Bersih</p>
                <p class="text-xs text-indigo-300 italic">(Profit + Bonus/Loss) - Pengeluaran</p>
            </div>
            <div class="text-3xl font-black">
                Rp {{ number_format(($totalProfit + $bonusLoss) - $totalExpenses, 0, ',', '.') }}
            </div>
        </div>

        {{-- TABLE PENJUALAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <h2 class="font-bold text-slate-700 text-sm uppercase tracking-wider">Rincian Transaksi Penjualan</h2>
            </div>
            <div class="overflow-x-auto">
                <table id="datatable" class="w-full text-sm text-left">
                    <thead class="bg-white text-slate-500 border-b">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Invoice</th>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold text-right">Total</th>
                            <th class="px-6 py-4 font-semibold text-right">Profit</th>
                            <th class="px-6 py-4 font-semibold text-center">Metode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sales as $sale)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-indigo-600">#{{ $sale->invoice_number }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="{{ $sale->benefit >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                                        {{ $sale->benefit >= 0 ? '+' : '' }} Rp {{ number_format($sale->benefit, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold uppercase">{{ $sale->payment_method }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            "pageLength": 10,
            "order": [[ 1, "desc" ]],
            "language": {
                "search": "Cari Invoice:",
                "lengthMenu": "Tampilkan _MENU_ data"
            }
        });
    });
</script>
@endpush
