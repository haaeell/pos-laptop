@extends('layouts.catalog')

@section('title', 'Pesanan Saya | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .orders-section {
            padding: 40px 0 60px;
        }

        .orders-section h1 {
            font-size: 24px;
            margin-bottom: 22px;
        }

        .order-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .order-card .num {
            font-weight: 700;
            font-size: 13.5px;
            color: var(--primary);
        }

        .order-card .date {
            font-size: 11.5px;
            color: var(--muted);
        }

        .order-status-pill {
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-pending_payment {
            background: #FEF0C7;
            color: #B54708;
        }

        .status-paid,
        .status-processing {
            background: #FEF0C7;
            color: #B54708;
        }

        .status-shipped {
            background: #D1E9FF;
            color: #175CD3;
        }

        .status-completed {
            background: #D1FADF;
            color: #027A48;
        }

        .status-cancelled,
        .status-expired,
        .status-failed {
            background: #FEE4E2;
            color: #B42318;
        }

        .orders-empty {
            text-align: center;
            padding: 70px 0;
            color: var(--muted);
        }

        .orders-empty i {
            font-size: 40px;
            color: #CBD5E1;
            margin-bottom: 14px;
            display: block;
        }

        .order-tabs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            margin-bottom: 20px;
            -webkit-overflow-scrolling: touch;
        }

        .order-tabs::-webkit-scrollbar {
            display: none;
        }

        .order-tab {
            flex-shrink: 0;
            padding: 9px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--muted);
            white-space: nowrap;
        }

        .order-tab.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        @media(max-width:480px) {
            .orders-section {
                padding: 20px 0 40px;
            }

            .orders-section h1 {
                font-size: 19px;
            }

            .order-card {
                padding: 13px 14px;
                gap: 8px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="orders-section">
        <div class="container">
            <h1><i class="fa-solid fa-box"></i> Pesanan Saya</h1>

            <div class="order-tabs">
                <a href="{{ route('customer.orders.index') }}" class="order-tab {{ !$activeTab ? 'active' : '' }}">Semua</a>
                <a href="{{ route('customer.orders.index', ['status' => 'pending_payment']) }}" class="order-tab {{ $activeTab === 'pending_payment' ? 'active' : '' }}">Menunggu Pembayaran</a>
                <a href="{{ route('customer.orders.index', ['status' => 'processing']) }}" class="order-tab {{ $activeTab === 'processing' ? 'active' : '' }}">Diproses</a>
                <a href="{{ route('customer.orders.index', ['status' => 'completed']) }}" class="order-tab {{ $activeTab === 'completed' ? 'active' : '' }}">Selesai</a>
                <a href="{{ route('customer.orders.index', ['status' => 'cancelled']) }}" class="order-tab {{ $activeTab === 'cancelled' ? 'active' : '' }}">Dibatalkan</a>
            </div>

            @if ($orders->isEmpty())
                <div class="orders-empty">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Anda belum memiliki pesanan.</p>
                </div>
            @else
                @foreach ($orders as $order)
                    <a href="{{ route('customer.orders.show', $order) }}" class="order-card">
                        <div>
                            <div class="num">{{ $order->order_number }}</div>
                            <div class="date">{{ $order->created_at->translatedFormat('d M Y H:i') }}</div>
                        </div>
                        <div>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
                        <span class="order-status-pill status-{{ $order->status }}">{{ $order->status_label }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    </section>
@endsection
