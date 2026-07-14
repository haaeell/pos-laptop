@extends('layouts.catalog')

@section('title', 'Favorit Saya | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .fav-section {
            padding: 40px 0 60px;
        }

        .fav-section h1 {
            font-size: 24px;
            margin-bottom: 22px;
        }

        .fav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 18px;
        }

        .fav-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            position: relative;
        }

        .fav-card-image {
            aspect-ratio: 1/1;
            background: #F2F4F7;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fav-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fav-card-body {
            padding: 14px;
        }

        .fav-card-body h3 {
            font-size: 13.5px;
            margin-bottom: 6px;
            min-height: 34px;
        }

        .fav-card-body .price {
            font-weight: 800;
            color: var(--primary);
            font-size: 14px;
        }

        .fav-remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 0;
            background: rgba(255,255,255,.92);
            color: var(--danger);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fav-empty {
            text-align: center;
            padding: 60px 0;
            color: var(--muted);
        }
    </style>
@endsection

@section('content')
    <section class="fav-section">
        <div class="container">
            <h1><i class="fa-solid fa-heart"></i> Favorit Saya</h1>

            @if ($products->isEmpty())
                <div class="fav-empty">
                    <p>Belum ada produk favorit. Yuk jelajahi produk dan simpan yang kamu suka.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary" style="margin-top:14px;display:inline-block;">Lihat Produk</a>
                </div>
            @else
                <div class="fav-grid" id="favGrid">
                    @foreach ($products as $product)
                        <div class="fav-card" data-product-id="{{ $product->id }}">
                            <button type="button" class="fav-remove-btn" onclick="removeFavorite({{ $product->id }}, this)" aria-label="Hapus dari favorit">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                            <a href="{{ route('catalog.show', $product->id) }}">
                                <div class="fav-card-image">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <i class="fa-solid fa-image" style="font-size:32px;color:#CBD5E1;"></i>
                                    @endif
                                </div>
                                <div class="fav-card-body">
                                    <h3>{{ $product->name }}</h3>
                                    <div class="price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function removeFavorite(productId, btn) {
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
                    if (!data.favorited) {
                        btn.closest('.fav-card').remove();
                    }
                });
        }
    </script>
@endpush
