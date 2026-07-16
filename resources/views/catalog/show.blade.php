@extends('layouts.catalog')

@section('title', $product->name . ' | ' . ($settings['nama_toko'] ?? 'Barokah Computer') . ' Subang')
@section('meta_description', $product->name . ' harga Rp ' . number_format($product->selling_price, 0, ',', '.') . ' - Jual ' . $product->name . ' di ' . ($settings['nama_toko'] ?? 'Barokah Computer') . ', toko komputer Subang. ' . \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 100))
@if ($product->image)
    @section('og_image', asset('storage/' . $product->image))
@endif

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
            min-width: 0;
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

        .rating-summary {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .rating-stars {
            color: #F79009;
            font-size: 13px;
        }

        .rating-stars .rating-star-empty {
            color: #D0D5DD;
        }

        .rating-count {
            color: var(--muted);
        }

        .reviews-section {
            padding: 10px 0 30px;
        }

        .reviews-summary-card {
            display: flex;
            align-items: center;
            gap: 24px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 20px;
        }

        .reviews-summary-score {
            text-align: center;
            flex-shrink: 0;
        }

        .reviews-summary-score strong {
            font-size: 36px;
            display: block;
            color: var(--primary);
        }

        .reviews-summary-score .rating-stars {
            font-size: 16px;
        }

        .reviews-summary-score span.count {
            font-size: 12px;
            color: var(--muted);
        }

        .review-item {
            border-top: 1px solid var(--line);
            padding: 16px 0;
        }

        .review-item:first-child {
            border-top: 0;
            padding-top: 0;
        }

        .review-item-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .review-item-name {
            font-weight: 700;
            font-size: 13.5px;
        }

        .review-item-date {
            font-size: 11.5px;
            color: var(--muted);
        }

        .review-item .rating-stars {
            font-size: 12px;
            margin-bottom: 6px;
        }

        .review-item p {
            font-size: 13px;
            color: #475467;
        }

        .reviews-empty {
            color: var(--muted);
            font-size: 13px;
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

        .buy-row {
            display: flex;
            gap: 12px;
            margin-top: 26px;
            flex-wrap: wrap;
        }

        .btn-buy-now {
            background: var(--primary);
            color: #fff;
            flex: 1;
            min-width: 180px;
        }

        .btn-buy-now:hover {
            background: var(--primary-dark);
        }

        .btn-add-cart {
            background: #fff;
            border-color: var(--primary);
            color: var(--primary);
            flex: 1;
            min-width: 180px;
        }

        .btn-add-cart:hover {
            background: var(--primary-soft);
        }

        .cta-row {
            display: flex;
            gap: 12px;
            margin-top: 14px;
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

        .btn-share, .btn-favorite {
            flex: 1;
            min-width: 150px;
            background: #fff;
            border: 1px solid var(--line);
            color: var(--text, #1D2939);
            cursor: pointer;
        }

        .btn-favorite.active {
            border-color: #E11D48;
            color: #E11D48;
            background: #FEF2F4;
        }

        .btn-favorite.active i {
            font-weight: 900;
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
            min-width: 0;
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

            .reviews-summary-card {
                flex-direction: column;
                align-items: stretch;
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
    @php
        $isSold = $product->status === 'sold' || $product->stock <= 0;
    @endphp

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
                        <span class="badge-pill">
                            @if($product->category?->icon)
                                <i class="{{ $product->category->icon }}"></i>
                            @endif
                            {{ $product->category->name ?? '-' }}
                        </span>
                        <span class="badge-pill condition">{{ $product->condition === 'used' ? 'Bekas' : 'Baru' }}</span>
                        @if ($isSold)
                            <span class="badge-pill stock"><i class="fa-solid fa-tag"></i> Sold</span>
                        @else
                            <span class="badge-pill stock"><i class="fa-solid fa-circle-check"></i> Tersedia</span>
                        @endif
                    </div>

                    <h1 class="product-title">{{ $product->name }}</h1>
                    <p class="product-code">Kode Produk: {{ $product->product_code }}</p>
                    @if ($product->reviewsCount() > 0)
                        <div class="rating-summary">
                            <span class="rating-stars">
                                @for ($s = 1; $s <= 5; $s++)
                                    <i class="fa-solid fa-star{{ $s > round($product->averageRating()) ? ' rating-star-empty' : '' }}"></i>
                                @endfor
                            </span>
                            <strong>{{ number_format($product->averageRating(), 1) }}</strong>
                            <span class="rating-count">({{ $product->reviewsCount() }} ulasan)</span>
                        </div>
                    @endif
                    @if ($product->strike_price && $product->strike_price > $product->selling_price)
                        <div style="text-decoration:line-through;color:#98A2B3;font-size:14px;font-weight:600;">
                            Rp {{ number_format($product->strike_price, 0, ',', '.') }}
                        </div>
                    @endif
                    <div class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>

                    <div class="spec-row">
                        <span>Kategori</span>
                        <span>{{ $product->category->name ?? '-' }}</span>
                    </div>
                    <div class="spec-row">
                        <span>Merek</span>
                        <span>
                            @if($product->brand?->logo)
                                <img src="{{ asset('storage/' . $product->brand->logo) }}" alt="{{ $product->brand->name }}" style="height:18px;vertical-align:middle;object-fit:contain;margin-right:4px;">
                            @endif
                            {{ $product->brand->name ?? '-' }}
                        </span>
                    </div>
                    <div class="spec-row">
                        <span>Kondisi</span>
                        <span>{{ $product->condition === 'used' ? 'Bekas' : 'Baru' }}</span>
                    </div>
                    <div class="spec-row">
                        <span>Stok</span>
                        <span>{{ $isSold ? 'Sold' : $product->stock . ' unit' }}</span>
                    </div>

                    <div class="info-divider"></div>

                    <div>
                        <div class="desc-title">Deskripsi Produk</div>
                        <div class="desc-body">
                            {!! $product->description ?: '<span style="font-style:italic;color:#98A2B3;">Deskripsi produk belum tersedia.</span>' !!}
                        </div>
                    </div>

                    <div class="buy-row">
                        @if ($isSold)
                            <button type="button" class="btn btn-buy-now" style="flex:1;min-width:180px;opacity:.5;cursor:not-allowed;" disabled>
                                <i class="fa-solid fa-ban"></i> Sold
                            </button>
                        @elseif (auth('customers')->check())
                            <form action="{{ route('checkout.buy') }}" method="POST" style="flex:1;min-width:180px;">
                                @csrf
                                <input type="hidden" name="product_slug" value="{{ $product->slug }}">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="btn btn-buy-now" style="width:100%;">
                                    <i class="fa-solid fa-bag-shopping"></i> Beli Sekarang
                                </button>
                            </form>
                            <form action="{{ route('cart.add') }}" method="POST" style="flex:1;min-width:180px;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-add-cart" style="width:100%;">
                                    <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
                                </button>
                            </form>
                        @else
                            <a href="{{ route('customer.login', ['redirect' => url()->current()]) }}"
                                class="btn btn-buy-now">
                                <i class="fa-solid fa-bag-shopping"></i> Beli Sekarang
                            </a>
                            <a href="{{ route('customer.login', ['redirect' => url()->current()]) }}"
                                class="btn btn-add-cart">
                                <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
                            </a>
                        @endif
                    </div>

                    <div class="cta-row">
                        <button type="button" class="btn btn-share" onclick="shareProduct()">
                            <i class="fa-solid fa-share-nodes"></i> Bagikan
                        </button>

                        @auth('customers')
                            <button type="button" class="btn btn-favorite {{ $isFavorited ? 'active' : '' }}" id="favoriteBtn"
                                data-product-id="{{ $product->id }}" onclick="toggleFavorite(this)">
                                <i class="fa-solid fa-heart"></i> <span id="favoriteBtnLabel">{{ $isFavorited ? 'Favorit' : 'Favoritkan' }}</span>
                            </button>
                        @else
                            <a href="{{ route('customer.login', ['redirect' => url()->current()]) }}" class="btn btn-favorite">
                                <i class="fa-solid fa-heart"></i> Favoritkan
                            </a>
                        @endauth

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

    <section class="reviews-section">
        <div class="container">
            <div class="section-head">
                <h2>Ulasan Produk</h2>
            </div>

            @if ($product->reviewsCount() > 0)
                <div class="reviews-summary-card">
                    <div class="reviews-summary-score">
                        <strong>{{ number_format($product->averageRating(), 1) }}</strong>
                        <div class="rating-stars">
                            @for ($s = 1; $s <= 5; $s++)
                                <i class="fa-solid fa-star{{ $s > round($product->averageRating()) ? ' rating-star-empty' : '' }}"></i>
                            @endfor
                        </div>
                        <span class="count">{{ $product->reviewsCount() }} ulasan</span>
                    </div>
                    <div style="flex:1;">
                        @foreach ($product->reviews->take(10) as $review)
                            <div class="review-item">
                                <div class="review-item-head">
                                    <span class="review-item-name">{{ $review->customer->name ?? 'Pelanggan' }}</span>
                                    <span class="review-item-date">{{ $review->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <div class="rating-stars">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <i class="fa-solid fa-star{{ $s > $review->rating ? ' rating-star-empty' : '' }}"></i>
                                    @endfor
                                </div>
                                @if ($review->comment)
                                    <p>{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="reviews-empty">Belum ada ulasan untuk produk ini.</p>
            @endif
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
                        <a href="{{ route('catalog.show', $r->slug) }}" class="product-card">
                            <div class="product-image">
                                @if($r->image)
                                    <img src="{{ asset('storage/' . $r->image) }}" alt="{{ $r->name }}" loading="lazy" decoding="async">
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

        function shareProduct() {
            const shareData = {
                title: @json($product->name),
                text: 'Lihat produk ini: {{ $product->name }}',
                url: window.location.href,
            };

            if (navigator.share) {
                navigator.share(shareData).catch(() => {});
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    Swal.fire({ icon: 'success', title: 'Link Disalin', text: 'Link produk telah disalin ke clipboard.', timer: 1600, showConfirmButton: false });
                }).catch(() => {
                    Swal.fire({ icon: 'info', title: 'Bagikan', text: window.location.href });
                });
            }
        }

        function toggleFavorite(btn) {
            const productId = btn.dataset.productId;
            btn.disabled = true;

            fetch(`/akun/produk/${productId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
                .then(res => res.json())
                .then(data => {
                    btn.classList.toggle('active', data.favorited);
                    document.getElementById('favoriteBtnLabel').innerText = data.favorited ? 'Favorit' : 'Favoritkan';
                })
                .finally(() => { btn.disabled = false; });
        }
    </script>
@endpush
