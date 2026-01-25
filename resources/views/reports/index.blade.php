@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="bg-white rounded-xl space-y-6">

        <h1 class="text-2xl font-semibold text-slate-800">Laporan</h1>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">

                {{-- FORM FILTER --}}
                <form class="flex flex-wrap items-end gap-3">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Dari
                            Tanggal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-calendar-day text-xs"></i>
                            </div>
                            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                                class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none text-slate-700">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Sampai
                            Tanggal</label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-calendar-check text-xs"></i>
                            </div>
                            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                                class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none text-slate-700">
                        </div>
                    </div>

                    <button type="submit"
                        class="h-[40px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm shadow-indigo-100 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Terapkan
                    </button>
                </form>

                {{-- EXPORT BUTTONS --}}
                <div class="flex items-center gap-2">
                    <a href="/reports/pdf?from={{ $from }}&to={{ $to }}" target="_blank"
                        class="flex-1 lg:flex-none h-[40px] px-4 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-100 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-pdf"></i>
                        Cetak PDF
                    </a>
                    <a href="/reports/excel?from={{ $from }}&to={{ $to }}"
                        class="flex-1 lg:flex-none h-[40px] px-4 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-100 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-excel"></i>
                        Excel
                    </a>
                </div>
            </div>
        </div>
        {{-- SUMMARY --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Card Total Penjualan --}}
            <div
                class="p-6 bg-white rounded-2xl border-l-4 border-l-indigo-500 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Penjualan</p>
                    <h3 class="text-xl font-black text-slate-800">
                        Rp{{ number_format($totalSales, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>

            {{-- Card Profit --}}
            <div
                class="p-6 bg-white rounded-2xl border-l-4 border-l-emerald-500 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Keuntungan</p>
                    <h3 class="text-xl font-black text-emerald-600">
                        Rp{{ number_format($totalProfit, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-arrow-up-right-dots"></i>
                </div>
            </div>

            {{-- Card Bonus/Loss --}}
            <div
                class="p-6 bg-white rounded-2xl border-l-4 border-l-rose-500 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Bonus / (Loss)</p>
                    <h3 class="text-xl font-black text-rose-600">
                        Rp{{ number_format($bonusLoss, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-gift"></i>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="datatable" class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">#</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Invoice</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Tanggal</th>
                            <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Grand Total</th>
                            <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Profit</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Pembayaran</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 uppercase">
                        @foreach ($sales as $i => $sale)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="px-4 py-4 text-slate-500 whitespace-nowrap">{{ $i + 1 }}</td>

                                <td class="px-4 py-4 font-bold text-indigo-600 whitespace-nowrap">
                                    <span
                                        class="bg-indigo-50 px-2 py-1 rounded text-xs">#{{ $sale->invoice_number }}</span>
                                </td>

                                <td class="px-4 py-4 text-slate-600 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $sale->created_at->format('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $sale->created_at->format('H:i') }}
                                            WIB</span>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-right font-bold text-slate-800 whitespace-nowrap">
                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $sale->benefit >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $sale->benefit >= 0 ? '+' : '' }} Rp
                                        {{ number_format($sale->benefit, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 rounded-lg text-[10px] font-bold tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ strtoupper($sale->payment_method) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FOOT NOTE --}}
        <p class="text-xs text-slate-400">
            Profit bersih = Profit – Bonus/Loss
        </p>

    </div>
@endsection
