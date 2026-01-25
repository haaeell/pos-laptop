<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Katalog Elektronik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
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

<body class="bg-gray-50 text-gray-800">

    <!-- ================= STORE HEADER ================= -->
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <!-- LEFT : LOGO + STORE INFO -->
            <div class="flex items-center gap-4">
                <!-- LOGO -->
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-600
                       flex items-center justify-center text-white text-2xl font-bold">
                    E
                </div>

                <!-- STORE INFO -->
                <div>
                    <h1 class="text-lg font-bold leading-tight">
                        Barokah Computer
                    </h1>
                    <p class="text-sm text-gray-500 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-indigo-600"></i>
                        Jl. Sudirman No. 88, Jakarta
                    </p>
                </div>
            </div>

            <!-- RIGHT : CONTACT -->
            <div class="flex items-center gap-3">
                <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-green-500 hover:bg-green-600
                       text-white text-sm font-semibold transition">
                    <i class="fa-brands fa-whatsapp"></i>
                    WhatsApp
                </a>

                <span class="hidden md:flex items-center gap-2 text-sm text-gray-500">
                    <i class="fa-solid fa-clock text-indigo-600"></i>
                    Buka 09.00 – 21.00
                </span>
            </div>

        </div>
    </header>

    <!-- ================= HERO ================= -->
    <section class="relative">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-blue-600 to-blue-600"></div>
        <div class="relative max-w-7xl mx-auto px-6 pb-24 py-12 text-white">
            <p class=" text-indigo-100 max-w-xl text-lg">
                Laptop, aksesoris & elektronik lainnya. Dapatkan produk berkualitas dengan harga terbaik.
            </p>
        </div>
    </section>

    <!-- ================= FILTER ================= -->
    <section class="-mt-16 relative z-10">
        <div class="max-w-7xl mx-auto px-6">
            <div
                class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl px-6 py-5 flex flex-col lg:flex-row gap-4 items-center">

                <div class="relative flex-1 w-full">
                    <input id="search" type="text" placeholder="Cari nama produk atau kode..." class="w-full h-14 pl-12 pr-4 rounded-2xl border border-gray-200
                               focus:ring-2 focus:ring-indigo-500 transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-4 text-gray-400"></i>
                </div>

                <select id="category"
                    class="h-14 px-6 rounded-2xl border border-gray-200 text-sm font-medium focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select id="brand"
                    class="h-14 px-6 rounded-2xl border border-gray-200 text-sm font-medium focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Merek</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <!-- ================= CATALOG ================= -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <div id="products" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4"></div>

        <div id="empty" class="hidden text-center py-32 text-gray-400">
            Produk tidak ditemukan.
        </div>
        <div id="pagination" class="flex justify-center gap-2 mt-12"></div>
    </section>


    <!-- ================= MODAL ================= -->
    <div id="modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">

        <div class="bg-white rounded-3xl max-w-lg w-full p-6 relative">

            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

            <div id="m-image"
                class="h-48 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 overflow-hidden">
            </div>


            <h3 id="m-name" class="text-xl font-semibold"></h3>
            <p id="m-code" class="text-sm text-gray-500 mt-1"></p>

            <div class="flex gap-2 mt-4">
                <span id="m-category" class="text-xs px-3 py-1 rounded-full bg-indigo-100 text-indigo-700"></span>
                <span id="m-condition" class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700"></span>
            </div>

            <p id="m-desc" class="text-sm text-gray-600 mt-5 leading-relaxed"></p>

            <div class="mt-6 flex justify-between items-center">
                <span id="m-price" class="text-2xl font-bold text-indigo-600"></span>

                <a href="https://wa.me/6281234567890" target="_blank"
                    class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl text-sm font-semibold">
                    <i class="fa-brands fa-whatsapp mr-2"></i>Chat Penjual
                </a>
            </div>
        </div>
    </div>




    <!-- Select2 JS -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#category').select2({
                placeholder: "Semua Kategori",
                allowClear: true,
            });

            $('#brand').select2({
                placeholder: "Semua Merek",
                allowClear: true,
            });

            $('#category, #brand').on('change', function () {
                currentPage = 1;
                loadProducts();
            });

            $('#search').on('input', function () {
                currentPage = 1;
                debounceLoad();
            });

            loadProducts();
        });
        let timeout = null;
        let currentPage = 1;
        const searchEl = document.getElementById('search');
        const categoryEl = document.getElementById('category');
        const brandEl = document.getElementById('brand');
        const container = document.getElementById('products');
        const emptyEl = document.getElementById('empty');

        function debounceLoad() {
            clearTimeout(timeout);
            timeout = setTimeout(loadProducts, 300);
        }

        async function loadProducts() {
            container.innerHTML = skeleton();
            emptyEl.classList.add('hidden');

            const params = new URLSearchParams({
                search: searchEl.value,
                category: categoryEl.value,
                brand: brandEl.value,
                page: currentPage
            });

            const res = await fetch(`/data/catalog?${params}`);
            const resData = await res.json();

            container.innerHTML = '';

            if (!resData.data.length) {
                emptyEl.classList.remove('hidden');
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            resData.data.forEach(p => container.innerHTML += card(p));
            renderPagination(resData.meta);
        }
        function renderPagination(meta) {
            const pag = document.getElementById('pagination');
            pag.innerHTML = '';

            if (meta.last_page <= 1) return;

            // Prev
            pag.innerHTML += `
        <button ${meta.current_page === 1 ? 'disabled' : ''}
            onclick="goPage(${meta.current_page - 1})"
            class="px-4 py-2 rounded-lg border text-sm
            ${meta.current_page === 1 ? 'text-gray-400' : 'hover:bg-indigo-50'}">
            ‹
        </button>
    `;

            // Numbers
            for (let i = 1; i <= meta.last_page; i++) {
                pag.innerHTML += `
            <button onclick="goPage(${i})"
                class="px-4 py-2 rounded-lg text-sm
                ${i === meta.current_page
                        ? 'bg-indigo-600 text-white'
                        : 'border hover:bg-indigo-50'}">
                ${i}
            </button>
        `;
            }

            // Next
            pag.innerHTML += `
        <button ${meta.current_page === meta.last_page ? 'disabled' : ''}
            onclick="goPage(${meta.current_page + 1})"
            class="px-4 py-2 rounded-lg border text-sm
            ${meta.current_page === meta.last_page ? 'text-gray-400' : 'hover:bg-indigo-50'}">
            ›
        </button>
    `;
        }
        function goPage(page) {
            currentPage = page;
            loadProducts();
        }


        function card(p) {
            const imageHtml = p.image
                ? `<img src="/storage/${p.image}" class="w-full h-full object-cover">`
                : `<i class="fa-solid fa-image text-slate-300 text-4xl"></i>`;
            return `
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                 <div class="h-32 bg-gray-100 flex items-center justify-center">
            ${imageHtml}
        </div>

                <div class="p-3">
                    <h3 class="text-sm font-semibold line-clamp-2">${p.name}</h3>
                    <p class="text-xs text-gray-500 mt-1">Kode: ${p.code}</p>

                    <div class="mt-2 text-sm font-bold text-indigo-600">
                        ${formatPrice(p.price)}
                    </div>

                    <button onclick='openModal(${JSON.stringify(p)})'
                        class="mt-3 w-full text-xs py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                        <i class="fa-solid fa-eye mr-1"></i>Lihat Detail
                    </button>
                </div>
            </div>`;
        }

        function openModal(p) {
            document.getElementById('m-name').innerText = p.name;
            document.getElementById('m-code').innerText = `Kode Produk: ${p.code}`;
            document.getElementById('m-category').innerText = p.category;
            document.getElementById('m-condition').innerText = p.condition === 'used' ? 'Bekas' : 'Baru';
            document.getElementById('m-desc').innerText = p.description || 'Tidak ada deskripsi.';
            document.getElementById('m-price').innerText = formatPrice(p.price);
            const imageContainer = document.getElementById('m-image');
            imageContainer.innerHTML = p.image
                ? `<img src="/storage/${p.image}" class="w-full h-full object-contain">`
                : `<i class="fa-solid fa-image text-slate-300 text-9xl"></i>`;

            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            document.getElementById('modal').classList.remove('flex');
        }

        function formatPrice(v) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(v);
        }

        function skeleton() {
            return Array(10).fill(`
                <div class="animate-pulse bg-white rounded-xl h-56"></div>
            `).join('');
        }

        loadProducts();
    </script>

</body>

</html>