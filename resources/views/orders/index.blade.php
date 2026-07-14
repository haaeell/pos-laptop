@extends('layouts.app')

@section('title', 'Pesanan Online')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Pesanan Online</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Pesanan Online</li>
                    </ol>
                </nav>
            </div>
        </div>

        <form class="flex flex-wrap items-end gap-3 mb-3">
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Status</label>
                <select name="status"
                    class="h-[40px] px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-700"
                    onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach (\App\Models\Order::STATUS_LABELS as $key => $label)
                        <option value="{{ $key }}" {{ $statusFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="datatable" class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">No. Pesanan</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Tanggal</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Pelanggan</th>
                            <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Total</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Status</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="px-4 py-4 font-bold text-indigo-600 whitespace-nowrap">
                                    <span class="bg-indigo-50 px-2 py-1 rounded text-xs">#{{ $order->order_number }}</span>
                                </td>
                                <td class="px-4 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $order->customer->name ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-right font-bold whitespace-nowrap">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                        $color = match ($order->status) {
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'shipped' => 'bg-blue-100 text-blue-700',
                                            'paid', 'processing' => 'bg-amber-100 text-amber-700',
                                            'cancelled', 'expired', 'failed' => 'bg-rose-100 text-rose-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase {{ $color }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <a href="{{ route('orders.show', $order->id) }}"
                                        class="inline-flex items-center justify-center w-9 h-9 text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm shadow-blue-100"
                                        title="Lihat Detail">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>
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
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data"
                }
            });
        });
    </script>
@endpush
