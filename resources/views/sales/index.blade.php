@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl">

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

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th class="text-right">Grand Total</th>
                        <th class="text-right">Profit</th>
                        <th>Pembayaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($sales as $i => $sale)
                                <tr>
                                    <td>{{ $i + 1 }}</td>

                                    <td class="font-medium text-indigo-600">
                                        {{ $sale->invoice_number }}
                                    </td>

                                    <td class="text-slate-500">
                                        {{ $sale->created_at->format('d M Y H:i') }}
                                    </td>

                                    <td>{{ $sale->user->name }}</td>

                                    <td class="text-right font-semibold">
                                        Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                    </td>

                                    <td class="text-right">
                                        <span class="px-2 py-1 rounded text-xs
                                                                                                                                                                                                                                            {{ $sale->benefit >= 0
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700' }}">
                                            Rp {{ number_format($sale->benefit, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="px-2 py-1 rounded text-xs bg-slate-100 text-slate-700">
                                            {{ strtoupper($sale->payment_method) }}
                                        </span>
                                    </td>

                                    <td class="text-center space-x-1">
                                        <button onclick="openSaleDetail({{ $sale->id }})"
                                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </button>


                                        <a href="{{ url("/sales/{$sale->id}/invoice-pdf") }}" target="_blank"
                                            class="px-3 py-1 bg-slate-700 text-white rounded hover:bg-slate-800">
                                            <i class="fa-solid fa-print"></i> Cetak Invoice
                                        </a>
                                    </td>
                                </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="saleModal"
                class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

                <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl">

                    <!-- HEADER -->
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Detail Penjualan</h2>
                            <p id="modalInvoice" class="text-xs text-slate-500"></p>
                        </div>
                        <button onclick="closeSaleDetail()"
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- BODY -->
                    <div class="p-6 space-y-4">

                        <div class="border rounded-xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-100 text-slate-600 text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Produk</th>
                                        <th class="px-4 py-3 text-right">Harga</th>
                                        <th class="px-4 py-3 text-right">Profit</th>
                                    </tr>
                                </thead>
                                <tbody id="modalItems" class="divide-y"></tbody>
                            </table>
                        </div>

                        <div class="flex justify-end gap-8 text-sm">
                            <div>
                                <p class="text-slate-500">Grand Total</p>
                                <p id="modalTotal" class="font-bold text-right text-slate-800"></p>
                            </div>
                            <div>
                                <p class="text-slate-500">Profit</p>
                                <p id="modalProfit" class="font-bold text-right text-emerald-600"></p>
                            </div>
                        </div>
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
            $('#datatable').DataTable({
                order: [[2, 'desc']]
            })
        })
    </script>
@endpush