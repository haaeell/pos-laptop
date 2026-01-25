@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="p-6 bg-white rounded-xl space-y-6">

        <h1 class="text-2xl font-semibold text-slate-800">Laporan</h1>

        {{-- FILTER --}}
        <form class="flex flex-wrap gap-2">
            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="border rounded px-3 py-1 text-sm">

            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="border rounded px-3 py-1 text-sm">

            <button class="px-4 py-1 bg-indigo-600 text-white rounded">
                Terapkan
            </button>

            <a href="/reports/pdf?from={{$from}}&to={{$to}}" class="px-3 py-1 bg-red-600 text-white rounded">
                PDF
            </a>

            <a href="/reports/excel?from={{$from}}&to={{$to}}" class="px-3 py-1 bg-green-600 text-white rounded">
                Excel
            </a>
        </form>

        {{-- SUMMARY --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-500">Total Penjualan</p>
                <p class="text-xl font-bold">
                    Rp {{ number_format($totalSales) }}
                </p>
            </div>

            <div class="p-4 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-500">Profit</p>
                <p class="text-xl font-bold text-green-600">
                    Rp {{ number_format($totalProfit) }}
                </p>
            </div>

            <div class="p-4 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-500">Bonus / Loss</p>
                <p class="text-xl font-bold text-red-600">
                    Rp {{ number_format($bonusLoss) }}
                </p>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-3 py-2">Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Pembayaran</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $s)
                        <tr class="border-t">
                            <td class="px-3 py-2 text-indigo-600">
                                {{ $s->invoice_number }}
                            </td>
                            <td>{{ $s->created_at->format('d M Y') }}</td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ strtoupper($s->payment_method) }}</td>
                            <td class="text-right">
                                Rp {{ number_format($s->grand_total) }}
                            </td>
                            <td class="text-right">
                                Rp {{ number_format($s->benefit) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-slate-400">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOT NOTE --}}
        <p class="text-xs text-slate-400">
            Profit bersih = Profit – Bonus/Loss
        </p>

    </div>
@endsection