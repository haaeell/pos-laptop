@extends('layouts.app')

@section('title', 'Customer Online')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Customer Online</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Customer Online</li>
                    </ol>
                </nav>
                <p class="mt-3 max-w-3xl text-sm text-slate-500">
                    Pantau seluruh pelanggan yang registrasi online beserta aktivitas pesanan, nilai belanja, alamat tersimpan, favorit, dan keterlibatan mereka di toko.
                </p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Customer</p>
                <p class="mt-2 text-3xl font-black text-slate-800">{{ number_format($stats['totalCustomers']) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Customer Aktif</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ number_format($stats['activeCustomers']) }}</p>
            </div>
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Sudah Belanja</p>
                <p class="mt-2 text-3xl font-black text-blue-700">{{ number_format($stats['customersWithOrders']) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Total Pesanan</p>
                <p class="mt-2 text-3xl font-black text-amber-700">{{ number_format($stats['totalCustomerOrders']) }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-600">Omzet Customer</p>
                <p class="mt-2 text-2xl font-black text-indigo-700">Rp {{ number_format($stats['totalCustomerRevenue'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div class="xl:col-span-2">
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Cari Customer</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}"
                        placeholder="Nama, email, atau nomor HP"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="active" @selected($filters['status'] === 'active')>Aktif</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Urutkan</label>
                    <select name="sort" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none">
                        <option value="">Terbaru Daftar</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>Terlama Daftar</option>
                        <option value="most_orders" @selected($filters['sort'] === 'most_orders')>Pesanan Terbanyak</option>
                        <option value="highest_spent" @selected($filters['sort'] === 'highest_spent')>Belanja Tertinggi</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex min-h-[48px] flex-1 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-sm">
                        <i class="fa-solid fa-filter"></i> Terapkan
                    </button>
                    <a href="{{ route('customers.index') }}" class="inline-flex min-h-[48px] items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            @forelse ($customers as $customer)
                <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-black text-indigo-700">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h2 class="truncate text-lg font-bold text-slate-800">{{ $customer->name }}</h2>
                                    <p class="truncate text-sm text-slate-500">{{ $customer->email }}</p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        Bergabung {{ optional($customer->created_at)->translatedFormat('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $customer->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                <i class="fa-solid {{ $customer->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <a href="{{ route('customers.show', $customer->id) }}"
                                class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-3 py-1 text-xs font-bold text-white">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Detail
                            </a>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pesanan</p>
                            <p class="mt-2 text-2xl font-black text-slate-800">{{ number_format($customer->orders_count) }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Alamat</p>
                            <p class="mt-2 text-2xl font-black text-slate-800">{{ number_format($customer->addresses_count) }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Favorit</p>
                            <p class="mt-2 text-2xl font-black text-slate-800">{{ number_format($customer->favorite_products_count) }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Ulasan</p>
                            <p class="mt-2 text-2xl font-black text-slate-800">{{ number_format($customer->reviews_count) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-[1.2fr_.8fr]">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kontak</p>
                            <div class="mt-2 space-y-2 text-sm text-slate-600">
                                <p><i class="fa-solid fa-envelope mr-2 text-slate-400"></i>{{ $customer->email }}</p>
                                <p><i class="fa-solid fa-phone mr-2 text-slate-400"></i>{{ $customer->phone ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-indigo-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-500">Nilai Belanja</p>
                            <p class="mt-2 text-xl font-black text-indigo-700">Rp {{ number_format($customer->orders_sum_grand_total ?? 0, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-indigo-500">Akumulasi seluruh pesanan customer.</p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="xl:col-span-2 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-sm text-slate-500">
                    Belum ada customer online yang sesuai dengan filter ini.
                </div>
            @endforelse
        </div>

        @if ($customers->hasPages())
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection
