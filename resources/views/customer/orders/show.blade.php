@extends('layouts.catalog')

@section('title', $order->order_number . ' | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .order-detail-section {
            padding: 40px 0 60px;
        }

        .order-detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
        }

        .order-detail-header h1 {
            font-size: 22px;
        }

        .order-detail-header p {
            font-size: 12.5px;
            color: var(--muted);
        }

        .order-status-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pending_payment,
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

        .order-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }

        .order-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .order-card h3 {
            font-size: 14.5px;
            margin-bottom: 14px;
        }

        .order-item-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 8px 0;
            border-bottom: 1px dashed var(--line);
        }

        .order-item-row:last-child {
            border-bottom: 0;
        }

        .order-item-row-media {
            align-items: center;
            gap: 10px;
        }

        .order-item-thumb {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: #F2F4F7;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            color: #CBD5E1;
        }

        .order-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .order-item-name {
            flex: 1;
        }

        .review-btn {
            border: 1px solid var(--primary);
            color: var(--primary);
            background: var(--primary-soft);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            margin-top: 4px;
            cursor: pointer;
        }

        .review-done-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            color: #B54708;
            background: #FEF0C7;
            padding: 4px 10px;
            border-radius: 999px;
            margin-top: 4px;
        }

        .review-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(16, 24, 40, .5);
            z-index: 150;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .review-modal-overlay.open {
            display: flex;
        }

        .review-modal {
            background: #fff;
            border-radius: 18px;
            width: min(420px, 100%);
            padding: 24px;
        }

        .review-modal h3 {
            font-size: 16px;
            margin-bottom: 4px;
        }

        .review-modal p {
            font-size: 12.5px;
            color: var(--muted);
            margin-bottom: 16px;
        }

        .review-star-row {
            display: flex;
            gap: 8px;
            font-size: 30px;
            color: #D0D5DD;
            margin-bottom: 16px;
            justify-content: center;
        }

        .review-star-row i.active {
            color: #F79009;
        }

        .review-star-row span {
            cursor: pointer;
        }

        .review-modal textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .review-modal-actions {
            display: flex;
            gap: 10px;
        }

        .timeline {
            position: relative;
            padding-left: 24px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: -24px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary);
        }

        .timeline-item::after {
            content: "";
            position: absolute;
            left: -20px;
            top: 14px;
            bottom: -6px;
            width: 1px;
            background: var(--line);
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .timeline-item strong {
            display: block;
            font-size: 13px;
        }

        .timeline-item span {
            font-size: 11.5px;
            color: var(--muted);
        }

        .address-box p {
            font-size: 13px;
            margin-bottom: 4px;
        }

        .address-box .muted {
            color: var(--muted);
            font-size: 12px;
        }

        @media(max-width:860px) {
            .order-layout {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:480px) {
            .order-detail-section {
                padding: 24px 0 40px;
            }

            .order-detail-header h1 {
                font-size: 18px;
            }

            .order-card {
                padding: 16px;
            }

            .order-item-row {
                font-size: 12px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="order-detail-section">
        <div class="container">
            <div class="order-detail-header">
                <div>
                    <h1>{{ $order->order_number }}</h1>
                    <p>{{ $order->created_at->translatedFormat('d M Y H:i') }}</p>
                </div>
                <span class="order-status-pill status-{{ $order->status }}">{{ $order->status_label }}</span>
            </div>

            <div class="order-layout">
                <div>
                    <div class="order-card">
                        <h3><i class="fa-solid fa-box"></i> Produk</h3>
                        @foreach ($order->items as $item)
                            <div class="order-item-row order-item-row-media">
                                <div class="order-item-thumb">
                                    @if ($item->product?->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}">
                                    @else
                                        <i class="fa-solid fa-image"></i>
                                    @endif
                                </div>
                                <span class="order-item-name">
                                    {{ $item->product_name }} × {{ $item->qty }}
                                    @if ($order->status === 'completed')
                                        <br>
                                        @if ($item->review)
                                            <span class="review-done-badge"><i class="fa-solid fa-star"></i> Sudah diulas</span>
                                        @else
                                            <button type="button" class="review-btn" onclick='openReviewModal({{ $item->id }}, {{ json_encode($item->product_name) }})'>
                                                <i class="fa-regular fa-star"></i> Beri Ulasan
                                            </button>
                                        @endif
                                    @endif
                                </span>
                                <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                        @if ($order->delivery_method === 'shipping' && $order->shipping_cost !== null)
                            <div class="order-item-row">
                                <span>Ongkos Kirim ({{ $order->courier_service_name }})</span>
                                <strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                            </div>
                        @elseif ($order->delivery_method === 'pickup')
                            <div class="order-item-row">
                                <span>Metode</span>
                                <strong>Pickup Sendiri</strong>
                            </div>
                        @endif
                        <div class="order-item-row" style="font-weight:800;color:var(--primary);">
                            <span>Total</span>
                            <strong>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="order-card address-box">
                        <h3><i class="fa-solid fa-location-dot"></i> {{ $order->delivery_method === 'pickup' ? 'Info Pickup' : 'Alamat Pengiriman' }}</h3>
                        @if ($order->delivery_method === 'pickup')
                            <p><strong>{{ $namaToko }}</strong></p>
                            <p>{{ $alamat }}</p>
                            <p class="muted">Pesanan diambil sendiri di toko setelah pembayaran selesai.</p>
                        @else
                            <p><strong>{{ $order->recipient_name }}</strong> ({{ $order->recipient_phone }})</p>
                            <p>{{ $order->address_detail }}</p>
                            <p class="muted">{{ $order->district }}, {{ $order->city }}, {{ $order->province }}</p>
                        @endif
                        @if ($order->notes)
                            <p class="muted">Catatan: {{ $order->notes }}</p>
                        @endif
                    </div>

                    @if ($order->delivery_method === 'shipping' && $order->hasShipment())
                        <div class="order-card">
                            <h3><i class="fa-solid fa-truck-fast"></i> Lacak Paket</h3>
                            <p style="font-size:13px;margin-bottom:4px;">
                                <strong>{{ $order->courier_service_name }}</strong>
                            </p>
                            <p style="font-size:12.5px;color:var(--muted);margin-bottom:14px;">
                                No. Resi: <strong>{{ $order->courier_waybill_id ?? '-' }}</strong>
                                @if ($order->shipment_status)
                                    &middot; Status: <strong>{{ $order->shipment_status }}</strong>
                                @endif
                            </p>

                            @if ($order->trackingHistories->isNotEmpty())
                                <div class="timeline">
                                    @foreach ($order->trackingHistories as $history)
                                        <div class="timeline-item">
                                            <strong>{{ $history->status }}</strong>
                                            <span>{{ $history->created_at->translatedFormat('d M Y H:i') }}</span>
                                            @if ($history->note)
                                                <p style="font-size:12px;margin-top:3px;">{{ $history->note }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="font-size:12.5px;color:var(--muted);">Belum ada update tracking.</p>
                            @endif
                        </div>
                    @elseif ($order->delivery_method === 'pickup')
                        <div class="order-card">
                            <h3><i class="fa-solid fa-store"></i> Ambil di Toko</h3>
                            <p style="font-size:13px;margin-bottom:4px;">
                                <strong>Pesanan siap diambil setelah status pembayaran lunas.</strong>
                            </p>
                            <p style="font-size:12.5px;color:var(--muted);margin-bottom:0;">
                                Tidak ada resi, tracking, atau pengiriman kurir untuk pesanan ini.
                            </p>
                        </div>
                    @endif

                    @if ($order->status === 'pending_payment')
                        <div class="order-card" style="display:flex;gap:10px;flex-wrap:wrap;">
                            @if ($order->snap_token)
                                <a href="{{ route('checkout.pay', $order->order_number) }}" class="btn btn-primary" style="flex:1;min-width:160px;">
                                    Lanjutkan Pembayaran
                                </a>
                            @endif
                            <button type="button" onclick="confirmCancelOrder()" class="btn btn-light" style="flex:1;min-width:160px;color:#B42318;"
                                data-cancel-url="{{ route('customer.orders.cancel', $order->order_number) }}">
                                Batalkan Pesanan
                            </button>
                        </div>
                    @endif
                </div>

                <div class="order-card">
                    <h3><i class="fa-solid fa-timeline"></i> Status Pesanan</h3>
                    <div class="timeline">
                        @foreach ($order->statusHistories as $history)
                            <div class="timeline-item">
                                <strong>{{ $history->status_label }}</strong>
                                <span>{{ $history->created_at->translatedFormat('d M Y H:i') }}</span>
                                @if ($history->note)
                                    <p style="font-size:12px;margin-top:3px;">{{ $history->note }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="review-modal-overlay" id="reviewModalOverlay">
        <div class="review-modal">
            <h3>Beri Ulasan</h3>
            <p id="reviewProductName"></p>
            <div class="review-star-row" id="reviewStarRow">
                @for ($s = 1; $s <= 5; $s++)
                    <span data-star="{{ $s }}"><i class="fa-solid fa-star"></i></span>
                @endfor
            </div>
            <textarea id="reviewComment" rows="3" placeholder="Bagaimana pengalaman Anda dengan produk ini? (opsional)"></textarea>
            <div class="review-modal-actions">
                <button type="button" class="btn btn-light" style="flex:1;" onclick="closeReviewModal()">Batal</button>
                <button type="button" class="btn btn-primary" style="flex:1;" onclick="submitReview()">Kirim Ulasan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ===== Product review modal =====
        let reviewOrderItemId = null;
        let reviewRating = 0;

        function openReviewModal(orderItemId, productName) {
            reviewOrderItemId = orderItemId;
            reviewRating = 0;
            document.getElementById('reviewProductName').innerText = productName;
            document.getElementById('reviewComment').value = '';
            renderReviewStars();
            document.getElementById('reviewModalOverlay').classList.add('open');
        }

        function closeReviewModal() {
            document.getElementById('reviewModalOverlay').classList.remove('open');
        }

        function renderReviewStars() {
            document.querySelectorAll('#reviewStarRow span').forEach(el => {
                el.querySelector('i').classList.toggle('active', Number(el.dataset.star) <= reviewRating);
            });
        }

        document.querySelectorAll('#reviewStarRow span').forEach(el => {
            el.addEventListener('click', function () {
                reviewRating = Number(this.dataset.star);
                renderReviewStars();
            });
        });

        async function submitReview() {
            if (!reviewRating) {
                Swal.fire({ icon: 'warning', title: 'Pilih Rating', text: 'Mohon pilih rating bintang terlebih dahulu.' });
                return;
            }

            showLoading();

            try {
                const res = await fetch('{{ route('customer.reviews.store', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        order_item_id: reviewOrderItemId,
                        rating: reviewRating,
                        comment: document.getElementById('reviewComment').value,
                    }),
                });

                const data = await res.json().catch(() => ({
                    message: res.status === 419
                        ? 'Sesi login berakhir. Silakan refresh halaman lalu coba lagi.'
                        : 'Server mengembalikan response tidak valid.',
                }));
                hideLoading();

                if (!res.ok) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                    return;
                }

                closeReviewModal();
                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
            } catch (err) {
                hideLoading();
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan.' });
            }
        }

        const CANCEL_REASONS = {
            'Salah pilih produk/ukuran': 'Salah pilih produk/ukuran',
            'Ingin ubah alamat pengiriman': 'Ingin ubah alamat pengiriman',
            'Menemukan harga lebih murah': 'Menemukan harga lebih murah',
            'Berubah pikiran': 'Berubah pikiran',
            'other': 'Alasan lainnya',
        };

        function confirmCancelOrder() {
            const cancelUrl = document.querySelector('[data-cancel-url]').dataset.cancelUrl;

            Swal.fire({
                icon: 'warning',
                title: 'Batalkan Pesanan?',
                input: 'select',
                inputOptions: CANCEL_REASONS,
                inputPlaceholder: 'Pilih alasan pembatalan',
                showCancelButton: true,
                confirmButtonText: 'Lanjut',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#B42318',
                inputValidator: (value) => !value && 'Mohon pilih alasan pembatalan.',
            }).then(result => {
                if (!result.isConfirmed) return;

                if (result.value === 'other') {
                    Swal.fire({
                        icon: 'question',
                        title: 'Alasan Pembatalan',
                        input: 'text',
                        inputPlaceholder: 'Tulis alasan Anda...',
                        showCancelButton: true,
                        confirmButtonText: 'Batalkan Pesanan',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#B42318',
                        inputValidator: (value) => !value && 'Mohon isi alasan pembatalan.',
                    }).then(r2 => {
                        if (r2.isConfirmed) submitCancelOrder(cancelUrl, r2.value);
                    });
                } else {
                    submitCancelOrder(cancelUrl, CANCEL_REASONS[result.value]);
                }
            });
        }

        async function submitCancelOrder(url, reason) {
            showLoading();

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reason }),
                });

                const data = await res.json();
                hideLoading();

                if (!res.ok) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                    return;
                }

                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
            } catch (err) {
                hideLoading();
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan.' });
            }
        }
    </script>
@endpush
