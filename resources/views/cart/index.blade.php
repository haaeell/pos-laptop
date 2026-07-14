@extends('layouts.catalog')

@section('title', 'Keranjang Belanja | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .cart-section {
            padding: 40px 0 60px;
        }

        .cart-section h1 {
            font-size: 24px;
            margin-bottom: 22px;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }

        .cart-list {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border-bottom: 1px solid var(--line);
        }

        .cart-item:last-child {
            border-bottom: 0;
        }

        .cart-thumb {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            background: #F6F8FB;
            overflow: hidden;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            color: #CBD5E1;
        }

        .cart-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-info {
            flex: 1;
            min-width: 0;
        }

        .cart-info strong {
            display: block;
            font-size: 13.5px;
            margin-bottom: 4px;
        }

        .cart-info span {
            font-size: 12.5px;
            color: var(--primary);
            font-weight: 700;
        }

        .cart-qty {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cart-qty button {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            cursor: pointer;
            font-weight: 700;
        }

        .cart-qty form {
            display: inline;
        }

        .cart-qty span {
            width: 26px;
            text-align: center;
            font-weight: 700;
            font-size: 13px;
        }

        .cart-remove {
            border: 0;
            background: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 14px;
            padding: 6px;
        }

        .cart-summary {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px;
            position: sticky;
            top: 90px;
        }

        .cart-summary h3 {
            font-size: 15px;
            margin-bottom: 16px;
        }

        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 8px 0;
            border-bottom: 1px dashed var(--line);
        }

        .cart-summary-row.total {
            border-bottom: 0;
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
            padding-top: 14px;
        }

        .cart-empty {
            text-align: center;
            padding: 70px 0;
            color: var(--muted);
        }

        .cart-empty i {
            font-size: 40px;
            color: #CBD5E1;
            margin-bottom: 14px;
            display: block;
        }

        @media(max-width:768px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }

            .cart-summary {
                position: static;
            }
        }
    </style>
@endsection

@section('content')
    <section class="cart-section">
        <div class="container">
            <h1><i class="fa-solid fa-cart-shopping"></i> Keranjang Belanja</h1>

            @if ($items->isEmpty())
                <div class="cart-empty">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <p>Keranjang Anda masih kosong.</p>
                    <a href="{{ url('/#products') }}" class="btn btn-primary" style="margin-top:16px;">Mulai
                        Belanja</a>
                </div>
            @else
                <div class="cart-layout">
                    <div class="cart-list">
                        @foreach ($items as $item)
                            <div class="cart-item">
                                <div class="cart-thumb">
                                    @if ($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="">
                                    @else
                                        <i class="fa-solid fa-image"></i>
                                    @endif
                                </div>

                                <div class="cart-info">
                                    <strong>{{ $item->product->name }}</strong>
                                    <span>Rp {{ number_format($item->product->selling_price, 0, ',', '.') }}</span>
                                </div>

                                <div class="cart-qty">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="qty" value="{{ max(1, $item->qty - 1) }}">
                                        <button type="submit" {{ $item->qty <= 1 ? 'disabled' : '' }}>−</button>
                                    </form>
                                    <span>{{ $item->qty }}</span>
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="qty" value="{{ $item->qty + 1 }}">
                                        <button type="submit" {{ $item->qty >= $item->product->stock ? 'disabled' : '' }}>+</button>
                                    </form>
                                </div>

                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cart-remove" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <div class="cart-summary">
                        <h3>Ringkasan Belanja</h3>
                        @php $total = $items->sum(fn($i) => $i->qty * $i->product->selling_price); @endphp
                        <div class="cart-summary-row">
                            <span>Total Item</span>
                            <span>{{ $items->sum('qty') }}</span>
                        </div>
                        <div class="cart-summary-row total">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('checkout.create') }}" class="btn btn-primary"
                            style="width:100%;margin-top:14px;">
                            Checkout <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
