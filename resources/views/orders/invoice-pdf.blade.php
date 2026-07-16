<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1f2937;
            margin: 0;
            background-color: #ffffff;
        }

        .container {
            padding: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .header-table {
            margin-bottom: 40px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            text-transform: uppercase;
        }

        .brand-details {
            color: #6b7280;
            font-size: 10px;
            margin-top: 5px;
        }

        .invoice-label {
            text-align: right;
            vertical-align: top;
        }

        .invoice-label h2 {
            margin: 0;
            font-size: 24px;
            color: #e5e7eb;
            font-weight: 900;
        }

        .badge {
            padding: 2px 6px;
            background: #f3f4f6;
            border-radius: 4px;
            font-size: 9px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .status-paid,
        .status-processing,
        .status-shipped,
        .status-completed {
            background: #d1fae5;
            color: #047857;
        }

        .status-pending_payment {
            background: #fef3c7;
            color: #b45309;
        }

        .status-cancelled,
        .status-expired,
        .status-failed {
            background: #fee2e2;
            color: #b91c1c;
        }

        .info-section {
            margin-bottom: 30px;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
        }

        .info-title {
            text-transform: uppercase;
            font-size: 9px;
            font-weight: 700;
            color: #9ca3af;
            margin-bottom: 5px;
            display: block;
        }

        .items-table {
            margin-top: 20px;
            table-layout: fixed;
        }

        .items-table th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            padding: 12px 10px;
            border-bottom: 2px solid #1f2937;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .summary-wrapper {
            margin-top: 20px;
        }

        .summary-table {
            width: 290px;
            margin-left: auto;
        }

        .summary-table td {
            padding: 6px 0;
        }

        .grand-total-row td {
            padding-top: 10px;
            font-size: 14px;
            font-weight: 800;
            color: #111827;
            border-top: 1px solid #1f2937;
        }

        .notes-box,
        .shipping-box {
            margin-top: 20px;
            padding: 12px 14px;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            font-size: 10px;
            color: #374151;
            background-color: #f8fafc;
        }

        .box-title {
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
            color: #9ca3af;
            font-size: 9px;
        }

        .logo {
            height: 50px;
            max-width: 140px;
            object-fit: contain;
            margin-bottom: 6px;
        }

        .signature-section {
            margin-top: 50px;
        }

        .signature-table {
            width: 100%;
            text-align: center;
        }

        .signature-box {
            height: 70px;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            margin: 0 40px 6px;
        }

        .signature-line.single {
            margin: 0 auto 6px;
            width: 220px;
        }

        .signature-name {
            font-weight: 700;
            font-size: 11px;
        }

        .signature-label {
            font-size: 10px;
            color: #6b7280;
        }

        .warranty-box {
            margin-top: 20px;
            padding: 12px 14px;
            border: 1px dashed #9ca3af;
            border-radius: 6px;
            font-size: 10px;
            color: #374151;
            background-color: #f9fafb;
        }
    </style>
</head>

<body>
    @php
        $logo = $settings['logo'] ?? 'logo.jpeg';
        $namaToko = $settings['nama_toko'] ?? 'Barokah Computer';
        $alamat = $settings['alamat'] ?? 'Alamat toko belum diatur';
        $statusClass = 'status-' . $order->status;
        $statusLabel = $order->status_label;
        $tanggalTransaksi = $order->paid_at ?? $order->created_at;
        $customerName = $order->customer?->name ?? $order->recipient_name ?? 'Customer';
        $garansiHari = 14;
        $tanggalGaransi = $tanggalTransaksi->copy()->addDays($garansiHari);
    @endphp
    <div class="container">
        <table class="header-table">
            <tr>
                <td>
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" class="logo" alt="Logo">
                    @endif

                    <div class="brand-name">{{ $namaToko }}</div>

                    <div class="brand-details">
                        {{ $alamat }}<br>
                        @if($contacts->count())
                            {{ $contacts->pluck('label')->zip($contacts->pluck('phone'))
                                ->map(fn($c) => $c[0] . ': ' . $c[1])
                                ->implode(' | ') }}
                        @endif
                    </div>
                </td>

                <td class="invoice-label">
                    <h2>INVOICE</h2>
                    <span class="badge">NO: {{ $order->order_number }}</span><br>
                    <span class="status-badge {{ $statusClass }}" style="margin-top:6px;">{{ $statusLabel }}</span>
                </td>
            </tr>
        </table>

        <table class="info-section">
            <tr>
                <td width="50%">
                    <span class="info-title">Ditagihkan Kepada:</span>
                    <strong>{{ $customerName }}</strong><br>
                    @if($order->recipient_phone)
                        <span style="font-size:10px; color:#6b7280;">HP: {{ $order->recipient_phone }}</span><br>
                    @endif
                    @if($order->customer?->email)
                        <span style="font-size:10px; color:#6b7280;">Email: {{ $order->customer->email }}</span>
                    @endif
                </td>

                <td width="50%" class="text-right">
                    <span class="info-title">Tanggal Transaksi:</span>
                    <strong>{{ $tanggalTransaksi->translatedFormat('d F Y') }}</strong><br>
                    <span style="font-size:10px; color:#6b7280;">
                        {{ $order->delivery_label }}{{ $order->courier_service_name ? ' • ' . $order->courier_service_name : '' }}
                    </span>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" width="40px">#</th>
                    <th class="text-left">Deskripsi Produk</th>
                    <th class="text-center" width="50px">Qty</th>
                    <th class="text-right" width="100px">Harga</th>
                    <th class="text-right" width="110px">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            <strong>{{ $item->product_name }}</strong><br>
                            <small style="color: #6b7280;">{{ $item->product?->product_code ?? 'Produk online' }}</small>
                        </td>
                        <td class="text-center">{{ $item->qty ?? 1 }}</td>
                        <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td class="text-left" style="color: #6b7280;">Subtotal Produk</td>
                    <td class="text-right">Rp {{ number_format($order->items_subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-left" style="color: #6b7280;">Ongkos Kirim</td>
                    <td class="text-right">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if(($order->referral_discount ?? 0) > 0)
                    <tr>
                        <td class="text-left" style="color: #6b7280;">Diskon Referral</td>
                        <td class="text-right">- Rp {{ number_format($order->referral_discount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="grand-total-row">
                    <td class="text-left">GRAND TOTAL</td>
                    <td class="text-right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="shipping-box" style="clear: both;">
            <div class="box-title">{{ $order->delivery_method === 'pickup' ? 'Info Pickup' : 'Alamat Pengiriman' }}</div>
            <strong>{{ $order->recipient_name }}</strong><br>
            {{ $order->address_detail }}<br>
            {{ $order->district }}, {{ $order->city }}, {{ $order->province }}
        </div>

        @if($order->notes || $order->referral_code)
            <div class="notes-box">
                <div class="box-title">Catatan Tambahan</div>
                @if($order->notes)
                    <div>Catatan customer: {{ $order->notes }}</div>
                @endif
                @if($order->referral_code)
                    <div>
                        Referral: {{ $order->referral_code }}{{ $order->marketing_name ? ' (' . $order->marketing_name . ')' : '' }}
                    </div>
                @endif
            </div>
        @endif

        <div class="warranty-box">
            <div class="box-title">Keterangan Garansi</div>
            <ul style="margin: 6px 0 0 16px; padding: 0;">
                <li>
                    Masa garansi <strong>{{ $garansiHari }} hari</strong> terhitung sejak
                    <strong>{{ $tanggalTransaksi->translatedFormat('d F Y') }}</strong>
                    sampai dengan
                    <strong>{{ $tanggalGaransi->translatedFormat('d F Y') }}</strong>.
                </li>
                <li>Garansi mengikuti ketentuan produk, penjual, atau distributor yang berlaku.</li>
                <li>Garansi tidak berlaku untuk kerusakan fisik, cairan, atau kesalahan penggunaan.</li>
                <li>Nota ini perlu disertakan saat pengajuan garansi atau komplain transaksi.</li>
            </ul>
        </div>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td width="{{ $order->delivery_method === 'pickup' ? '50%' : '100%' }}">
                        <div class="signature-box"></div>
                        <div class="signature-line {{ $order->delivery_method === 'pickup' ? '' : 'single' }}"></div>
                        <div class="signature-name">{{ $namaToko }}</div>
                        <div class="signature-label">Penjual</div>
                    </td>

                    @if($order->delivery_method === 'pickup')
                        <td width="50%">
                            <div class="signature-box"></div>
                            <div class="signature-line"></div>
                            <div class="signature-name">{{ $customerName }}</div>
                            <div class="signature-label">Pembeli</div>
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di <strong>{{ $namaToko }}</strong>.</p>
        </div>
    </div>
</body>

<script>
    window.onload = function () {
        window.print();
        window.onafterprint = function () {
            window.close();
        }
    }
</script>

</html>
