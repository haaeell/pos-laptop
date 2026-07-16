@extends('layouts.app')

@section('title', 'Customer Online')

@push('styles')
    <style>
        #datatable_wrapper .dataTables_length,
        #datatable_wrapper .dataTables_filter {
            margin-bottom: 1rem;
            color: #334155;
            font-size: 0.95rem;
        }

        #datatable_wrapper .dataTables_length select,
        #datatable_wrapper .dataTables_filter input {
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            padding: 0.55rem 0.85rem;
            background: #fff;
            color: #0f172a;
        }

        #datatable_wrapper .dataTables_filter input {
            min-width: 240px;
        }

        #datatable_wrapper .dataTables_info,
        #datatable_wrapper .dataTables_paginate {
            margin-top: 1rem;
            color: #475569;
        }

        #datatable_wrapper .paginate_button {
            border-radius: 0.75rem !important;
        }

        #datatable_wrapper .paginate_button.current {
            background: #eef2ff !important;
            border: 1px solid #c7d2fe !important;
            color: #4338ca !important;
        }

        #datatable thead th {
            white-space: nowrap;
            vertical-align: middle;
        }

        #datatable tbody td {
            vertical-align: middle;
        }

        .customer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #eef2ff;
            color: #4338ca;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        .customer-name-cell {
            min-width: 220px;
        }

        .customer-contact-cell {
            min-width: 240px;
        }

        .customer-metric {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.38rem 0.65rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569;
            white-space: nowrap;
        }

        .customer-detail-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-width: 108px;
            padding: 0.72rem 1rem;
            border-radius: 0.9rem;
            background: linear-gradient(135deg, #4f46e5, #2563eb);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            box-shadow: 0 12px 24px rgba(37, 99, 235, .18);
        }

        .customer-detail-btn:hover {
            filter: brightness(1.03);
        }
    </style>
@endpush

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

        <div class="rounded-3xl border border-slate-200 bg-white px-4 py-5 shadow-sm">
            <div class="overflow-x-auto">
                <table id="datatable" class="w-full text-sm">
                    <thead class="bg-slate-100 text-slate-700">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Kontak</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aktivitas</th>
                            <th class="px-4 py-3">Total Belanja</th>
                            <th class="px-4 py-3">Tanggal Daftar</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $i => $customer)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-500">{{ $i + 1 }}</td>
                                <td class="customer-name-cell px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="customer-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                        <div class="min-w-0">
                                            <p class="truncate font-bold text-slate-800">{{ $customer->name }}</p>
                                            <p class="truncate text-xs text-slate-400">ID #{{ $customer->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="customer-contact-cell px-4 py-3">
                                    <div class="space-y-1">
                                        <p class="text-sm text-slate-700">{{ $customer->email }}</p>
                                        <p class="text-xs text-slate-400">{{ $customer->phone ?: 'Nomor HP belum diisi' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $customer->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        <i class="fa-solid {{ $customer->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                        {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="customer-metric"><i class="fa-solid fa-bag-shopping text-indigo-500"></i>{{ number_format($customer->orders_count) }} pesanan</span>
                                        <span class="customer-metric"><i class="fa-solid fa-location-dot text-emerald-500"></i>{{ number_format($customer->addresses_count) }} alamat</span>
                                        <span class="customer-metric"><i class="fa-solid fa-heart text-rose-500"></i>{{ number_format($customer->favorite_products_count) }} favorit</span>
                                        <span class="customer-metric"><i class="fa-solid fa-star text-amber-500"></i>{{ number_format($customer->reviews_count) }} ulasan</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-black text-indigo-700">
                                    Rp {{ number_format($customer->orders_sum_grand_total ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ optional($customer->created_at)->translatedFormat('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('customers.show', $customer->id) }}" class="customer-detail-btn">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-sm text-slate-500">
                                    Belum ada customer online.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#datatable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[6, 'desc']],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ customer',
                    infoEmpty: 'Belum ada customer',
                    zeroRecords: 'Customer tidak ditemukan',
                    paginate: {
                        previous: 'Sebelumnya',
                        next: 'Berikutnya',
                    },
                },
            });
        });
    </script>
@endpush
