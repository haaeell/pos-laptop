@extends('layouts.catalog')

@section('title', 'Produk | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .produk-section {
            padding: 32px 0 60px;
        }

        .filter-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-row select {
            min-width: 160px;
        }

        .filter-bar {
            display: flex;
            flex-direction: column;
            gap: 14px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 18px;
            margin-bottom: 24px;
            box-shadow: 0 18px 38px rgba(15, 23, 42, .05);
        }

        .filter-mobile-head {
            display: none;
        }

        .filter-panel {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-mobile-kicker {
            display: block;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #94A3B8;
            margin-bottom: 4px;
        }

        .filter-mobile-summary {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.4;
        }

        .filter-mobile-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 16px;
            border: 1px solid #D7E3F7;
            border-radius: 999px;
            background: #F8FBFF;
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .filter-group input[type="number"] {
            width: 120px;
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 13px;
        }

        .filter-price-grid {
            display: flex;
            align-items: flex-end;
            gap: 12px;
        }

        .filter-group select {
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 13px;
            background: #fff;
        }

        .filter-checkbox {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 600;
            padding-bottom: 9px;
            cursor: pointer;
            white-space: nowrap;
        }

        .filter-price-sep {
            padding-bottom: 9px;
            color: var(--muted);
        }

        #applyFilterBtn {
            padding: 9px 20px;
            font-size: 13px;
        }

        @media(max-width:640px) {
            .produk-section {
                padding-top: 12px;
            }

            .filter-bar {
                gap: 14px;
                padding: 14px;
                border-radius: 22px;
                margin-bottom: 18px;
            }

            .filter-mobile-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .filter-mobile-head > div {
                min-width: 0;
            }

            .filter-mobile-summary {
                font-size: 13px;
            }

            .filter-mobile-toggle {
                display: inline-flex;
                flex-shrink: 0;
            }

            .filter-panel {
                display: none;
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                padding-top: 4px;
            }

            .filter-bar.is-open .filter-panel {
                display: flex;
            }

            .filter-group {
                gap: 6px;
            }

            .filter-group input[type="number"],
            .filter-group select,
            .select2-container {
                width: 100% !important;
            }

            .filter-group input[type="number"],
            .filter-group select {
                min-height: 44px;
                font-size: 14px;
                border-radius: 12px;
            }

            .filter-price-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .filter-price-sep {
                display: none;
            }

            .filter-checkbox {
                min-height: 44px;
                padding: 12px 14px;
                border: 1px solid var(--line);
                border-radius: 14px;
                background: #F8FAFC;
            }

            #applyFilterBtn {
                width: 100%;
                min-height: 46px;
                border-radius: 14px;
                font-size: 14px;
            }
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .product-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 14px;
            transition: .25s;
            position: relative;
            min-width: 0;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .product-image {
            height: 170px;
            border-radius: 12px;
            background: #F6F8FB;
            display: grid;
            place-items: center;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-card.out-of-stock {
            filter: grayscale(1);
            opacity: .65;
        }

        .out-of-stock-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-18deg);
            border: 2.5px solid #98A2B3;
            color: #667085;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 4px 16px;
            border-radius: 6px;
            background: rgba(255, 255, 255, .75);
        }

        .product-card h3 {
            font-size: 14px;
            margin: 14px 0 6px;
            min-height: 38px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--muted);
            font-size: 11px;
        }

        .price {
            display: flex;
            align-items: end;
            justify-content: space-between;
            margin-top: 12px;
        }

        .price strong {
            color: var(--primary);
            font-size: 15px;
        }

        .detail-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--primary);
            cursor: pointer;
            flex-shrink: 0;
        }

        .detail-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 14px;
            margin-bottom: 36px;
        }

        .category-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px 10px;
            text-align: center;
            transition: .2s;
            cursor: pointer;
        }

        .category-card:hover {
            border-color: #B6CDF6;
            transform: translateY(-3px);
        }

        .category-card.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .category-icon {
            width: 54px;
            height: 54px;
            border-radius: 15px;
            background: var(--primary-soft);
            display: grid;
            place-items: center;
            margin: 0 auto 10px;
            font-size: 22px;
            color: var(--primary);
        }

        .category-card span {
            font-size: 12px;
            font-weight: 700;
        }

        .pagination-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            flex-wrap: wrap;
        }

        .pagination-btn,
        .pagination-ellipsis {
            min-width: 44px;
            height: 44px;
            padding: 0 14px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pagination-btn {
            cursor: pointer;
            transition: .18s ease;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #bfd3fb;
            background: #f8fbff;
            color: var(--primary);
            transform: translateY(-1px);
        }

        .pagination-btn.is-active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 10px 20px rgba(23, 92, 211, .18);
        }

        .pagination-btn:disabled {
            cursor: not-allowed;
            opacity: .45;
        }

        .pagination-ellipsis {
            border-style: dashed;
            color: var(--muted);
        }

        .empty-state {
            text-align: center;
            padding: 80px 0;
            color: var(--muted);
        }

        .skeleton {
            border-radius: 16px;
            height: 260px;
            border: 1px solid var(--line);
            background: linear-gradient(100deg, #F1F3F6 30%, #FAFBFC 50%, #F1F3F6 70%);
            background-size: 200% 100%;
            animation: shimmer 1.4s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        @media(max-width:960px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:640px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .category-grid {
                display: flex;
                overflow-x: auto;
                gap: 6px;
                margin-bottom: 18px;
                padding: 2px 0 6px;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }

            .category-grid::-webkit-scrollbar {
                display: none;
            }

            .category-card {
                flex: 0 0 68px;
                width: 68px;
                min-height: 80px;
                padding: 8px 4px;
                border-radius: 9px;
            }

            .category-icon {
                width: 30px;
                height: 30px;
                border-radius: 9px;
                margin-bottom: 5px;
                font-size: 12px;
            }

            .category-card span {
                font-size: 9px;
                line-height: 1.2;
            }

            .product-card {
                padding: 10px;
            }

            .product-card h3 {
                font-size: 12.5px;
                min-height: 32px;
                margin: 10px 0 4px;
            }

            .price {
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 8px;
            }

            .price strong {
                font-size: 13px;
            }

            .detail-btn {
                width: 30px;
                height: 30px;
                border-radius: 8px;
                font-size: 12px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="produk-section">
        <div class="container">
            <div class="section-head">
                <div>
                    <h1 style="font-size:24px;">Katalog Produk</h1>
                    <p>Pilihan produk terbaik untuk kebutuhan kerja, belajar, dan hiburan.</p>
                </div>
            </div>

            <div class="filter-bar" id="filterBar">
                <div class="filter-mobile-head">
                    <div>
                        <span class="filter-mobile-kicker">Filter Produk</span>
                        <span class="filter-mobile-summary" id="filterSummary">Semua produk ditampilkan</span>
                    </div>
                    <button type="button" id="toggleFilterBtn" class="filter-mobile-toggle" aria-expanded="false">
                        <i class="fa-solid fa-sliders"></i> Filter
                    </button>
                </div>

                <div class="filter-panel" id="filterPanel">
                    <div class="filter-group">
                        <label>Merek</label>
                        <select id="brand">
                            <option value="">Semua Merek</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-price-grid">
                        <div class="filter-group">
                            <label>Harga Min</label>
                            <input type="number" id="minPrice" placeholder="0" min="0">
                        </div>
                        <span class="filter-price-sep">—</span>
                        <div class="filter-group">
                            <label>Harga Max</label>
                            <input type="number" id="maxPrice" placeholder="Tanpa batas" min="0">
                        </div>
                    </div>

                    <label class="filter-checkbox">
                        <input type="checkbox" id="inStockOnly"> Hanya yang Tersedia
                    </label>

                    <div class="filter-group">
                        <label>Tampilkan</label>
                        <select id="perPage">
                            <option value="10">10 Produk</option>
                            <option value="20">20 Produk</option>
                            <option value="50">50 Produk</option>
                            <option value="100">100 Produk</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>

                    <button type="button" id="applyFilterBtn" class="btn btn-primary">
                        <i class="fa-solid fa-filter"></i> Terapkan
                    </button>
                </div>
            </div>

            @if($categories->count())
                <div class="category-grid" id="categoryGrid">
                    <div class="category-card active" data-category="" onclick="filterByCategory('')">
                        <div class="category-icon"><i class="fa-solid fa-border-all"></i></div>
                        <span>Semua</span>
                    </div>
                    @foreach($categories as $cat)
                        <div class="category-card" data-category="{{ $cat->id }}" onclick="filterByCategory('{{ $cat->id }}')">
                            <div class="category-icon"><i class="{{ $cat->icon ?: 'fa-solid fa-layer-group' }}"></i></div>
                            <span>{{ $cat->name }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div id="products-grid" class="product-grid"></div>
            <div id="empty" class="empty-state" style="display:none">Produk tidak ditemukan.</div>
            <div id="pagination" class="pagination-row"></div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const isCustomerAuthed = @json(Auth::guard('customers')->check());
        const pageParams = new URLSearchParams(window.location.search);
        const initialBrand = pageParams.get('brand') || '';
        const initialCategory = pageParams.get('category') || '';
        const isMobileViewport = window.matchMedia('(max-width: 640px)').matches;

        $(document).ready(function () {
            if (!isMobileViewport) {
                $('#brand').select2({ placeholder: "Semua Merek", allowClear: true });
            }

            brandEl.value = initialBrand;
            if (!isMobileViewport) {
                $('#brand').val(initialBrand).trigger('change.select2');
            }

            activeCategory = initialCategory;
            document.querySelectorAll('.category-card').forEach(c => {
                c.classList.toggle('active', c.dataset.category === String(activeCategory));
            });

            $('#brand').on('change', function () {
                currentPage = 1;
                loadProducts();
            });

            document.getElementById('perPage').addEventListener('change', function () {
                currentPage = 1;
                loadProducts();
            });

            document.getElementById('applyFilterBtn').addEventListener('click', function () {
                currentPage = 1;
                loadProducts();
                closeMobileFilter();
            });

            const toggleFilterBtn = document.getElementById('toggleFilterBtn');
            if (toggleFilterBtn) {
                toggleFilterBtn.addEventListener('click', function () {
                    document.getElementById('filterBar').classList.toggle('is-open');
                    this.setAttribute('aria-expanded', document.getElementById('filterBar').classList.contains('is-open') ? 'true' : 'false');
                });
            }

            const globalSearch = document.getElementById('global-search');
            if (globalSearch && globalSearch.value) {
                searchEl.value = globalSearch.value;
            }

            updateFilterSummary();
            loadProducts();
        });

        let currentPage = 1;
        let activeCategory = '';
        const searchEl = { value: (new URLSearchParams(window.location.search)).get('search') || '' };
        const brandEl = document.getElementById('brand');
        const minPriceEl = document.getElementById('minPrice');
        const maxPriceEl = document.getElementById('maxPrice');
        const inStockOnlyEl = document.getElementById('inStockOnly');
        const perPageEl = document.getElementById('perPage');
        const container = document.getElementById('products-grid');
        const emptyEl = document.getElementById('empty');
        const filterSummaryEl = document.getElementById('filterSummary');

        function filterByCategory(id) {
            activeCategory = id;
            currentPage = 1;
            document.querySelectorAll('.category-card').forEach(c => c.classList.toggle('active', c.dataset.category === String(id)));
            loadProducts();
        }

        function updateFilterSummary() {
            if (!filterSummaryEl) return;

            const parts = [];
            const brandName = brandEl.options[brandEl.selectedIndex]?.text?.trim();

            if (activeCategory) {
                const activeCategoryCard = document.querySelector(`.category-card[data-category="${activeCategory}"] span`);
                if (activeCategoryCard) {
                    parts.push(activeCategoryCard.textContent.trim());
                }
            }

            if (brandEl.value && brandName) {
                parts.push(brandName);
            }

            if (minPriceEl.value || maxPriceEl.value) {
                const min = minPriceEl.value ? formatCompactPrice(minPriceEl.value) : '0';
                const max = maxPriceEl.value ? formatCompactPrice(maxPriceEl.value) : 'tak terbatas';
                parts.push(`${min} - ${max}`);
            }

            if (inStockOnlyEl.checked) {
                parts.push('Stok tersedia');
            }

            if (perPageEl.value !== '10') {
                parts.push(perPageEl.value === 'all' ? 'Semua produk' : `${perPageEl.value} produk`);
            }

            filterSummaryEl.textContent = parts.length ? parts.join(' • ') : 'Semua produk ditampilkan';
        }

        function closeMobileFilter() {
            const filterBar = document.getElementById('filterBar');
            const toggleFilterBtn = document.getElementById('toggleFilterBtn');

            if (!filterBar || !toggleFilterBtn || !window.matchMedia('(max-width: 640px)').matches) {
                return;
            }

            filterBar.classList.remove('is-open');
            toggleFilterBtn.setAttribute('aria-expanded', 'false');
        }

        async function loadProducts() {
            const perPageVal = perPageEl.value;
            updateFilterSummary();
            container.innerHTML = skeleton(perPageVal === 'all' ? 12 : Math.min(Number(perPageVal) || 8, 12));
            emptyEl.style.display = 'none';

            const params = new URLSearchParams({
                search: searchEl.value,
                category: activeCategory,
                brand: brandEl.value,
                min_price: minPriceEl.value,
                max_price: maxPriceEl.value,
                in_stock_only: inStockOnlyEl.checked ? '1' : '',
                per_page: perPageVal,
                page: currentPage
            });

            const res = await fetch(`/data/catalog?${params}`);
            const resData = await res.json();

            container.innerHTML = '';

            if (!resData.data.length) {
                emptyEl.style.display = 'block';
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

            const pages = [];
            const current = meta.current_page;
            const last = meta.last_page;

            pages.push(1);
            for (let i = current - 1; i <= current + 1; i++) {
                if (i > 1 && i < last) pages.push(i);
            }
            if (last > 1) pages.push(last);

            const uniquePages = [...new Set(pages)].sort((a, b) => a - b);

            pag.innerHTML += `
                <button ${current === 1 ? 'disabled' : ''}
                    onclick="goPage(${current - 1})"
                    class="pagination-btn"
                    aria-label="Halaman sebelumnya">
                    ‹
                </button>`;

            uniquePages.forEach((page, index) => {
                const prevPage = uniquePages[index - 1];
                if (prevPage && page - prevPage > 1) {
                    pag.innerHTML += `<span class="pagination-ellipsis">...</span>`;
                }

                pag.innerHTML += `
                    <button onclick="goPage(${page})"
                        class="pagination-btn ${page === current ? 'is-active' : ''}"
                        aria-label="Ke halaman ${page}">
                        ${page}
                    </button>`;
            });

            pag.innerHTML += `
                <button ${current === last ? 'disabled' : ''}
                    onclick="goPage(${current + 1})"
                    class="pagination-btn"
                    aria-label="Halaman berikutnya">
                    ›
                </button>`;
        }

        function goPage(page) {
            currentPage = page;
            loadProducts();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function card(p) {
            const isSold = Boolean(p.is_sold);

            const imageHtml = p.image
                ? `<img src="/storage/${p.image}" alt="${p.name}" loading="lazy" decoding="async">`
                : `<i class="fa-solid fa-image" style="font-size:36px;color:#CBD5E1;"></i>`;

            const cartBtn = isSold
                ? `<button type="button" class="detail-btn" disabled style="display:flex;align-items:center;justify-content:center;opacity:.5;cursor:not-allowed;" aria-label="Sold"><i class="fa-solid fa-cart-plus"></i></button>`
                : (isCustomerAuthed
                    ? `<button type="button" class="detail-btn add-cart-btn" data-product-id="${p.id}" style="display:flex;align-items:center;justify-content:center;" aria-label="Tambah ke keranjang"><i class="fa-solid fa-cart-plus"></i></button>`
                    : `<a href="/akun/login?redirect=${encodeURIComponent(p.url)}" class="detail-btn" style="display:flex;align-items:center;justify-content:center;" aria-label="Tambah ke keranjang"><i class="fa-solid fa-cart-plus"></i></a>`);

            const hasDiscount = p.strike_price && Number(p.strike_price) > Number(p.price);
            const strikeHtml = hasDiscount
                ? `<span style="text-decoration:line-through;color:#98A2B3;font-size:11.5px;font-weight:500;display:block;">${formatPrice(p.strike_price)}</span>`
                : '';

            return `
            <article class="product-card ${isSold ? 'out-of-stock' : ''}">
                <a href="${p.url}" style="display:block;">
                    <div class="product-image">
                        ${imageHtml}
                        ${isSold ? '<span class="out-of-stock-badge">Sold</span>' : ''}
                    </div>
                    <h3>${p.name}</h3>
                    <div class="meta"><span>Kode: ${p.code}</span></div>
                </a>
                <div class="price">
                    <div>
                        ${strikeHtml}
                        <strong>${formatPrice(p.price)}</strong>
                    </div>
                    <div style="display:flex;gap:6px;">
                        ${cartBtn}
                        <a href="${p.url}" class="detail-btn" style="display:flex;align-items:center;justify-content:center;" aria-label="Lihat detail">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                </div>
            </article>`;
        }

        $(document).on('click', '.add-cart-btn', function () {
            const btn = $(this);
            const productId = btn.data('product-id');
            const token = document.querySelector('meta[name="csrf-token"]').content;

            btn.prop('disabled', true);

            fetch('/keranjang/tambah', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, qty: 1 }),
            })
                .then(res => {
                    if (res.ok) {
                        btn.html('<i class="fa-solid fa-check"></i>');
                        setTimeout(() => btn.html('<i class="fa-solid fa-cart-plus"></i>').prop('disabled', false), 1200);
                    } else {
                        btn.prop('disabled', false);
                    }
                })
                .catch(() => btn.prop('disabled', false));
        });

        function formatPrice(v) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(v);
        }

        function formatCompactPrice(v) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(v);
        }

        function skeleton(count = 8) {
            return Array(count).fill(`<div class="skeleton"></div>`).join('');
        }
    </script>
@endpush
