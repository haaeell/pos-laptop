<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $sale->invoice_number }}</title>
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

        /* ===== ALIGNMENT HELPERS ===== */
        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* ===== HEADER ===== */
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

        /* ===== INFO SECTION ===== */
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

        /* ===== TABLE ITEMS (PERBAIKAN DI SINI) ===== */
        .items-table {
            margin-top: 20px;
            table-layout: fixed;
        }

        /* Fixed layout untuk presisi lebar */

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
            /* Menjaga teks tetap sejajar di tengah secara vertikal */
        }

        .bonus-row td {
            color: #059669;
            background-color: #f0fdf4;
            font-style: italic;
        }

        /* ===== SUMMARY ===== */
        .summary-wrapper {
            margin-top: 20px;
        }

        .summary-table {
            width: 250px;
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
            margin-top: 60px;
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

        .signature-name {
            font-weight: 700;
            font-size: 11px;
        }

        .signature-label {
            font-size: 10px;
            color: #6b7280;
        }

        .warranty-box {
            margin-top: 25px;
            padding: 12px 14px;
            border: 1px dashed #9ca3af;
            border-radius: 6px;
            font-size: 10px;
            color: #374151;
            background-color: #f9fafb;
        }

        .warranty-title {
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    @php
        $logo = $settings['logo'] ?? 'logo.jpeg';
        $namaToko = $settings['nama_toko'] ?? 'Barokah Computer';
        $alamat = $settings['alamat'] ?? 'Alamat toko belum diatur';

        use Carbon\Carbon;

        Carbon::setLocale('id');

        $garansiHari = 14;
        $tanggalTransaksi = $sale->created_at;
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
                            ->implode(' | ')
                                                                                                                                                                                                                                                                            }}
                        @endif
                    </div>
                </td>

                <td class="invoice-label">
                    <h2>INVOICE</h2>
                    <span class="badge">NO: {{ $sale->invoice_number }}</span>
                </td>
            </tr>
        </table>

        <table class="info-section">
            <tr>
                <td width="50%">
                    <span class="info-title">Ditagihkan Kepada:</span>
                    <strong>
                        {{ $sale->customer_name ?? 'Pelanggan Umum' }}
                    </strong><br>

                    @if($sale->customer_phone)
                        <span style="font-size:10px; color:#6b7280;">
                            HP: {{ $sale->customer_phone }}
                        </span>
                    @endif
                </td>

                <td width="50%" class="text-right">
                    <span class="info-title">Tanggal Transaksi:</span>
                    <strong>{{ $sale->created_at->format('d F Y') }}</strong>
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
                    <th class="text-right" width="100px">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            <strong>{{ $item->product->name }}</strong><br>
                            <small style="color: #6b7280;">{{ $item->product->product_code }}</small>
                        </td>
                        <td class="text-center">1</td>
                        <td class="text-right">Rp {{ number_format($item->final_price) }}</td>
                        <td class="text-right">Rp {{ number_format($item->final_price) }}</td>
                    </tr>
                @endforeach

                @foreach($sale->bonuses as $bonus)
                    <tr class="bonus-row">
                        <td class="text-center">•</td>
                        <td class="text-left">
                            <strong>[BONUS] {{ $bonus->product->name }}</strong><br>
                            <small>{{ $bonus->product->product_code }}</small>
                        </td>
                        <td class="text-center">1</td>
                        <td class="text-right">Rp 0</td>
                        <td class="text-right">FREE</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td class="text-left" style="color: #6b7280;">Subtotal</td>
                    <td class="text-right">Rp {{ number_format($sale->grand_total) }}</td>
                </tr>
                <tr class="grand-total-row">
                    <td class="text-left">GRAND TOTAL</td>
                    <td class="text-right">Rp {{ number_format($sale->grand_total) }}</td>
                </tr>
            </table>
        </div>

        <div class="warranty-box">
            <div class="warranty-title">Keterangan Garansi</div>

            <ul style="margin: 6px 0 0 16px; padding: 0;">
                <li>
                    Masa garansi <strong>{{ $garansiHari }} hari</strong> terhitung sejak
                    <strong>{{ $tanggalTransaksi->translatedFormat('d F Y') }}</strong>
                    sampai dengan
                    <strong>{{ $tanggalGaransi->translatedFormat('d F Y') }}</strong>.

                </li>
                <li>Garansi sesuai ketentuan pabrik / distributor.</li>
                <li>Garansi tidak berlaku untuk kerusakan fisik, cairan, atau kesalahan penggunaan.</li>
                <li>Wajib menyertakan invoice ini saat klaim garansi.</li>
            </ul>
        </div>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td width="50%">
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $namaToko }}</div>
                        <div class="signature-label">Penjual</div>
                    </td>

                    <td width="50%">
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">
                            {{ $sale->customer_name ?? 'Pelanggan' }}
                        </div>
                        <div class="signature-label">Pembeli</div>
                    </td>
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