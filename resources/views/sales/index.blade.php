@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Penjualan</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Penjualan</li>
                    </ol>
                </nav>
            </div>

            <a href="{{ route('sales.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                + Transaksi Baru
            </a>
        </div>

        <form class="flex flex-wrap items-end gap-3 mb-3">
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Dari
                    Tanggal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-calendar-day text-xs"></i>
                    </div>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none text-slate-700">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Sampai
                    Tanggal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-calendar-check text-xs"></i>
                    </div>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none text-slate-700">
                </div>
            </div>

            <button type="submit"
                class="h-[40px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm shadow-indigo-100 transition-all flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i>
                Terapkan
            </button>
        </form>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
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
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Sales</th>
                            <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Fee Sales</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 uppercase">
                        @foreach ($sales as $i => $sale)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="px-4 py-4 text-slate-500 whitespace-nowrap">{{ $loop->iteration }}</td>

                                <td class="px-4 py-4 font-bold text-indigo-600 whitespace-nowrap">
                                    <span class="bg-indigo-50 px-2 py-1 rounded text-xs">#{{ $sale->invoice_number }}</span>
                                </td>

                                <td class="px-4 py-4 text-slate-600 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $sale->created_at->format('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $sale->created_at->format('H:i') }}
                                            WIB</span>
                                    </div>
                                </td>

                                <td class="px-4 py-4 font-bold text-slate-800 whitespace-nowrap">
                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-4  whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                                                                                                                                                                                {{ $sale->benefit >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $sale->benefit >= 0 ? '+' : '' }} Rp
                                        {{ number_format($sale->benefit, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-4 py-4  whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 rounded-lg text-[10px] font-bold tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ strtoupper($sale->payment_method) }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($sale->salesPerson)
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-slate-700">
                                                {{ $sale->salesPerson->name }}
                                            </span>
                                            <span class="text-[10px] text-slate-400">
                                                {{ $sale->salesPerson->phone }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs italic text-slate-400">Tanpa Sales</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <span class="text-rose-600 font-bold">
                                        Rp {{ number_format($sale->fee_sales ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>


                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex  gap-2">
                                        <button onclick="openSaleDetail({{ $sale->id }})"
                                            class="inline-flex items-center justify-center w-9 h-9 text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm shadow-blue-100"
                                            title="Lihat Detail">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>

                                        <a href="{{ url("/sales/{$sale->id}/invoice-pdf") }}" target="_blank"
                                            class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-800 hover:text-white transition-all duration-200 shadow-sm shadow-slate-200"
                                            title="Cetak Invoice">
                                            <i class="fa-solid fa-print text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="saleModal"
        class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden transform transition-all">
            <div class="flex items-center justify-between px-8 py-6 bg-slate-50 border-b border-slate-100">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">Detail Transaksi</h2>
                    <p id="modalInvoice" class="text-sm text-indigo-600 font-medium mt-1"></p>
                </div>
                <button onclick="closeSaleDetail()"
                    class="w-10 h-10 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 rounded-2xl bg-slate-50 border">
                        <p class="text-xs font-semibold text-slate-400 uppercase">Sales</p>
                        <p id="modalSales" class="font-bold text-slate-700 mt-1">-</p>
                        <p id="modalSalesPhone" class="text-xs text-slate-400"></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100">
                        <p class="text-xs font-semibold text-rose-600 uppercase">Fee Sales</p>
                        <p id="modalFeeSales" class="text-xl font-extrabold text-rose-600 mt-1">
                            Rp 0
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 overflow-hidden shadow-sm bg-slate-50/30">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-500 border-b border-slate-100">
                                <th class="px-4 py-3 text-left font-semibold">Item Produk</th>
                                <th class="px-4 py-3 text-right font-semibold">Harga Jual</th>
                                <th class="px-4 py-3 text-right font-semibold text-emerald-600">Profit</th>
                            </tr>
                        </thead>
                        <tbody id="modalItems" class="divide-y divide-slate-100 bg-white text-slate-700">
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Penjualan</p>
                        <p id="modalTotal" class="text-xl font-extrabold text-slate-800 mt-1"></p>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Total Keuntungan</p>
                        <p id="modalProfit" class="text-xl font-extrabold text-emerald-700 mt-1"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openSaleDetail(id) {
            fetch(`/sales/${id}/detail`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('saleModal').classList.remove('hidden')
                    document.getElementById('saleModal').classList.add('flex')

                    document.getElementById('modalInvoice').innerText =
                        data.invoice + ' · ' + data.date

                    document.getElementById('modalTotal').innerText =
                        'Rp ' + data.grand_total

                    document.getElementById('modalProfit').innerText =
                        'Rp ' + data.benefit

                    document.getElementById('modalSales').innerText =
                        data.sales_name ?? 'Tanpa Sales'

                    document.getElementById('modalSalesPhone').innerText =
                        data.sales_phone ?? ''

                    document.getElementById('modalFeeSales').innerText =
                        'Rp ' + data.fee_sales


                    let rows = ''

                    data.items.forEach(item => {
                        rows += `
                                                                                                                                    <tr class="border-t">
                                                                                                                                        <td class="px-3 py-2">${item.name}</td>
                                                                                                                                        <td class="px-3 py-2 text-right">Rp ${item.price}</td>
                                                                                                                                        <td class="px-3 py-2 text-right">${item.benefit}</td>
                                                                                                                                    </tr>
                                                                                                                                `
                    })

                    data.bonuses.forEach(item => {
                        rows += `
                                                                                                                                    <tr class="border-t text-green-600">
                                                                                                                                        <td class="px-3 py-2">🎁 ${item.name}</td>
                                                                                                                                        <td class="px-3 py-2 text-right">Rp 0</td>
                                                                                                                                        <td class="px-3 py-2 text-right">${item.benefit}</td>
                                                                                                                                    </tr>
                                                                                                                                `
                    })

                    document.getElementById('modalItems').innerHTML = rows
                })
        }

        function closeSaleDetail() {
            document.getElementById('saleModal').classList.add('hidden')
        }

        $(document).ready(function () {
            $('#datatable').DataTable()
        })
    </script>
@endpush
