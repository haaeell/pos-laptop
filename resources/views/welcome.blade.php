@extends('layouts.catalog')

@section('title', ($settings['nama_toko'] ?? 'Barokah Computer') . ' | Toko Komputer Subang - Laptop, Aksesoris & Service')
@section('meta_description', ($settings['nama_toko'] ?? 'Barokah Computer') . ' - Toko komputer Subang terpercaya. Jual beli dan service laptop, komputer, aksesoris, serta perangkat elektronik berkualitas di Subang.')

@push('scripts')
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "ElectronicsStore",
            "name": "{{ $settings['nama_toko'] ?? 'Barokah Computer' }}",
            "image": "{{ asset('storage/' . ($settings['logo'] ?? 'logo.jpeg')) }}",
            "description": "{{ ($settings['nama_toko'] ?? 'Barokah Computer') . ' - Toko komputer Subang terpercaya. Jual beli dan service laptop, komputer, aksesoris, serta perangkat elektronik berkualitas di Subang.' }}",
            "address": {
                "@@type": "PostalAddress",
                "streetAddress": "{{ $settings['alamat'] ?? '' }}",
                "addressLocality": "Subang",
                "addressRegion": "Jawa Barat",
                "addressCountry": "ID"
            },
            "url": "{{ url('/') }}",
            "telephone": "{{ optional($contacts->first())->phone }}",
            "openingHours": "{{ $settings['jam_buka'] ?? '' }}"
        }
    </script>
@endpush

@section('styles')
    <style>
        .hero {
            padding: 24px 0 10px;
        }

        .hero-card {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            background: linear-gradient(110deg, #F8FBFF 0%, #EEF4FF 55%, #E6EFFF 100%);
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            min-height: 370px;
            border: 1px solid #DCE7FA;
        }

        .hero-copy {
            padding: 52px;
            align-self: center;
        }

        .hero-copy h1 {
            font-size: 44px;
            line-height: 1.1;
            letter-spacing: -1.6px;
            max-width: 560px;
            color: var(--text);
        }

        .hero-copy h1 span {
            color: var(--primary);
        }

        .hero-card p {
            color: var(--muted);
            max-width: 530px;
            font-size: 15px;
            margin: 18px 0 24px;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 16px;
        }

        .hero-visual {
            position: relative;
            display: grid;
            place-items: center;
            padding: 26px;
        }

        .hero-visual:before {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .8);
        }

        .hero-visual img {
            position: relative;
            z-index: 2;
            width: 390px;
            border-radius: 16px;
            filter: drop-shadow(0 26px 28px rgba(16, 24, 40, .22));
        }

        .promo-chip {
            position: absolute;
            z-index: 3;
            right: 38px;
            top: 48px;
            background: #fff;
            border: 1px solid var(--line);
            padding: 12px 14px;
            border-radius: 13px;
            box-shadow: var(--shadow);
            font-size: 12px;
        }

        .promo-chip strong {
            color: var(--primary);
            display: block;
        }

        .benefits {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            margin-top: 16px;
            box-shadow: 0 8px 20px rgba(16, 24, 40, .04);
        }

        .benefit {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 20px;
            border-right: 1px solid var(--line);
        }

        .benefit:last-child {
            border-right: 0;
        }

        .benefit-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .benefit strong {
            display: block;
            font-size: 13px;
        }

        .benefit span {
            font-size: 11px;
            color: var(--muted);
        }

        .filter-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-row select {
            min-width: 180px;
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

        .category-card-link {
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .category-card:hover {
            border-color: #B6CDF6;
            transform: translateY(-3px);
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
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .service-banner {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            background: linear-gradient(100deg, #1257C5, #1E73EA);
            border-radius: 22px;
            overflow: hidden;
            color: #fff;
            min-height: 255px;
        }

        .service-copy {
            padding: 42px;
        }

        .service-copy h2 {
            font-size: 28px;
            max-width: 520px;
            line-height: 1.15;
        }

        .service-copy p {
            margin: 14px 0 22px;
            color: rgba(255, 255, 255, .82);
            max-width: 560px;
            font-size: 14px;
        }

        .service-points {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 12px;
            margin-bottom: 24px;
        }

        .service-photo {
            background: url('https://images.unsplash.com/photo-1588702547923-7093a6c3ba33?auto=format&fit=crop&w=1000&q=80') center/cover;
            min-height: 255px;
        }

        .why-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .why-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 22px;
        }

        .why-card .why-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            margin-bottom: 16px;
            font-size: 16px;
        }

        .why-card h3 {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .why-card p {
            font-size: 12px;
            color: var(--muted);
        }

        .brands {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 14px;
            align-items: center;
        }

        .brand-chip {
            height: 70px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            display: grid;
            place-items: center;
            color: #475467;
            font-weight: 800;
            font-size: 15px;
            text-align: center;
            padding: 8px;
        }

        .brand-chip-link {
            display: grid;
            place-items: center;
            width: 100%;
            height: 100%;
            color: inherit;
            text-decoration: none;
        }

        .brand-chip-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .testimonial {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 22px;
        }

        .testimonial p {
            font-size: 13px;
            color: #475467;
            margin-bottom: 18px;
        }

        .person {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--primary-soft);
            display: grid;
            place-items: center;
            font-weight: 800;
            color: var(--primary);
            flex-shrink: 0;
        }

        .person strong {
            display: block;
            font-size: 13px;
        }

        .person span {
            font-size: 11px;
            color: var(--muted);
        }

        .contact-bar {
            background: var(--primary);
            color: #fff;
            border-radius: 18px;
            padding: 24px 28px;
            display: grid;
            grid-template-columns: 1.1fr 1fr 1fr;
            gap: 22px;
        }

        .contact-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .contact-item strong {
            display: block;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .contact-item span {
            font-size: 11px;
            color: rgba(255, 255, 255, .78);
        }

        @media(max-width:960px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .benefits {
                grid-template-columns: repeat(2, 1fr);
            }

            .benefit:nth-child(2) {
                border-right: 0;
            }

            .benefit:nth-child(-n+2) {
                border-bottom: 1px solid var(--line);
            }

            .contact-bar {
                grid-template-columns: 1fr;
            }

            .hero-card {
                grid-template-columns: 1fr;
            }

            .hero-copy {
                padding: 38px;
            }

            .hero-copy h1 {
                font-size: 36px;
            }

            .hero-visual {
                min-height: 260px;
            }

            .service-banner {
                grid-template-columns: 1fr;
            }

            .service-photo {
                min-height: 180px;
            }

            .why-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .brands {
                grid-template-columns: repeat(4, 1fr);
            }

            .testimonial-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:640px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
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

            .hero-copy {
                padding: 28px 22px;
            }

            .hero-copy h1 {
                font-size: 30px;
            }

            .hero-card p {
                font-size: 13px;
            }

            .hero-visual img {
                width: 280px;
            }

            .promo-chip {
                right: 18px;
                top: 24px;
            }

            .benefits {
                grid-template-columns: repeat(2, 1fr);
                border-radius: 14px;
                margin-top: 12px;
            }

            .benefit {
                padding: 12px;
                gap: 8px;
            }

            .benefit-icon {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .benefit strong {
                font-size: 11px;
            }

            .benefit span {
                font-size: 10px;
            }

            /* Category: horizontal scroll chips, app style */
            .category-grid {
                display: flex;
                overflow-x: auto;
                gap: 12px;
                padding-bottom: 4px;
                scrollbar-width: none;
            }

            .category-grid::-webkit-scrollbar {
                display: none;
            }

            .category-card {
                flex: 0 0 auto;
                width: 74px;
                padding: 12px 6px;
                border-radius: 16px;
            }

            .category-icon {
                width: 46px;
                height: 46px;
                font-size: 18px;
                margin-bottom: 6px;
            }

            .category-card span {
                font-size: 10.5px;
            }

            .filter-row {
                width: 100%;
            }

            .filter-row select {
                width: 100%;
            }

            .product-card {
                border-radius: 14px;
                padding: 10px;
            }

            .product-image {
                height: 128px;
                border-radius: 10px;
            }

            .product-card h3 {
                font-size: 12px;
                margin: 10px 0 4px;
                min-height: 32px;
            }

            .price strong {
                font-size: 13px;
            }

            .detail-btn {
                width: 30px;
                height: 30px;
            }

            .service-banner {
                border-radius: 16px;
            }

            .service-copy {
                padding: 22px 18px;
            }

            .service-copy h2 {
                font-size: 19px;
            }

            .service-copy p {
                font-size: 12px;
            }

            .service-points {
                gap: 10px;
                font-size: 11px;
                margin-bottom: 16px;
            }

            .why-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .why-card {
                padding: 14px;
                border-radius: 14px;
            }

            .why-card .why-icon {
                width: 34px;
                height: 34px;
                margin-bottom: 10px;
            }

            .why-card h3 {
                font-size: 12.5px;
            }

            .why-card p {
                font-size: 11px;
            }

            .brands {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }

            .brand-chip {
                height: 56px;
                font-size: 12px;
                border-radius: 12px;
            }

            .testimonial {
                border-radius: 14px;
                padding: 16px;
            }

            .contact-bar {
                border-radius: 14px;
                padding: 18px;
            }
        }
    </style>
@endsection

@section('content')
    <main>
        <section class="hero" id="home">
            <div class="container">
                <div class="hero-card">
                    <div class="hero-copy">
                        <div class="eyebrow">● {{ strtoupper($namaToko) }}</div>
                        <h1>Laptop Terbaik untuk Produktivitas <span>Tanpa Batas</span></h1>
                        <p>{{ $deskripsi }}</p>
                        <div class="hero-actions">
                            <a href="#products" class="btn btn-primary">Belanja Sekarang →</a>
                            <a href="#service" class="btn btn-light">Lihat Layanan</a>
                        </div>
                    </div>
                    <div class="hero-visual">
                        <div class="promo-chip"><strong>Cicilan 0%</strong>Hingga 12 bulan</div>
                        <img alt="Laptop modern"
                            src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=900&q=90" />
                    </div>
                </div>

                <div class="benefits">
                    <div class="benefit">
                        <div class="benefit-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <div><strong>Pengiriman Cepat</strong><span>Aman dan terpercaya</span></div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-icon"><i class="fa-solid fa-check"></i></div>
                        <div><strong>Garansi Resmi</strong><span>Produk bergaransi</span></div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-icon"><i class="fa-solid fa-star"></i></div>
                        <div><strong>Produk Original</strong><span>100% berkualitas</span></div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                        <div><strong>Buka {{ $jamBuka }}</strong><span>Konsultasi & service</span></div>
                    </div>
                </div>
            </div>
        </section>

        @if($categories->count())
            <section id="categories">
                <div class="container">
                    <div class="section-head">
                        <div>
                            <h2>Kategori Belanja</h2>
                            <p>Temukan produk lebih cepat berdasarkan kebutuhan Anda.</p>
                        </div>
                    </div>
                    <div class="category-grid">
                        @foreach($categories as $cat)
                            <a href="{{ route('catalog.listing', ['category' => $cat->id]) }}" class="category-card category-card-link" aria-label="Lihat produk kategori {{ $cat->name }}">
                                <div class="category-icon"><i class="{{ $cat->icon ?: 'fa-solid fa-layer-group' }}"></i></div>
                                <span>{{ $cat->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section id="products">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2>Katalog Produk</h2>
                        <p>Pilihan produk terbaik untuk kebutuhan kerja, belajar, dan hiburan.</p>
                    </div>
                    <div class="filter-row">
                        <a href="{{ route('catalog.listing') }}" class="btn btn-light">Lihat Semua Produk</a>
                        <select id="brand">
                            <option value="">Semua Merek</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="products-grid" class="product-grid"></div>
                <div id="empty" class="empty-state" style="display:none">Produk tidak ditemukan.</div>
                <div id="pagination" class="pagination-row"></div>
            </div>
        </section>

        <section id="service">
            <div class="container">
                <div class="service-banner">
                    <div class="service-copy">
                        <h2>Service Laptop & Komputer Terpercaya</h2>
                        <p>Perbaikan laptop, komputer, upgrade perangkat, instalasi sistem, dan pengecekan kerusakan
                            oleh teknisi berpengalaman.</p>
                        <div class="service-points">
                            <span>✓ Diagnosa awal</span>
                            <span>✓ Pengerjaan transparan</span>
                            <span>✓ Garansi service</span>
                        </div>
                        @if($contacts->count())
                            <a class="btn btn-light"
                                href="https://wa.me/{{ $contacts->first()->phone }}?text={{ urlencode('Halo, saya ingin booking service laptop/komputer.') }}"
                                target="_blank">Booking Service</a>
                        @endif
                    </div>
                    <div class="service-photo"></div>
                </div>
            </div>
        </section>

        <section id="about">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2>Kenapa Belanja di {{ $namaToko }}?</h2>
                        <p>Pelayanan lengkap untuk pembelian maupun perbaikan perangkat.</p>
                    </div>
                </div>
                <div class="why-grid">
                    <div class="why-card">
                        <div class="why-icon"><i class="fa-solid fa-store"></i></div>
                        <h3>Toko Terpercaya</h3>
                        <p>Pelayanan langsung, jelas, dan berorientasi pada kebutuhan pelanggan.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                        <h3>Harga Kompetitif</h3>
                        <p>Harga terbaik dengan pilihan produk sesuai kebutuhan dan anggaran.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-icon"><i class="fa-solid fa-users"></i></div>
                        <h3>Pilihan Lengkap</h3>
                        <p>Laptop, aksesoris, komponen, perangkat elektronik, dan jasa service.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-icon"><i class="fa-solid fa-comments"></i></div>
                        <h3>Respons Cepat</h3>
                        <p>Konsultasi pembelian dan service dengan respons yang cepat.</p>
                    </div>
                </div>
            </div>
        </section>

        @if($brands->count())
            <section id="brands">
                <div class="container">
                    <div class="section-head">
                        <div>
                            <h2>Brand Pilihan</h2>
                            <p>Produk dari berbagai merek populer dan terpercaya.</p>
                        </div>
                    </div>
                    <div class="brands">
                        @foreach($brands as $brand)
                            <a href="{{ route('catalog.listing', ['brand' => $brand->id]) }}" class="brand-chip brand-chip-link" aria-label="Lihat produk merek {{ $brand->name }}">
                                @if($brand->logo)
                                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="brand-chip-logo">
                                @else
                                    {{ $brand->name }}
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section id="testimonials">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2>Testimoni Pelanggan</h2>
                        <p>Contoh testimoni yang dapat diganti dengan ulasan asli pelanggan.</p>
                    </div>
                </div>
                <div class="testimonial-grid">
                    <div class="testimonial">
                        <p>"Pelayanannya ramah, penjelasan produknya jelas, dan laptop sesuai kebutuhan saya."</p>
                        <div class="person">
                            <div class="avatar">RP</div>
                            <div><strong>Rizky Pratama</strong><span>Pelanggan laptop</span></div>
                        </div>
                    </div>
                    <div class="testimonial">
                        <p>"Service cepat dan transparan. Kerusakan dijelaskan sebelum mulai dikerjakan."</p>
                        <div class="person">
                            <div class="avatar">SN</div>
                            <div><strong>Siti Nurhaliza</strong><span>Pelanggan service</span></div>
                        </div>
                    </div>
                    <div class="testimonial">
                        <p>"Pilihan aksesoris cukup lengkap dan harganya masih masuk akal."</p>
                        <div class="person">
                            <div class="avatar">DK</div>
                            <div><strong>Dedi Kurniawan</strong><span>Pelanggan aksesoris</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact">
            <div class="container">
                <div class="contact-bar">
                    <div class="contact-item">
                        <div><i class="fa-solid fa-location-dot"></i></div>
                        <div><strong>Lokasi Toko</strong><span>{{ $alamat }}</span></div>
                    </div>
                    <div class="contact-item">
                        <div><i class="fa-solid fa-clock"></i></div>
                        <div><strong>Jam Operasional</strong><span>{{ $jamBuka }}</span></div>
                    </div>
                    <div class="contact-item">
                        <div><i class="fa-brands fa-whatsapp"></i></div>
                        <div><strong>WhatsApp</strong><span>
                                @foreach($contacts as $contact)
                                    {{ $contact->label }}@if(!$loop->last), @endif
                                @endforeach
                            </span></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        const isCustomerAuthed = @json(Auth::guard('customers')->check());

        $(document).ready(function () {
            $('#brand').select2({ placeholder: "Semua Merek", allowClear: true });

            $('#brand').on('change', function () {
                currentPage = 1;
                loadProducts();
            });

            const globalSearch = document.getElementById('global-search');
            if (globalSearch && globalSearch.value) {
                searchEl.value = globalSearch.value;
            }

            loadProducts();
        });

        let timeout = null;
        let currentPage = 1;
        let activeCategory = '';
        const searchEl = { value: (new URLSearchParams(window.location.search)).get('search') || '' };
        const brandEl = document.getElementById('brand');
        const container = document.getElementById('products-grid');
        const emptyEl = document.getElementById('empty');

        async function loadProducts() {
            container.innerHTML = skeleton();
            emptyEl.style.display = 'none';

            const params = new URLSearchParams({
                search: searchEl.value,
                category: activeCategory,
                brand: brandEl.value,
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
            window.applyScrollReveal?.(container);
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
            document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
        }

        function card(p) {
            const outOfStock = Number(p.stock) <= 0;

            const imageHtml = p.image
                ? `<img src="/storage/${p.image}" alt="${p.name}" loading="lazy" decoding="async">`
                : `<i class="fa-solid fa-image" style="font-size:36px;color:#CBD5E1;"></i>`;

            const cartBtn = outOfStock
                ? `<button type="button" class="detail-btn" disabled style="display:flex;align-items:center;justify-content:center;opacity:.5;cursor:not-allowed;" aria-label="Stok habis"><i class="fa-solid fa-cart-plus"></i></button>`
                : (isCustomerAuthed
                    ? `<button type="button" class="detail-btn add-cart-btn" data-product-id="${p.id}" style="display:flex;align-items:center;justify-content:center;" aria-label="Tambah ke keranjang"><i class="fa-solid fa-cart-plus"></i></button>`
                    : `<a href="/akun/login?redirect=${encodeURIComponent(p.url)}" class="detail-btn" style="display:flex;align-items:center;justify-content:center;" aria-label="Tambah ke keranjang"><i class="fa-solid fa-cart-plus"></i></a>`);

            const hasDiscount = p.strike_price && Number(p.strike_price) > Number(p.price);
            const strikeHtml = hasDiscount
                ? `<span style="text-decoration:line-through;color:#98A2B3;font-size:11.5px;font-weight:500;display:block;">${formatPrice(p.strike_price)}</span>`
                : '';

            return `
            <article class="product-card ${outOfStock ? 'out-of-stock' : ''}">
                <a href="${p.url}" style="display:block;">
                    <div class="product-image">
                        ${imageHtml}
                        ${outOfStock ? '<span class="out-of-stock-badge">Habis</span>' : ''}
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

        function skeleton() {
            return Array(8).fill(`<div class="skeleton"></div>`).join('');
        }
    </script>
@endpush
