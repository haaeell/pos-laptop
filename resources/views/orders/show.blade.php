@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    @php
        $statusMeta = [
            'pending_payment' => ['label' => 'Menunggu Pembayaran', 'icon' => 'fa-clock', 'class' => 'bg-slate-100 text-slate-700 border-slate-200', 'bar' => 'bg-slate-400'],
            'paid' => ['label' => 'Sudah Dibayar', 'icon' => 'fa-circle-check', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bar' => 'bg-emerald-500'],
            'processing' => ['label' => 'Diproses', 'icon' => 'fa-box-open', 'class' => 'bg-amber-50 text-amber-700 border-amber-200', 'bar' => 'bg-amber-500'],
            'shipped' => ['label' => 'Dikirim', 'icon' => 'fa-truck-fast', 'class' => 'bg-blue-50 text-blue-700 border-blue-200', 'bar' => 'bg-blue-500'],
            'completed' => ['label' => 'Selesai', 'icon' => 'fa-award', 'class' => 'bg-teal-50 text-teal-700 border-teal-200', 'bar' => 'bg-teal-500'],
            'cancelled' => ['label' => 'Dibatalkan', 'icon' => 'fa-ban', 'class' => 'bg-rose-50 text-rose-700 border-rose-200', 'bar' => 'bg-rose-500'],
            'expired' => ['label' => 'Kedaluwarsa', 'icon' => 'fa-hourglass-end', 'class' => 'bg-rose-50 text-rose-700 border-rose-200', 'bar' => 'bg-rose-500'],
            'failed' => ['label' => 'Gagal', 'icon' => 'fa-triangle-exclamation', 'class' => 'bg-rose-50 text-rose-700 border-rose-200', 'bar' => 'bg-rose-500'],
        ];

        $meta = $statusMeta[$order->status] ?? $statusMeta['pending_payment'];
        $nextAction = [
            'paid' => ['label' => 'Tandai Diproses', 'icon' => 'fa-box-open'],
            'shipped' => ['label' => 'Tandai Selesai', 'icon' => 'fa-circle-check'],
        ][$order->status] ?? null;
        $canCreateShipment = in_array($order->status, ['paid', 'processing']) && !$order->hasShipment();
        $canCancel = !in_array($order->status, ['completed', 'cancelled', 'expired', 'failed']);
        $paymentLabel = $order->midtrans_payment_type ? strtoupper(str_replace('_', ' ', $order->midtrans_payment_type)) : 'Belum terdeteksi';
        $statusSteps = ['pending_payment', 'paid', 'processing', 'shipped', 'completed'];
        $currentStep = array_search($order->status, $statusSteps, true);
    @endphp

    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-6 text-white shadow-sm">
            <div class="absolute -right-14 -top-14 h-44 w-44 rounded-full bg-blue-500/20 blur-2xl"></div>
            <div class="absolute bottom-0 left-1/3 h-24 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <a href="{{ route('orders.index') }}" class="mb-4 inline-flex items-center gap-2 text-xs font-semibold text-slate-300 hover:text-white">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar pesanan
                    </a>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-bold tracking-tight">#{{ $order->order_number }}</h1>
                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-bold uppercase {{ $meta['class'] }}">
                            <i class="fa-solid {{ $meta['icon'] }}"></i> {{ $order->status_label }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-slate-300">
                        Dibuat {{ $order->created_at->translatedFormat('d M Y H:i') }}
                        @if ($order->paid_at)
                            &middot; Dibayar {{ $order->paid_at->translatedFormat('d M Y H:i') }}
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:min-w-[520px]">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wider text-slate-300">Total</p>
                        <p class="mt-1 text-lg font-black">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wider text-slate-300">Item</p>
                        <p class="mt-1 text-lg font-black">{{ $order->items->sum('qty') }} pcs</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wider text-slate-300">Ongkir</p>
                        <p class="mt-1 text-lg font-black">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wider text-slate-300">Pembayaran</p>
                        <p class="mt-1 truncate text-sm font-black">{{ $paymentLabel }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success') || session('error'))
            <div class="rounded-2xl border p-4 text-sm font-semibold {{ session('success') ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700' }}">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Progress Pesanan</h2>
                            <p class="text-xs text-slate-500">Ringkasan alur pesanan dari pembayaran sampai selesai.</p>
                        </div>
                        <span class="h-2 w-24 rounded-full {{ $meta['bar'] }}"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
                        @foreach ($statusSteps as $idx => $step)
                            @php
                                $isDone = $currentStep !== false && $idx <= $currentStep;
                                $stepMeta = $statusMeta[$step];
                            @endphp
                            <div class="rounded-2xl border p-3 {{ $isDone ? 'border-indigo-200 bg-indigo-50' : 'border-slate-200 bg-slate-50' }}">
                                <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-xl {{ $isDone ? 'bg-indigo-600 text-white' : 'bg-white text-slate-400' }}">
                                    <i class="fa-solid {{ $stepMeta['icon'] }}"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700">{{ $stepMeta['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h2 class="text-base font-bold text-slate-800">Produk Dipesan</h2>
                        <p class="text-xs text-slate-500">Detail item, harga satuan, dan subtotal.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">Produk</th>
                                    <th class="px-5 py-3 text-center">Qty</th>
                                    <th class="px-5 py-3 text-right">Harga</th>
                                    <th class="px-5 py-3 text-right">HPP</th>
                                    <th class="px-5 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($order->items as $item)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-slate-800">{{ $item->product_name }}</p>
                                            <p class="mt-1 text-xs text-slate-400">Produk ID: {{ $item->product_id ?? '-' }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold">{{ $item->qty }}</td>
                                        <td class="px-5 py-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-5 py-4 text-right text-slate-500">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                                        <td class="px-5 py-4 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-slate-200 bg-slate-50 text-sm">
                                <tr>
                                    <td class="px-5 py-3 text-right text-slate-500" colspan="4">Subtotal Produk</td>
                                    <td class="px-5 py-3 text-right font-semibold">Rp {{ number_format($order->items_subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-5 py-3 text-right text-slate-500" colspan="4">Ongkos Kirim</td>
                                    <td class="px-5 py-3 text-right font-semibold">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-5 py-4 text-right font-bold text-slate-800" colspan="4">Grand Total</td>
                                    <td class="px-5 py-4 text-right text-lg font-black text-indigo-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Tracking Pengiriman</h2>
                            <p class="text-xs text-slate-500">Status resi, tracking ID, dan riwayat perjalanan paket.</p>
                        </div>
                        @if ($order->hasShipment())
                            <form action="{{ route('orders.shipment.refresh', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-50 px-4 py-2 text-xs font-bold text-indigo-700 transition hover:bg-indigo-600 hover:text-white">
                                    <i class="fa-solid fa-rotate"></i> Refresh Tracking
                                </button>
                            </form>
                        @endif
                    </div>

                    @if (!$order->hasShipment())
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="font-bold text-slate-700">Pengiriman belum dibuat</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Kurir: {{ $order->courier_service_name ?? '-' }} &middot;
                                        Ongkir Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                                @if ($canCreateShipment)
                                    <form action="{{ route('orders.shipment.create', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-200">
                                            <i class="fa-solid fa-box-open"></i> Buat Pengiriman
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kurir / Service</p>
                                <p class="mt-1 font-bold text-slate-800">{{ $order->courier_service_name ?? '-' }}</p>
                            </div>
                            <div class="rounded-2xl bg-blue-50 p-4">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-blue-400">No. Resi</p>
                                <p class="mt-1 font-bold text-blue-800">{{ $order->courier_waybill_id ?? '-' }}</p>
                            </div>
                            <div class="rounded-2xl bg-emerald-50 p-4">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-500">Status Biteship</p>
                                <p class="mt-1 font-bold text-emerald-800">{{ $order->shipment_status ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50 to-white p-4">
                            @if ($order->trackingHistories->isNotEmpty())
                                <div class="relative space-y-0">
                                    <div class="absolute left-[17px] top-4 h-[calc(100%-32px)] w-px bg-slate-200"></div>
                                    @foreach ($order->trackingHistories->sortByDesc('created_at') as $history)
                                        <div class="relative flex gap-4 pb-5 last:pb-0">
                                            <div class="relative z-10 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border-4 border-white bg-indigo-600 text-white shadow">
                                                <i class="fa-solid fa-location-dot text-xs"></i>
                                            </div>
                                            <div class="flex-1 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                                    <p class="font-bold text-slate-800">{{ $history->status }}</p>
                                                    <p class="text-xs font-semibold text-slate-400">{{ $history->created_at->translatedFormat('d M Y H:i') }}</p>
                                                </div>
                                                @if ($history->note)
                                                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $history->note }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-8 text-center">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <i class="fa-solid fa-route"></i>
                                    </div>
                                    <p class="font-bold text-slate-700">Belum ada riwayat tracking</p>
                                    <p class="mt-1 text-sm text-slate-500">Klik refresh tracking untuk mengambil riwayat terbaru dari Biteship.</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6 xl:col-span-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-bold text-slate-800">Aksi Admin</h2>
                    <div class="mt-4 flex flex-col gap-3">
                        @if ($nextAction)
                            <form action="{{ route('orders.advance', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                                    <i class="fa-solid {{ $nextAction['icon'] }} mr-2"></i> {{ $nextAction['label'] }}
                                </button>
                            </form>
                        @endif

                        @if ($canCancel)
                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-600 hover:text-white">
                                    <i class="fa-solid fa-xmark mr-2"></i> Batalkan Pesanan
                                </button>
                            </form>
                        @endif

                        @if (!$nextAction && !$canCancel)
                            <div class="rounded-2xl bg-slate-50 p-4 text-sm font-semibold text-slate-500">
                                Tidak ada aksi lanjutan untuk status ini.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-bold text-slate-800">Informasi Customer</h2>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nama</p>
                            <p class="mt-1 font-bold text-slate-800">{{ $order->customer->name ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Email</p>
                            <p class="mt-1 break-all font-semibold text-slate-700">{{ $order->customer->email ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">No. HP Penerima</p>
                            <p class="mt-1 font-semibold text-slate-700">{{ $order->recipient_phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-bold text-slate-800">Alamat Pengiriman</h2>
                    <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-600">
                        <p class="font-bold text-slate-800">{{ $order->recipient_name }}</p>
                        <p class="mt-2">{{ $order->address_detail }}</p>
                        <p class="mt-1">{{ $order->district }}, {{ $order->city }}, {{ $order->province }}</p>
                        @if ($order->notes)
                            <div class="mt-3 rounded-xl bg-white p-3 text-xs text-slate-500">
                                <span class="font-bold text-slate-700">Catatan:</span> {{ $order->notes }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-bold text-slate-800">Detail Teknis</h2>
                    <div class="mt-4 grid grid-cols-1 gap-3 text-sm">
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Order ID</span>
                            <strong>{{ $order->id }}</strong>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Snap Token</span>
                            <strong>{{ $order->snap_token ? 'Ada' : 'Belum ada' }}</strong>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Biteship ID</span>
                            <strong class="max-w-[160px] truncate">{{ $order->biteship_order_id ?? '-' }}</strong>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Tracking ID</span>
                            <strong class="max-w-[160px] truncate">{{ $order->courier_tracking_id ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-bold text-slate-800">Riwayat Status</h2>
                    <div class="relative mt-5">
                        <div class="absolute left-[15px] top-3 h-[calc(100%-24px)] w-px bg-slate-200"></div>
                        <div class="space-y-4">
                            @foreach ($order->statusHistories->sortByDesc('created_at') as $history)
                                <div class="relative flex gap-3">
                                    <div class="relative z-10 mt-1 h-8 w-8 rounded-full border-4 border-white bg-slate-900 shadow"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $history->status_label }}</p>
                                        <p class="text-xs font-semibold text-slate-400">{{ $history->created_at->translatedFormat('d M Y H:i') }}</p>
                                        @if ($history->note)
                                            <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $history->note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
