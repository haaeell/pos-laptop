@extends('layouts.catalog')

@section('title', $product->name . ' | ' . ($settings['nama_toko'] ?? 'Barokah Computer'))
@section('meta_description', $product->name . ' - ' . ($settings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
        }

        .breadcrumb {
            padding: 18px 0 0;
            font-size: 12px;
            color: var(--muted);
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .breadcrumb a {
            color: var(--muted);
            font-weight: 600;
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .breadcrumb span.current {
            color: var(--text);
            font-weight: 600;
        }

        .detail-section {
            padding: 24px 0 54px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .gallery {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 20px;
        }

        .gallery-main {
            height: 380px;
            border-radius: 16px;
            overflow: hidden;
            background: #F6F8FB;
        }

        .gallery-main .swiper-slide {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F6F8FB;
        }

        .gallery-main img {
            max-height: 100%;
            width: auto;
            object-fit: contain;
        }

        .gallery-thumbs {
            margin-top: 12px;
            height: 76px;
        }

        .gallery-thumbs .swiper-slide {
            width: 76px;
            height: 76px;
            border-radius: 10px;
            overflow: hidden;
            opacity: .5;
            cursor: pointer;
            border: 2px solid transparent;
            background: #F6F8FB;
        }

        .gallery-thumbs .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-thumbs .swiper-slide-thumb-active {
            opacity: 1;
            border-color: var(--primary);
        }

        .no-image {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: #CBD5E1;
            font-size: 64px;
        }

        .info-panel {
            display: flex;
            flex-direction: column;
        }

        .badges {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .badge-pill {
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary);
        }

        .badge-pill.condition {
            background: #F1F5F9;
            color: #475467;
        }

        .badge-pill.stock {
            background: #EAFBF2;
            color: var(--success);
        }

        .product-title {
            font-size: 28px;
            letter-spacing: -.6px;
            margin-bottom: 8px;
        }

        .product-code {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 18px;
        }

        .product-price {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 22px;
        }

        .info-divider {
            border-top: 1px solid var(--line);
            margin: 20px 0;
        }

        .spec-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 10px 0;
            border-bottom: 1px dashed var(--line);
        }

        .spec-row span:first-child {
            color: var(--muted);
        }

        .spec-row span:last-child {
            font-weight: 600;
        }

        .desc-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .desc-body {
            font-size: 13px;
            color: #475467;
            line-height: 1.7;
        }

        .cta-row {
            display: flex;
            gap: 12px;
            margin-top: 26px;
            flex-wrap: wrap;
        }

        .btn-wa {
            background: var(--success);
            color: #fff;
            flex: 1;
            min-width: 180px;
        }

        .btn-wa:hover {
            background: #0e9c5a;
        }

        .trust-row {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--muted);
        }

        .trust-item i {
            color: var(--primary);
        }

        .related-section {
            padding: 10px 0 54px;
        }

        .related-grid {
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
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .product-image {
            height: 160px;
            border-radius: 12px;
            background: #F6F8FB;
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-card h3 {
            font-size: 13px;
            margin: 12px 0 8px;
            min-height: 36px;
        }

        .product-card .price {
            color: var(--primary);
            font-weight: 800;
            font-size: 14px;
        }

        @media(max-width:860px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .related-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .gallery-main {
                height: 300px;
            }

            .product-title {
                font-size: 22px;
            }

            .product-price {
                font-size: 26px;
            }
        }

        @media(max-width:480px) {
            .related-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .cta-row {
                flex-direction: column;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <span>/</span>
            <a href="{{ url('/') }}">{{ $product->category->name ?? 'Produk' }}</a>
            <span>/</span>
            <span class="current">{{ $product->name }}</span>
        </div>
    </div>

    <section class="detail-section">
        <div class="container">
            <div class="detail-grid">
                <div class="gallery">
                    @php $allImages = collect([$product->image])->filter()->merge($product->images->pluck('image')); @endphp
                    @if($allImages->count())
                        <div class="swiper gallery-main">
                            <div class="swiper-wrapper">
                                @foreach($allImages as $img)
                                    <div class="swiper-slide">
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if($allImages->count() > 1)
                            <div class="swiper gallery-thumbs">
                                <div class="swiper-wrapper">
                                    @foreach($allImages as $img)
                                        <div class="swiper-slide">
                                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="gallery-main no-image"><i class="fa-solid fa-image"></i></div>
                    @endif
                </div>

                <div class="info-panel">
                    <div class="badges">
                        <span class="badge-pill">{{ $product->category->name ?? '-' }}</span>
                        <span class="badge-pill condition">{{ $product->condition === 'used' ? 'Bekas' : 'Baru' }}</span>
                        <span class="badge-pill stock"><i class="fa-solid fa-circle-check"></i> Tersedia</span>
                    </div>

                    <h1 class="product-title">{{ $product->name }}</h1>
                    <p class="product-code">Kode Produk: {{ $product->product_code }}</p>
                    <div class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>

                    <div class="spec-row">
                        <span>Kategori</span>
                        <span>{{ $product->category->name ?? '-' }}</span>
                    </div>
                    <div class="spec-row">
                        <span>Merek</span>
                        <span>{{ $product->brand->name ?? '-' }}</span>
                    </div>
                    <div class="spec-row">
                        <span>Kondisi</span>
                        <span>{{ $product->condition === 'used' ? 'Bekas' : 'Baru' }}</span>
                    </div>

                    <div class="info-divider"></div>

                    <div>
                        <div class="desc-title">Deskripsi Produk</div>
                        <div class="desc-body">
                            {!! $product->description ?: '<span style="font-style:italic;color:#98A2B3;">Deskripsi produk belum tersedia.</span>' !!}
                        </div>
                    </div>

                    <div class="cta-row">
                        @foreach($contacts as $contact)
                            @php
                                $text = urlencode(
                                    ($contact->whatsapp_text ?? 'Halo, saya tertarik dengan produk berikut:') .
                                    "\n\nProduk: {$product->name}\nHarga: Rp " . number_format($product->selling_price, 0, ',', '.')
                                );
                            @endphp
                            <a href="https://wa.me/{{ $contact->phone }}?text={{ $text }}" target="_blank" class="btn btn-wa">
                                <i class="fa-brands fa-whatsapp"></i> {{ $contact->label }}
                            </a>
                        @endforeach
                        <a href="{{ url('/') }}" class="btn btn-light"><i class="fa-solid fa-list"></i> Lihat Produk Lain</a>
                    </div>

                    <div class="trust-row">
                        <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> Garansi Resmi</div>
                        <div class="trust-item"><i class="fa-solid fa-star"></i> Produk Original</div>
                        <div class="trust-item"><i class="fa-solid fa-truck-fast"></i> Pengiriman Aman</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($related->count())
        <section class="related-section">
            <div class="container">
                <div class="section-head">
                    <h2>Produk Terkait</h2>
                </div>
                <div class="related-grid">
                    @foreach($related as $r)
                        <a href="{{ route('catalog.show', $r->id) }}" class="product-card">
                            <div class="product-image">
                                @if($r->image)
                                    <img src="{{ asset('storage/' . $r->image) }}" alt="{{ $r->name }}">
                                @else
                                    <i class="fa-solid fa-image" style="font-size:32px;color:#CBD5E1;"></i>
                                @endif
                            </div>
                            <h3>{{ $r->name }}</h3>
                            <div class="price">Rp {{ number_format($r->selling_price, 0, ',', '.') }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const thumbSwiper = new Swiper('.gallery-thumbs', {
            spaceBetween: 10,
            slidesPerView: 'auto',
            freeMode: true,
            watchSlidesProgress: true,
        });

        const mainSwiper = new Swiper('.gallery-main', {
            spaceBetween: 10,
            thumbs: { swiper: thumbSwiper },
        });
    </script>
@endpush
