<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Barokah Computer')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.jpeg') }}">

    <!-- Font -->
    <link href="https://fonts.bunny.net/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        table.dataTable thead th {
            background-color: #f8fafc;
        }

        .select2-container .select2-selection--single {
            height: 42px;
            border-radius: 0.75rem;
            border: 1px solid #d1d5db;
            padding: 6px 12px;
            display: flex;
            align-items: center;
        }

        .select2-selection__rendered {
            padding-left: 0 !important;
        }

        .select2-selection__arrow {
            height: 100%;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #2563eb;
            outline: none;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-700">

    <div class="min-h-screen">

        <!-- Overlay -->
        <div id="overlay" class="fixed inset-0 bg-black/40 z-30 opacity-0 pointer-events-none transition md:hidden">
        </div>

        @php
            function isActive($pattern)
            {
                return request()->is($pattern)
                    ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md'
                    : 'text-slate-600 hover:bg-slate-100';
            }
        @endphp


        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64
        bg-white border-r border-slate-200
        shadow-sm transform -translate-x-full md:translate-x-0
        transition-transform duration-300 flex flex-col overflow-y-auto">

            <!-- LOGO -->
            <div class="h-16 flex items-center px-10 text-sm font-semibold border-b">
                <img src="/logo.jpeg" class="w-8 h-8" alt=""> Barokah Computer
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">

                {{-- ===================== DASHBOARD — SEMUA ROLE ===================== --}}
                <a href="/home"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('home') }}">
                    <i class="fa-solid fa-gauge-high w-5"></i>
                    Dashboard
                </a>

                {{-- ===================== TRANSAKSI — SEMUA ROLE ===================== --}}
                <div class="mt-6 pt-4 mb-2 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Transaksi
                </div>

                <a href="/sales"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('sales') }}">
                    <i class="fa-solid fa-cash-register w-5"></i>
                    Penjualan
                </a>

                <a href="/sales/create"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('sales/create') }}">
                    <i class="fa-solid fa-receipt w-5"></i>
                    Transaksi Baru
                </a>

                <a href="/services"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('services*') }}">
                    <i class="fa-solid fa-screwdriver-wrench w-5"></i>
                    Service
                </a>

                <a href="/orders"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('orders*') }}">
                    <i class="fa-solid fa-bag-shopping w-5"></i>
                    <span class="flex-1">Pesanan Online</span>
                    @if(($newOrdersCount ?? 0) > 0)
                        <span id="sidebarOrdersBadge" class="min-w-[20px] h-5 px-1.5 flex items-center justify-center
                            text-[11px] font-bold rounded-full bg-red-500 text-white">
                            {{ $newOrdersCount }}
                        </span>
                    @endif
                </a>

                <a href="/customers"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('customers*') }}">
                    <i class="fa-solid fa-users-viewfinder w-5"></i>
                    Customer Online
                </a>

                <a href="/products"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('products*') }}">
                    <i class="fa-solid fa-box-open w-5"></i>
                    Master Produk
                </a>

                @if (Auth::user()->isSuperAdmin())

                    {{-- ===================== MASTER DATA — ADMIN ONLY ===================== --}}
                    <div class="mt-6 pt-4 mb-2 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Master Data
                    </div>

                    <a href="/categories"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('categories*') }}">
                        <i class="fa-solid fa-layer-group w-5"></i>
                        Kategori
                    </a>

                    <a href="/brands"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('brands*') }}">
                        <i class="fa-solid fa-tags w-5"></i>
                        Brand
                    </a>

                    <a href="/articles"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('article*') }}">
                        <i class="fa-solid fa-newspaper w-5"></i>
                        Artikel
                    </a>

                    <a href="/reviews"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('reviews*') }}">
                        <i class="fa-solid fa-star w-5"></i>
                        Ulasan Produk
                    </a>

                    <a href="/penjuals"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('penjuals*') }}">
                        <i class="fa-solid fa-user w-5"></i>
                        Marketing
                    </a>

                    <a href="/employees"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('employees*') }}">
                        <i class="fa-solid fa-users w-5"></i>
                        Karyawan
                    </a>

                    {{-- ===================== KEUANGAN — ADMIN ONLY ===================== --}}
                    <div class="mt-6 pt-4 mb-2 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Keuangan
                    </div>

                    <a href="/expenses"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('expenses') }}">
                        <i class="fa-solid fa-money-bill-wave w-5"></i>
                        Pengeluaran
                    </a>

                    <a href="/modals"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('modals*') }}">
                        <i class="fa-solid fa-hand-holding-dollar w-5"></i>
                        Modal / Hutang
                    </a>

                    <a href="/payrolls"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('payrolls*') }}">
                        <i class="fa-solid fa-coins w-5"></i>
                        Penggajian
                    </a>

                    {{-- ===================== LAPORAN — ADMIN ONLY ===================== --}}
                    <div class="mt-6 pt-4 mb-2 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Laporan
                    </div>

                    <a href="/reports"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('reports*') }}">
                        <i class="fa-solid fa-chart-line w-5"></i>
                        Laporan Penjualan
                    </a>

                    {{-- ===================== PENGATURAN — ADMIN ONLY ===================== --}}
                    <div class="mt-6 pt-4 mb-2 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Pengaturan
                    </div>

                    <a href="/contacts"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('contacts*') }}">
                        <i class="fa-brands fa-whatsapp w-5"></i>
                        Nomor WhatsApp
                    </a>

                    <a href="/settings"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('settings*') }}">
                        <i class="fa-solid fa-gear w-5"></i>
                        Setting Toko
                    </a>

                    <a href="/couriers"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('couriers*') }}">
                        <i class="fa-solid fa-truck w-5"></i>
                        Kurir
                    </a>

                    <a href="/users"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('users*') }}">
                        <i class="fa-solid fa-user-shield w-5"></i>
                        Manajemen User
                    </a>

                @endif

            </nav>
        </aside>


        <!-- CONTENT -->
        <div class="flex flex-col min-h-screen md:ml-64">

            <!-- HEADER -->
            <header class="h-16 bg-white border-b shadow-sm flex items-center justify-between px-4 md:px-6 sticky top-0 z-20">

                <button id="menuBtn" class="p-2 rounded-lg hover:bg-indigo-50 text-indigo-600 md:hidden">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="flex items-center gap-2 ml-auto">

                <div class="relative" id="notifDropdown">
                    <button type="button" class="relative p-2 rounded-lg hover:bg-indigo-50 text-slate-500 hover:text-indigo-600 focus:outline-none" id="notifBtn">
                        <i class="fa-solid fa-bell text-lg"></i>
                        <span id="notifBadge"
                            class="absolute top-0.5 right-0.5 min-w-[16px] h-4 px-1 flex items-center justify-center
                            text-[10px] font-bold rounded-full bg-red-500 text-white {{ ($newOrdersCount ?? 0) > 0 ? '' : 'hidden' }}">
                            {{ $newOrdersCount ?? 0 }}
                        </span>
                    </button>

                    <!-- NOTIF DROPDOWN -->
                    <div id="notifMenu" class="absolute right-0 mt-3 w-80 max-w-[90vw] bg-white border rounded-xl shadow-lg
                               opacity-0 invisible transition z-50 max-h-96 overflow-y-auto">
                        <div class="px-4 py-3 border-b font-semibold text-sm text-slate-700">
                            Pesanan Baru
                        </div>

                        <div id="notifList">
                            @forelse(($newOrders ?? []) as $order)
                                <a href="{{ route('orders.show', $order->id) }}"
                                    class="flex flex-col gap-0.5 px-4 py-3 text-sm hover:bg-slate-50 border-b last:border-b-0">
                                    <span class="font-semibold text-slate-800">{{ $order->order_number }}</span>
                                    <span class="text-slate-500 text-xs">{{ $order->customer?->name ?? $order->recipient_name }} &bull; Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                    <span class="text-slate-400 text-xs">{{ $order->created_at->diffForHumans() }}</span>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-slate-400">
                                    Tidak ada pesanan baru
                                </div>
                            @endforelse
                        </div>

                        <a href="/orders?status=paid"
                            class="block text-center px-4 py-2.5 text-xs font-semibold text-indigo-600 hover:bg-slate-50 border-t">
                            Lihat Semua Pesanan
                        </a>
                    </div>
                </div>

                <div class="relative" id="profileDropdown">
                    <button type="button" class="flex items-center gap-3 focus:outline-none" id="profileBtn">

                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-slate-400">Administrator</div>
                        </div>

                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}"
                            class="w-9 h-9 rounded-full border">
                    </button>

                    <!-- DROPDOWN -->
                    <div id="profileMenu" class="absolute right-0 mt-3 w-48 bg-white border rounded-xl shadow-lg
                               opacity-0 invisible transition z-50">

                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-3 text-sm hover:bg-slate-100 rounded-t-xl">
                            <i class="fa-solid fa-user"></i>
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-3 text-sm
                                       text-red-600 hover:bg-red-50 rounded-b-xl">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                </div>

            </header>

            <!-- MAIN -->
            <main class="p-4 md:p-6">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function () {
            const sidebar = $('#sidebar')
            const overlay = $('#overlay')

            $('#menuBtn').on('click', function () {
                sidebar.toggleClass('-translate-x-full')
                overlay.toggleClass('opacity-0 pointer-events-none')
            })

            overlay.on('click', function () {
                sidebar.addClass('-translate-x-full')
                overlay.addClass('opacity-0 pointer-events-none')
            })

            $('.datatable').DataTable({ responsive: true, pageLength: 10 })

            $('#profileBtn').on('click', function (e) {
                e.stopPropagation()
                $('#notifMenu').addClass('opacity-0 invisible')
                $('#profileMenu').toggleClass('opacity-0 invisible')
            })

            $('#notifBtn').on('click', function (e) {
                e.stopPropagation()
                $('#profileMenu').addClass('opacity-0 invisible')
                $('#notifMenu').toggleClass('opacity-0 invisible')
            })

            $(document).on('click', function () {
                $('#profileMenu').addClass('opacity-0 invisible')
                $('#notifMenu').addClass('opacity-0 invisible')
            })

            @auth
            function refreshOrderNotifications() {
                fetch('{{ route('orders.notifications.latest') }}', { headers: { 'Accept': 'application/json' } })
                    .then(res => res.ok ? res.json() : null)
                    .then(data => {
                        if (!data) return

                        const count = data.count || 0
                        const badge = $('#notifBadge')
                        const sidebarBadge = $('#sidebarOrdersBadge')

                        if (count > 0) {
                            badge.text(count).removeClass('hidden')
                            if (sidebarBadge.length) {
                                sidebarBadge.text(count)
                            }
                        } else {
                            badge.addClass('hidden')
                            sidebarBadge.text('0').addClass('hidden')
                        }

                        const list = $('#notifList')
                        if (!data.orders || data.orders.length === 0) {
                            list.html('<div class="px-4 py-6 text-center text-sm text-slate-400">Tidak ada pesanan baru</div>')
                            return
                        }

                        list.html(data.orders.map(order => `
                            <a href="${order.url}" class="flex flex-col gap-0.5 px-4 py-3 text-sm hover:bg-slate-50 border-b last:border-b-0">
                                <span class="font-semibold text-slate-800">${order.order_number}</span>
                                <span class="text-slate-500 text-xs">${order.customer_name ?? '-'} &bull; Rp ${Number(order.grand_total).toLocaleString('id-ID')}</span>
                                <span class="text-slate-400 text-xs">${order.created_at}</span>
                            </a>
                        `).join(''))
                    })
                    .catch(() => {})
            }

            setInterval(refreshOrderNotifications, 30000)
            @endauth
        })
    </script>

    @stack('scripts')

    @if ($errors->any())
        <script>
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += "{{ $error }}\n";
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorMessages,
            });
        </script>
    @endif

    @if (session('success') || session('error'))
        <script>
            $(document).ready(function () {
                var successMessage = "{{ session('success') }}";
                var errorMessage = "{{ session('error') }}";

                if (successMessage) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: successMessage,
                    });
                }

                if (errorMessage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                }
            });
        </script>
    @endif

</body>

</html>
