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
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Dari
                            Tanggal</label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-calendar-day text-xs"></i>
                            </div>
                            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                                class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-700">
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
                                class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-700">
                        </div>
                    </div>

                    <button type="submit"
                        class="h-[40px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all flex items-center gap-2">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Penjualan -->
            <div
                class="relative overflow-hidden p-5 bg-white rounded-2xl border border-slate-200 shadow-sm group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Penjualan</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-2">Rp{{ number_format($totalSales, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="浸13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-indigo-500"></div>
            </div>

            <!-- Fee Sales -->
            <div
                class="relative overflow-hidden p-5 bg-white rounded-2xl border border-slate-200 shadow-sm group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Fee Sales</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-2">Rp{{ number_format($totalFeeSales, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-indigo-500/50"></div>
            </div>

            <!-- Pengeluaran -->
            <div
                class="relative overflow-hidden p-5 bg-white rounded-2xl border border-slate-200 shadow-sm group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengeluaran</p>
                        <h3 class="text-xl font-bold text-rose-600 mt-2">Rp{{ number_format($totalExpenses, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2 bg-rose-50 rounded-lg text-rose-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-rose-500"></div>
            </div>

            <!-- Profit -->
            <div
                class="relative overflow-hidden p-5 bg-white rounded-2xl border border-slate-200 shadow-sm group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Profit</p>
                        <h3 class="text-xl font-bold text-emerald-600 mt-2">Rp{{ number_format($totalProfit, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-12c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4m0 5V3m0 18v-2" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-emerald-500"></div>
            </div>

            <!-- Bonus/Loss -->
            <div
                class="relative overflow-hidden p-5 bg-white rounded-2xl border border-slate-200 shadow-sm group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bonus / (Loss)</p>
                        <h3 class="text-xl font-bold text-amber-600 mt-2">Rp{{ number_format($bonusLoss, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-amber-500"></div>
            </div>

            <!-- Jasa Service -->
            <div
                class="relative overflow-hidden p-5 bg-white rounded-2xl border border-slate-200 shadow-sm group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jasa Service</p>
                        <h3 class="text-xl font-bold text-cyan-600 mt-2">
                            Rp{{ number_format($totalJasaService, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 bg-cyan-50 rounded-lg text-cyan-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-cyan-500"></div>
            </div>

            <!-- Gaji Karyawan -->
            <div
                class="relative overflow-hidden p-5 bg-white rounded-2xl border border-slate-200 shadow-sm group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Gaji Karyawan <span
                                class="text-[9px] text-slate-400 lowercase italic">(Tanpa Fee)</span></p>
                        <h3 class="text-xl font-bold text-slate-800 mt-2">
                            Rp{{ number_format($totalGajiKaryawan, 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-slate-400"></div>
            </div>

            <!-- Sparepart -->
            <div
                class="relative overflow-hidden p-5 bg-white rounded-2xl border border-violet-500 shadow-sm group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Profit Service <span
                                class="text-[9px] text-slate-400 lowercase italic">(Service)</span></p>
                        <h3 class="text-xl font-bold text-slate-800 mt-2">
                            Rp{{ number_format($profitService, 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-slate-400"></div>
            </div>
        </div>

        {{-- CALCULATION BOX --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

            <div class="px-5 py-4 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-500">Jumlah Saldo</p>
            </div>

            <div class="divide-y divide-slate-100">

                @php
                    $rows = [
                        ['label' => 'Total Penjualan', 'value' => $totalSales, 'plus' => true],
                        ['label' => 'Penambahan Modal', 'value' => $totalPenambahanModal, 'plus' => true],
                        ['label' => 'Total Services', 'value' => $totalServices, 'plus' => true],
                        ['label' => 'Total Pengeluaran', 'value' => $totalExpenses, 'plus' => false],
                        ['label' => 'Cicilan Modal', 'value' => $totalCicilan, 'plus' => false],
                        ['label' => 'Gaji Karyawan', 'value' => $totalGajiKaryawan, 'plus' => false],
                    ];
                @endphp

                @foreach ($rows as $row)
                    <div class="flex items-center justify-between px-5 py-3 {{ $loop->even ? 'bg-slate-50' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-1.5 rounded-full {{ $row['plus'] ? 'bg-emerald-600' : 'bg-red-600' }}"></div>
                            <span class="text-sm text-slate-700">{{ $row['label'] }}</span>
                        </div>
                        <span class="text-sm font-medium {{ $row['plus'] ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ $row['plus'] ? '+' : '−' }} Rp {{ number_format($row['value'], 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach

            </div>

            <div class="flex items-center justify-between px-5 py-4 border-t border-slate-200 bg-indigo-900">
                <span class="text-sm font-medium text-indigo-200">Jumlah Saldo</span>
                <span class="text-2xl font-bold text-white">
                    Rp
                    {{ number_format($totalSales - $totalExpenses + $totalPenambahanModal + $totalServices - $totalCicilan - $totalGajiKaryawan, 0, ',', '.') }}
                </span>
            </div>

        </div>

        {{-- TOTAL ASET --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">

            <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-amber-900 opacity-[0.06]"></div>
            <div class="absolute -bottom-12 right-10 w-24 h-24 rounded-full bg-amber-800 opacity-[0.05]"></div>

            <div class="flex justify-between items-start gap-4 relative">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class="fa-solid fa-building-columns text-amber-800 text-sm"></i>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Asset</span>
                    </div>
                    <p class="text-xs text-slate-400 italic">Nominal keseluruhan aset produk tersedia</p>
                </div>

                <div class="text-right shrink-0">
                    <p class="text-[11px] text-slate-400 mb-0.5">Rp</p>
                    <p class="text-2xl font-semibold text-slate-800">{{ number_format($totalAsset, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-xs text-slate-500">Dihitung dari produk berstatus
                    <span class="font-medium text-slate-700">Tersedia</span>,
                    stok lebih dari 1 dikalikan jumlah stok
                </span>
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
                    <tbody class=" divide-y divide-slate-100">
                        @foreach ($sales as $sale)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-indigo-600">#{{ $sale->invoice_number }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right font-bold">Rp
                                    {{ number_format($sale->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="{{ $sale->benefit >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                                        {{ $sale->benefit >= 0 ? '+' : '' }} Rp {{ number_format($sale->benefit, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold uppercase">{{ $sale->payment_method }}</span>
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
        $(document).ready(function () {
            $('#datatable').DataTable({
                "pageLength": 10,
                "order": [[1, "desc"]],
                "language": {
                    "search": "Cari Invoice:",
                    "lengthMenu": "Tampilkan _MENU_ data"
                }
            });
        });
    </script>
@endpush