@extends('layouts.catalog')

@section('title', 'Pembayaran | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .pay-section {
            padding: 60px 0;
            min-height: calc(100vh - 400px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pay-card {
            width: min(460px, 100%);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 36px 32px;
            text-align: center;
        }

        .pay-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 28px;
            margin: 0 auto 18px;
        }

        .pay-card h1 {
            font-size: 20px;
            margin-bottom: 6px;
        }

        .pay-card p {
            color: var(--muted);
            font-size: 13.5px;
            margin-bottom: 22px;
        }

        .pay-total {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 22px;
        }

        .pay-order-number {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 22px;
        }

        .auth-alert {
            background: #FEF3F2;
            border: 1px solid #FECDCA;
            color: #B42318;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            margin-bottom: 18px;
            text-align: left;
        }

        .pay-countdown {
            font-size: 13px;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 22px;
        }

        .pay-countdown.pending {
            background: #FFF6E5;
            border: 1px solid #FFE1A8;
            color: #B54708;
        }

        .pay-countdown.expired {
            background: #FEF3F2;
            border: 1px solid #FECDCA;
            color: #B42318;
        }

        @media(max-width:480px) {
            .pay-section {
                padding: 24px 16px;
            }

            .pay-card {
                padding: 26px 20px;
                border-radius: 16px;
            }

            .pay-total {
                font-size: 22px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="pay-section">
        <div class="pay-card">
            <div class="pay-icon"><i class="fa-solid fa-receipt"></i></div>
            <h1>Pesanan Berhasil Dibuat</h1>
            <p class="pay-order-number">No. Pesanan: <strong>{{ $order->order_number }}</strong></p>
            <div class="pay-total">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>

            @if ($order->status === 'pending_payment' && $order->expires_at)
                <div class="pay-countdown pending" id="payCountdown" data-expires-at="{{ $order->expires_at->toIso8601String() }}">
                    Selesaikan pembayaran dalam <span id="payCountdownTime">30:00</span>
                </div>
            @elseif (in_array($order->status, ['expired', 'cancelled', 'failed']))
                <div class="pay-countdown expired">
                    Pesanan ini telah {{ $order->status === 'expired' ? 'kedaluwarsa' : 'dibatalkan' }}.
                </div>
            @endif

            @if ($order->status === 'pending_payment')
                @if (!$midtransConfigured)
                    <div class="auth-alert">
                        Pembayaran online belum diaktifkan oleh admin toko. Silakan hubungi kami via WhatsApp untuk
                        menyelesaikan pesanan ini.
                    </div>
                @elseif ($order->snap_token)
                    <button type="button" id="payButton" class="btn btn-primary" style="width:100%;">
                        <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                    </button>
                @else
                    <p class="auth-alert">
                        Pesanan sudah dibuat, tetapi token pembayaran Midtrans belum berhasil dibuat. Silakan hubungi
                        admin toko dan sebutkan nomor pesanan ini.
                    </p>
                @endif
            @endif

            <p style="margin-top:20px;">
                <a href="{{ route('customer.orders.show', $order) }}" style="font-size:13px;color:var(--primary);font-weight:700;">
                    Lihat Detail Pesanan →
                </a>
            </p>
        </div>
    </section>
@endsection

@push('scripts')
    @if ($order->status === 'pending_payment' && $order->expires_at)
        <script>
            (function () {
                const countdownEl = document.getElementById('payCountdown');
                const timeEl = document.getElementById('payCountdownTime');
                const expiresAt = new Date(countdownEl.dataset.expiresAt).getTime();
                let alerted = false;

                const timer = setInterval(function () {
                    const diff = expiresAt - Date.now();

                    if (diff <= 0) {
                        clearInterval(timer);
                        countdownEl.classList.remove('pending');
                        countdownEl.classList.add('expired');
                        countdownEl.innerText = 'Waktu pembayaran habis, pesanan otomatis dibatalkan.';
                        const payButton = document.getElementById('payButton');
                        if (payButton) payButton.remove();
                        if (!alerted) {
                            alerted = true;
                            Swal.fire({
                                icon: 'error',
                                title: 'Waktu Pembayaran Habis',
                                text: 'Pesanan ini otomatis dibatalkan karena melebihi batas waktu pembayaran.',
                                confirmButtonColor: '#2563eb',
                            }).then(() => window.location.reload());
                        }
                        return;
                    }

                    const minutes = Math.floor(diff / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000);
                    timeEl.innerText = `${minutes}:${String(seconds).padStart(2, '0')}`;
                }, 1000);
            })();
        </script>
    @endif

    @if ($midtransConfigured && $order->snap_token && $order->status === 'pending_payment')
        <script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ $clientKey }}"></script>
        <script>
            document.getElementById('payButton').addEventListener('click', function () {
                snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function () {
                        window.location.href = '{{ route('customer.orders.show', $order) }}';
                    },
                    onPending: function () {
                        window.location.href = '{{ route('customer.orders.show', $order) }}';
                    },
                    onError: function () {
                        Swal.fire({ icon: 'error', title: 'Pembayaran Gagal', text: 'Silakan coba lagi.' });
                    },
                });
            });
        </script>
    @endif
@endpush
