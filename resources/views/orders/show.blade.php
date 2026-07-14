@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    @php
        $color = match ($order->status) {
            'completed' => 'bg-emerald-100 text-emerald-700',
            'shipped' => 'bg-blue-100 text-blue-700',
            'paid', 'processing' => 'bg-amber-100 text-amber-700',
            'cancelled', 'expired', 'failed' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-600',
        };
        $nextAction = [
            'paid' => ['label' => 'Tandai Diproses', 'icon' => 'fa-box'],
            'shipped' => ['label' => 'Tandai Selesai', 'icon' => 'fa-circle-check'],
        ][$order->status] ?? null;
        $canCreateShipment = in_array($order->status, ['paid', 'processing']) && !$order->hasShipment();
    @endphp

    <div class="mx-auto bg-white rounded-xl">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-box text-indigo-600"></i>
                    #{{ $order->order_number }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $order->created_at->translatedFormat('d M Y H:i') }} &middot; {{ $order->customer->name }}
                    ({{ $order->customer->email }})
                </p>
            </div>
            <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase {{ $color }}">{{ $order->status_label }}</span>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 lg:col-span-8 space-y-6">

                <div class="border rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="p-3 text-left">Produk</th>
                                <th class="p-3 text-center">Qty</th>
                                <th class="p-3 text-right">Harga</th>
                                <th class="p-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr class="border-t">
                                    <td class="p-3">{{ $item->product_name }}</td>
                                    <td class="p-3 text-center">{{ $item->qty }}</td>
                                    <td class="p-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-semibold">Rp
                                        {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            @if ($order->shipping_cost !== null)
                                <tr class="border-t">
                                    <td class="p-3 text-right text-slate-500" colspan="3">Subtotal Produk</td>
                                    <td class="p-3 text-right">Rp {{ number_format($order->items_subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="p-3 text-right text-slate-500" colspan="3">Ongkos Kirim
                                        @if ($order->courier_service_name)
                                            <span class="text-xs text-slate-400">({{ $order->courier_service_name }})</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="border-t bg-slate-50">
                                <td class="p-3 font-bold" colspan="3">Grand Total</td>
                                <td class="p-3 text-right font-bold text-indigo-600">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="rounded-xl border p-4 bg-slate-50">
                    <h3 class="text-sm font-semibold text-slate-700 mb-2">
                        <i class="fa-solid fa-location-dot text-indigo-500"></i> Alamat Pengiriman
                    </h3>
                    <p class="text-sm"><strong>{{ $order->recipient_name }}</strong> ({{ $order->recipient_phone }})</p>
                    <p class="text-sm text-slate-600">{{ $order->address_detail }}</p>
                    <p class="text-xs text-slate-400">{{ $order->district }}, {{ $order->city }}, {{ $order->province }}</p>
                    @if ($order->notes)
                        <p class="text-xs text-slate-400 mt-1">Catatan: {{ $order->notes }}</p>
                    @endif
                </div>

                <div class="rounded-xl border p-4">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3">
                        <i class="fa-solid fa-truck-fast text-indigo-500"></i> Pengiriman
                    </h3>

                    @if (!$order->hasShipment())
                        @if ($canCreateShipment)
                            <p class="text-xs text-slate-500 mb-3">
                                Kurir: {{ $order->courier_service_name ?? '-' }} &middot;
                                Ongkir: Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}
                            </p>
                            <form action="{{ route('orders.shipment.create', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2.5 rounded-xl text-sm text-white font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 shadow-md shadow-indigo-200 flex items-center gap-2">
                                    <i class="fa-solid fa-box-open"></i> Buat Pengiriman (Generate Resi)
                                </button>
                            </form>
                        @else
                            <p class="text-xs text-slate-400">Belum ada pengiriman dibuat untuk pesanan ini.</p>
                        @endif
                    @else
                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <p class="text-[11px] text-slate-400 uppercase">Kurir</p>
                                <p class="font-semibold">{{ $order->courier_service_name }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400 uppercase">No. Resi</p>
                                <p class="font-semibold">{{ $order->courier_waybill_id ?? '-' }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-[11px] text-slate-400 uppercase">Status Pengiriman (Biteship)</p>
                                <p class="font-semibold">{{ $order->shipment_status ?? '-' }}</p>
                            </div>
                        </div>

                        <form action="{{ route('orders.shipment.refresh', $order->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit"
                                class="px-3 py-2 rounded-lg text-xs text-indigo-600 font-semibold bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all">
                                <i class="fa-solid fa-rotate"></i> Refresh Tracking
                            </button>
                        </form>

                        @if ($order->trackingHistories->isNotEmpty())
                            <div class="space-y-2 border-t pt-3">
                                @foreach ($order->trackingHistories as $history)
                                    <div class="text-xs">
                                        <span class="font-semibold">{{ $history->status }}</span>
                                        <span class="text-slate-400">
                                            &middot; {{ $history->created_at->translatedFormat('d M Y H:i') }}
                                        </span>
                                        @if ($history->note)
                                            <p class="text-slate-500">{{ $history->note }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                <div class="flex gap-3">
                    @if ($nextAction)
                        <form action="{{ route('orders.advance', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2.5 rounded-xl text-sm text-white font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 shadow-md shadow-indigo-200 flex items-center gap-2">
                                <i class="fa-solid {{ $nextAction['icon'] }}"></i> {{ $nextAction['label'] }}
                            </button>
                        </form>
                    @endif

                    @if (!in_array($order->status, ['completed', 'cancelled', 'expired']))
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                            onsubmit="return confirm('Batalkan pesanan ini?')">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2.5 rounded-xl text-sm text-rose-600 font-semibold bg-rose-50 hover:bg-rose-600 hover:text-white transition-all">
                                <i class="fa-solid fa-xmark"></i> Batalkan Pesanan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="col-span-12 lg:col-span-4">
                <div class="rounded-2xl border p-5">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">
                        <i class="fa-solid fa-timeline text-indigo-500"></i> Riwayat Status
                    </h3>
                    <div class="space-y-4">
                        @foreach ($order->statusHistories as $history)
                            <div class="flex gap-3">
                                <div class="w-2 h-2 mt-1.5 rounded-full bg-indigo-500 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ $history->status_label }}</p>
                                    <p class="text-xs text-slate-400">
                                        {{ $history->created_at->translatedFormat('d M Y H:i') }}
                                    </p>
                                    @if ($history->note)
                                        <p class="text-xs text-slate-500 mt-1">{{ $history->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
