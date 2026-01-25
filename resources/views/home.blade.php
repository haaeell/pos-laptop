@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
    <div class="min-h-screen">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
            <p class="text-slate-500 text-sm">Pantau performa penjualan dan inventaris Anda secara real-time.</p>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @php
                // Fungsi untuk menyingkat angka jika terlalu besar
                function formatRingkas($n)
                {
                    if ($n >= 1000000000) {
                        return round($n / 1000000000, 1) . 'B';
                    }
                    if ($n >= 1000000) {
                        return round($n / 1000000, 1) . 'M';
                    }
                    if ($n >= 1000) {
                        return round($n / 1000, 1) . 'K';
                    }
                    return $n;
                }

                $cards = [
                    [
                        'label' => 'Penjualan Hari Ini',
                        'value' => $totalSales,
                        'icon' => 'fa-coins',
                        'bg' => 'bg-indigo-50',
                        'border' => 'border-indigo-100',
                        'text' => 'text-indigo-600',
                    ],
                    [
                        'label' => 'Total Profit',
                        'value' => $totalProfit,
                        'icon' => 'fa-arrow-up-right-dots',
                        'bg' => 'bg-emerald-50',
                        'border' => 'border-emerald-100',
                        'text' => 'text-emerald-600',
                    ],
                    [
                        'label' => 'Total Transaksi',
                        'value' => $totalTransactions,
                        'icon' => 'fa-receipt',
                        'bg' => 'bg-blue-50',
                        'border' => 'border-blue-100',
                        'text' => 'text-blue-600',
                    ],
                    [
                        'label' => 'Bonus (Loss)',
                        'value' => $totalBonus,
                        'icon' => 'fa-gift',
                        'bg' => 'bg-rose-50',
                        'border' => 'border-rose-100',
                        'text' => 'text-rose-600',
                    ],
                ];
            @endphp

            @foreach ($cards as $c)
                <div
                    class="{{ $c['bg'] }} {{ $c['border'] }} p-4 lg:p-5 rounded-2xl shadow-sm border-2 transition-all hover:scale-[1.02]">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p
                                class="text-[10px] font-bold uppercase tracking-wider opacity-70 {{ $c['text'] }} whitespace-nowrap">
                                {{ $c['label'] }}
                            </p>

                            <h2 class="font-black text-slate-800 whitespace-nowrap leading-tight">
                                {{-- Tampilan Mobile: Singkatan (misal 1.5M) | Tampilan Desktop: Angka Lengkap --}}
                                <span class="inline sm:hidden text-lg">
                                    @if ($c['label'] != 'Total Transaksi')
                                        Rp
                                    @endif{{ formatRingkas($c['value']) }}
                                </span>
                                <span class="hidden sm:inline text-base lg:text-md">
                                    @if ($c['label'] != 'Total Transaksi')
                                        Rp
                                    @endif{{ number_format($c['value'], 0, ',', '.') }}
                                </span>
                            </h2>
                        </div>

                        <div
                            class="bg-white {{ $c['text'] }} w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-xl shadow-sm border border-slate-50">
                            <i class="fa-solid {{ $c['icon'] }} text-lg"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bottom Insights: Top Products & Inventory --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col mb-5">
            <div class="flex items-center space-x-3 mb-8">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Status Inventaris</h3>
                    <p class="text-xs text-slate-500">Ketersediaan stok gudang</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 flex-grow">
                @foreach ($inventory as $status => $total)
                    @php
                        $theme = match ($status) {
                            'available' => [
                                'bg' => 'bg-emerald-50',
                                'border' => 'border-emerald-200',
                                'text' => 'text-emerald-700',
                                'icon' => 'fa-check-circle',
                                'label' => 'Tersedia',
                            ],
                            'sold' => [
                                'bg' => 'bg-rose-50',
                                'border' => 'border-rose-200',
                                'text' => 'text-rose-700',
                                'icon' => 'fa-xmark-circle',
                                'label' => 'Terjual',
                            ],
                        };
                    @endphp

                    <div onclick="openInventoryModal('{{ $status }}')"
                        class="{{ $theme['bg'] }} {{ $theme['border'] }} border p-5 rounded-2xl hover:shadow-lg cursor-pointer transition">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2 {{ $theme['text'] }}">
                                <i class="fa-solid {{ $theme['icon'] }}"></i>
                                <span class="text-xs font-bold uppercase tracking-wide">
                                    {{ $theme['label'] }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="text-3xl font-black {{ $theme['text'] }}">
                                {{ $total }}
                            </div>
                            <div class="text-xs font-semibold text-slate-500">
                                Produk
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Main Insights: Chart & Payments --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            {{-- Chart Penjualan --}}
            <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <i class="fa-solid fa-chart-line text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Tren Penjualan</h3>
                            <p class="text-xs text-slate-500">Visualisasi data seminggu terakhir</p>
                        </div>
                    </div>
                    <select
                        class="text-xs border-none bg-slate-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 font-semibold text-slate-600">
                        <option>7 Hari Terakhir</option>
                        <option>30 Hari Terakhir</option>
                    </select>
                </div>

                <div class="relative h-72">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                        <i class="fa-solid fa-wallet text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Metode Pembayaran</h3>
                        <p class="text-xs text-slate-500">Distribusi transaksi</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($paymentMethods as $pm)
                        @php
                            $method = strtolower($pm->payment_method);
                            $icon = 'fa-credit-card';
                            $color = 'text-slate-400';
                            $bg = 'bg-slate-50';

                            if (str_contains($method, 'tunai') || str_contains($method, 'cash')) {
                                $icon = 'fa-money-bill-wave';
                                $color = 'text-emerald-500';
                                $bg = 'bg-emerald-50';
                            } elseif (
                                str_contains($method, 'qris') ||
                                str_contains($method, 'gopay') ||
                                str_contains($method, 'ovo')
                            ) {
                                $icon = 'fa-qrcode';
                                $color = 'text-purple-500';
                                $bg = 'bg-purple-50';
                            } elseif (str_contains($method, 'transfer') || str_contains($method, 'bank')) {
                                $icon = 'fa-building-columns';
                                $color = 'text-blue-500';
                                $bg = 'bg-blue-50';
                            }
                        @endphp

                        <div
                            class="group flex items-center justify-between p-4 rounded-2xl border border-slate-200 shadow  hover:border-slate-200 hover:bg-slate-50 transition-all duration-300">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-10 h-10 {{ $bg }} {{ $color }} flex items-center justify-center rounded-xl transition-transform group-hover:scale-110">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>
                                <div>
                                    <span
                                        class="block text-sm font-bold text-slate-700">{{ strtoupper($pm->payment_method) }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="block text-sm font-extrabold text-center text-slate-900">{{ $pm->total }}</span>
                                <span class="text-[10px] text-slate-400">Transaksi</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Section Baru: Stock Distribution (Brand & Category) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                        <i class="fa-solid fa-tags text-lg"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">Stok Berdasarkan Merk</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($stockByBrand as $sb)
                        <div
                            class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-sm transition-all">
                            <span class="text-sm font-semibold text-slate-600">{{ $sb->name ?: 'Tanpa Merk' }}</span>
                            <span
                                class="px-2 py-1 bg-white rounded-lg text-xs font-black text-slate-800 shadow-sm">{{ $sb->total_stock }}
                                <span class="text-[10px] text-slate-400">Pcs</span></span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                        <i class="fa-solid fa-layer-group text-lg"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">Stok Berdasarkan Kategori</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($stockByCategory as $sc)
                        <div
                            class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-sm transition-all">
                            <span class="text-sm font-semibold text-slate-600">{{ $sc->name ?: 'Umum' }}</span>
                            <span
                                class="px-2 py-1 bg-white rounded-lg text-xs font-black text-slate-800 shadow-sm">{{ $sc->total_stock }}
                                <span class="text-[10px] text-slate-400">Pcs</span></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bottom Insights: Top Brands & Inventory Status --}}
        <div class="mb-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                            <i class="fa-solid fa-award text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-800">Merk Terlaris</h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-slate-400 bg-slate-50/50">
                                <th class="px-6 py-4">Merk</th>
                                <th class="px-6 py-4 text-center">Produk Terjual</th>
                                <th class="px-6 py-4 text-right">Profit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($topBrands as $brand)
                                <tr class="hover:bg-slate-50/80 transition-all group">
                                    <td class="px-6 py-4">
                                        <span
                                            class="font-bold text-slate-700 text-sm italic">{{ $brand->name ?: 'No Brand' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black text-slate-900">{{ $brand->total_sold }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">
                                            Rp {{ number_format($brand->total_profit, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="inventoryModal" class="fixed inset-0 bg-black/40 hidden z-50">
            <div class="bg-white max-w-4xl mx-auto mt-20 rounded-3xl shadow-xl overflow-hidden">

                {{-- HEADER --}}
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 id="modalTitle" class="font-bold text-slate-800 text-lg"></h3>
                    <button onclick="closeInventoryModal()" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                {{-- FILTER --}}
                <div class="px-6 py-4 grid grid-cols-2 gap-4 border-b bg-slate-50">
                    <select id="filterBrand" class="w-full">
                        <option value="">Semua Merk</option>
                        @foreach ($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>

                    <select id="filterCategory" class="w-full">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>


                {{-- LIST --}}
                <div class="p-6 max-h-[400px] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="text-slate-400 uppercase text-[10px]">
                            <tr>
                                <th class="text-left">Produk</th>
                                <th>Merk</th>
                                <th>Kategori</th>
                                <th class="text-right">Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTable" class="divide-y"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initSelect2() {
            $('#filterBrand').select2({
                placeholder: 'Pilih Merk',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#inventoryModal')
            });

            $('#filterCategory').select2({
                placeholder: 'Pilih Kategori',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#inventoryModal')
            });
        }
    </script>

    <script>
        const ctx = document.getElementById('salesChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salesChart->pluck('date')) !!},
                datasets: [{
                    label: 'Penjualan',
                    data: {!! json_encode($salesChart->pluck('total')) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#4f46e5'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });

        let currentStatus = null;

        function openInventoryModal(status) {
            currentStatus = status;

            document.getElementById('modalTitle').innerText =
                status === 'available' ? 'Produk Tersedia' : 'Produk Terjual';

            document.getElementById('inventoryModal').classList.remove('hidden');

            initSelect2();
            loadInventory();
        }

        function closeInventoryModal() {
            document.getElementById('inventoryModal').classList.add('hidden');
        }

        function loadInventory() {
            fetch(
                    `{{ route('dashboard.inventory') }}?status=${currentStatus}&brand_id=${filterBrand.value}&category_id=${filterCategory.value}`
                    )
                .then(res => res.json())
                .then(res => {
                    const tbody = document.getElementById('inventoryTable');
                    tbody.innerHTML = '';

                    res.products.forEach(p => {
                        tbody.innerHTML += `
                                    <tr>
                                        <td class="py-3 font-semibold">${p.name}</td>
                                        <td class="text-center">${p.brand?.name ?? '-'}</td>
                                        <td class="text-center">${p.category?.name ?? '-'}</td>
                                        <td class="text-right font-bold">Rp ${Number(p.selling_price).toLocaleString('id-ID')}</td>
                                    </tr>
                                `;
                    });
                });
        }

        filterBrand.onchange = loadInventory;
        filterCategory.onchange = loadInventory;
    </script>
@endpush
