<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin') }}</title>

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
</head>

<body class="bg-slate-50 text-slate-700">

    <div class="flex min-h-screen">

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
        <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-40 w-64
        bg-white border-r border-slate-200
        shadow-sm transform -translate-x-full md:translate-x-0
        transition-all duration-300 flex flex-col">

            <!-- LOGO -->
            <div class="h-16 flex items-center px-6 text-lg font-semibold text-indigo-600 border-b">
                <i class="fa-solid fa-store mr-2"></i> Barokah Computer
            </div>

            <!-- NAV -->
            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">

                <a href="/home"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('home') }}">
                    <i class="fa-solid fa-gauge-high w-5"></i>
                    Dashboard
                </a>

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

                <a href="/products"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('products*') }}">
                    <i class="fa-solid fa-box-open w-5"></i>
                    Produk
                </a>

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

                <div class="mt-6 pt-4 mb-2 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Laporan
                </div>

                <a href="/reports"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ isActive('reports*') }}">
                    <i class="fa-solid fa-chart-line w-5"></i>
                    Laporan Penjualan
                </a>
            </nav>

            <!-- LOGOUT -->
            <div class="p-4 border-t">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full bg-red-500 flex items-center justify-center gap-2 px-4 py-2 rounded-xl
                    text-white hover:bg-red-400 transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>


        <!-- CONTENT -->
        <div class="flex-1 flex flex-col">

            <!-- HEADER -->
            <header class="h-16 bg-white border-b shadow-sm flex items-center justify-between px-4 md:px-6">

                <button id="menuBtn" class="p-2 rounded-lg hover:bg-indigo-50 text-indigo-600 md:hidden">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="flex items-center gap-3 ml-auto">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-xs text-slate-400">Administrator</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}"
                        class="w-9 h-9 rounded-full border">
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
