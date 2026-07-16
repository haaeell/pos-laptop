@extends('layouts.app')

@section('title', 'Detail Customer Online')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-6 text-white shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <a href="{{ route('customers.index') }}" class="mb-4 inline-flex items-center gap-2 text-xs font-semibold text-slate-300 hover:text-white">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar customer
                    </a>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-2xl font-black">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight">{{ $customer->name }}</h1>
                            <p class="mt-1 text-sm text-slate-300">{{ $customer->email }}</p>
                            <p class="mt-2 text-xs text-slate-400">
                                Terdaftar {{ optional($customer->created_at)->translatedFormat('d M Y H:i') }}
                                @if($customer->updated_at)
                                    &middot; Update terakhir {{ $customer->updated_at->translatedFormat('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 lg:min-w-[430px]">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wider text-slate-300">Status</p>
                        <p class="mt-1 text-lg font-black">{{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wider text-slate-300">Nomor HP</p>
                        <p class="mt-1 text-sm font-black">{{ $customer->phone ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wider text-slate-300">Total Pesanan</p>
                        <p class="mt-1 text-lg font-black">{{ number_format($customer->orders_count) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wider text-slate-300">Total Belanja</p>
                        <p class="mt-1 text-lg font-black">Rp {{ number_format($orderStats['totalSpent'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Average Order</p>
                <p class="mt-2 text-2xl font-black text-slate-800">Rp {{ number_format($orderStats['averageOrderValue'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Pending Payment</p>
                <p class="mt-2 text-2xl font-black text-amber-700">{{ number_format($orderStats['pendingOrders']) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Pesanan Selesai</p>
                <p class="mt-2 text-2xl font-black text-emerald-700">{{ number_format($orderStats['completedOrders']) }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-600">Rata-rata Rating</p>
                <p class="mt-2 text-2xl font-black text-indigo-700">{{ number_format($orderStats['reviewAverage'], 1) }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h2 class="text-base font-bold text-slate-800">Riwayat Pesanan Customer</h2>
                        <p class="text-xs text-slate-500">Pantau semua pesanan online customer beserta status, metode pengiriman, dan nilai transaksinya.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">Order</th>
                                    <th class="px-5 py-3">Tanggal</th>
                                    <th class="px-5 py-3">Metode</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-center">Item</th>
                                    <th class="px-5 py-3 text-right">Total</th>
                                    <th class="px-5 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($customer->orders as $order)
                                    <tr class="hover:bg-slate-50/80">
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-slate-800">#{{ $order->order_number }}</p>
                                            <p class="mt-1 text-xs text-slate-400">{{ $order->recipient_name }} · {{ $order->recipient_phone }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-slate-600">{{ $order->created_at->translatedFormat('d M Y H:i') }}</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $order->delivery_method === 'pickup' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                                                {{ $order->delivery_label }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold
                                                {{ match($order->status) {
                                                    'completed' => 'bg-emerald-50 text-emerald-700',
                                                    'paid', 'processing' => 'bg-amber-50 text-amber-700',
                                                    'shipped' => 'bg-blue-50 text-blue-700',
                                                    'cancelled', 'expired', 'failed' => 'bg-rose-50 text-rose-700',
                                                    default => 'bg-slate-100 text-slate-700',
                                                } }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold text-slate-700">{{ $order->items_count }}</td>
                                        <td class="px-5 py-4 text-right font-black text-slate-800">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                        <td class="px-5 py-4 text-center">
                                            <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700">
                                                <i class="fa-solid fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-500">Customer ini belum memiliki pesanan online.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-bold text-slate-800">Alamat Tersimpan</h2>
                                <p class="text-xs text-slate-500">Alamat yang pernah dipakai customer untuk pengiriman.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $customer->addresses_count }} alamat</span>
                        </div>
                        <div class="mt-4 space-y-3">
                            @forelse($customer->addresses as $address)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $address->label }}</p>
                                            <p class="mt-1 text-sm text-slate-600">{{ $address->recipient_name }} · {{ $address->recipient_phone }}</p>
                                        </div>
                                        @if($address->is_default)
                                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-bold text-indigo-700">Utama</span>
                                        @endif
                                    </div>
                                    <p class="mt-3 text-sm leading-6 text-slate-500">
                                        {{ $address->address_detail }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }}
                                        @if($address->postal_code)
                                            - {{ $address->postal_code }}
                                        @endif
                                    </p>
                                    @if($address->latitude && $address->longitude)
                                        <p class="mt-2 text-xs text-slate-400">
                                            Lat: {{ $address->latitude }}, Lng: {{ $address->longitude }}
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                                    Customer belum menyimpan alamat.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-bold text-slate-800">Ulasan Produk</h2>
                                <p class="text-xs text-slate-500">Masukan terbaru dari customer terhadap produk.</p>
                            </div>
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">{{ $customer->reviews_count }} ulasan</span>
                        </div>
                        <div class="mt-4 space-y-3">
                            @forelse($customer->reviews as $review)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $review->product?->name ?? 'Produk tidak ditemukan' }}</p>
                                            <p class="mt-1 text-xs text-slate-400">{{ $review->created_at->translatedFormat('d M Y H:i') }}</p>
                                        </div>
                                        <div class="text-amber-500 text-sm">
                                            @for($s = 1; $s <= 5; $s++)
                                                <i class="fa-{{ $s <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mt-3 text-sm leading-6 text-slate-500">{{ $review->comment ?: 'Tidak ada komentar tambahan.' }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                                    Customer ini belum pernah memberikan ulasan.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-bold text-slate-800">Ringkasan Customer</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pickup vs Shipping</p>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-slate-500">Pickup</span>
                                <strong class="text-slate-800">{{ number_format($orderStats['pickupOrders']) }}</strong>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-sm">
                                <span class="text-slate-500">Shipping</span>
                                <strong class="text-slate-800">{{ number_format($orderStats['shippingOrders']) }}</strong>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pesanan Bermasalah</p>
                            <p class="mt-2 text-2xl font-black text-rose-600">{{ number_format($orderStats['cancelledOrders']) }}</p>
                            <p class="mt-1 text-xs text-slate-400">Gabungan cancelled, expired, dan failed.</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Wishlist / Favorit</p>
                            <p class="mt-2 text-2xl font-black text-slate-800">{{ number_format($customer->favorite_products_count) }}</p>
                            <p class="mt-1 text-xs text-slate-400">Produk yang disimpan customer.</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Keranjang Aktif</p>
                            <p class="mt-2 text-2xl font-black text-slate-800">{{ number_format($customer->cartItems->count()) }}</p>
                            <p class="mt-1 text-xs text-slate-400">Item yang masih ada di keranjang saat ini.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Favorit Customer</h2>
                            <p class="text-xs text-slate-500">Produk yang paling menarik bagi customer ini.</p>
                        </div>
                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">{{ $customer->favorite_products_count }} item</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse($customer->favoriteProducts as $favorite)
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 p-3">
                                <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 text-slate-300">
                                    @if($favorite->image)
                                        <img src="{{ asset('storage/' . $favorite->image) }}" alt="{{ $favorite->name }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fa-solid fa-image"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-bold text-slate-800">{{ $favorite->name }}</p>
                                    <p class="mt-1 text-sm font-semibold text-indigo-600">Rp {{ number_format($favorite->selling_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                                Customer belum menyimpan produk favorit.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
