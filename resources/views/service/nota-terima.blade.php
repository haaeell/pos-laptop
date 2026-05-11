<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tanda Terima {{ $service->service_number }}</title>
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
            padding: 28px 32px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

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
            margin-bottom: 14px;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            text-transform: uppercase;
        }

        .brand-details {
            color: #6b7280;
            font-size: 9px;
            margin-top: 3px;
        }

        .slip-label {
            text-align: right;
            vertical-align: top;
        }

        .slip-label h2 {
            margin: 0;
            font-size: 20px;
            color: #e5e7eb;
            font-weight: 900;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            background: #f3f4f6;
            border-radius: 4px;
            font-size: 9px;
            color: #374151;
            margin-top: 4px;
        }

        .logo {
            height: 44px;
            max-width: 130px;
            object-fit: contain;
            margin-bottom: 4px;
        }

        /* ===== DIVIDER ===== */
        .divider {
            border: none;
            border-top: 1px solid #f3f4f6;
            margin: 10px 0;
        }

        /* ===== INFO SECTION ===== */
        .info-section {
            margin-bottom: 12px;
        }

        .info-title {
            text-transform: uppercase;
            font-size: 8px;
            font-weight: 700;
            color: #9ca3af;
            margin-bottom: 2px;
            display: block;
        }

        /* ===== DEVICE CARD ===== */
        .device-card {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .device-title {
            font-weight: 700;
            font-size: 12px;
            color: #111827;
        }

        .device-sn {
            font-family: monospace;
            font-size: 9px;
            color: #6b7280;
            margin-top: 1px;
        }

        .detail-label {
            font-size: 8px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            margin-top: 6px;
            display: block;
        }

        .detail-text {
            font-size: 10px;
            color: #374151;
        }

        /* ===== ITEMS TABLE ===== */
        .items-table {
            margin-bottom: 8px;
            table-layout: fixed;
        }

        .items-table th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 8px;
            border-bottom: 2px solid #1f2937;
        }

        .items-table td {
            padding: 8px 8px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            font-size: 10px;
        }

        /* ===== ESTIMASI BIAYA ===== */
        .summary-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }

        .summary-table {
            width: 250px;
        }

        .summary-table td {
            padding: 4px 0;
            font-size: 10px;
        }

        .grand-total-row td {
            padding-top: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #111827;
            border-top: 1px solid #1f2937;
        }

        /* ===== NOTES BOX ===== */
        .notes-box {
            margin-bottom: 14px;
            padding: 8px 12px;
            border: 1px dashed #9ca3af;
            border-radius: 6px;
            font-size: 9px;
            color: #374151;
            background-color: #f9fafb;
        }

        .notes-title {
            font-weight: 700;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .notes-box ul {
            margin: 0 0 0 14px;
            padding: 0;
        }

        .notes-box li {
            margin-bottom: 2px;
        }

        /* ===== SIGNATURE ===== */
        .signature-section {
            margin-top: 14px;
        }

        .signature-table {
            width: 100%;
            text-align: center;
        }

        .signature-box {
            height: 48px;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            margin: 0 32px 4px;
        }

        .signature-name {
            font-weight: 700;
            font-size: 10px;
            color: #111827;
        }

        .signature-label {
            font-size: 9px;
            color: #6b7280;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 10px;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 8px;
            color: #9ca3af;
            font-size: 8px;
        }
    </style>
</head>

<body>
    @php
        $logo = $settings['logo'] ?? null;
        $namaToko = $settings['nama_toko'] ?? 'Barokah Computer';
        $alamat = $settings['alamat'] ?? '';
    @endphp

    <div class="container">

        <!-- ===== HEADER ===== -->
        <table class="header-table">
            <tr>
                <td>
                    @if($logo)
                        <img src="{{ public_path('storage/' . $logo) }}" class="logo" alt="Logo">
                    @endif
                    <div class="brand-name">{{ $namaToko }}</div>
                    <div class="brand-details">
                        {{ $alamat }}<br>
                        @if(isset($contacts) && $contacts->count())
                                            {{ $contacts->pluck('label')->zip($contacts->pluck('phone'))
                            ->map(fn($c) => $c[0] . ': ' . $c[1])
                            ->implode(' | ') }}
                        @endif
                    </div>
                </td>

                <td class="slip-label">
                    <h2>TANDA TERIMA</h2>
                    <span class="badge">{{ $service->service_number }}</span>
                </td>
            </tr>
        </table>

        <hr class="divider">

        <!-- ===== INFO KONSUMEN & WAKTU ===== -->
        <table class="info-section">
            <tr>
                <td width="50%">
                    <span class="info-title">Informasi Konsumen</span>
                    <strong style="font-size:12px; color:#111827;">{{ $service->customer_name }}</strong><br>
                    <span style="font-size:9px; color:#6b7280;">{{ $service->customer_phone ?? '-' }}</span>
                </td>

                <td width="50%" class="text-right">
                    <span class="info-title">Waktu Masuk</span>
                    <strong style="font-size:11px;">{{ $service->created_at->format('d/m/Y H:i') }}</strong><br>
                    <span style="font-size:9px; color:#6b7280;">
                        Status: <strong style="color:#d97706;">Menunggu Estimasi</strong>
                    </span>
                </td>
            </tr>
        </table>

        <!-- ===== DETAIL PERANGKAT ===== -->
        <div class="device-card">
            <div class="device-title">
                {{ trim($service->device_brand . ' ' . $service->device_type) ?: 'Perangkat' }}
            </div>

            @if($service->device_sn)
                <div class="device-sn">SN / IMEI: {{ $service->device_sn }}</div>
            @endif

            <span class="detail-label">Keluhan</span>
            <div class="detail-text">{{ $service->complaint }}</div>

            @if($service->notes)
                <span class="detail-label">Catatan</span>
                <div class="detail-text" style="color:#6b7280; font-style:italic;">{{ $service->notes }}</div>
            @endif
        </div>

        <!-- ===== ESTIMASI BIAYA ===== -->
        <div class="summary-wrapper">
            <table class="summary-table">
                <tr class="grand-total-row">
                    <td class="text-left">ESTIMASI BIAYA</td>
                    <td class="text-right" style="color:#9ca3af; font-style:italic; font-size:11px; font-weight:400;">
                        Akan dikonfirmasi
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== CATATAN PENTING ===== -->
        <div class="notes-box">
            <div class="notes-title">Keterangan Penting</div>
            <ul>
                <li>Simpan nota ini sebagai bukti pengambilan barang.</li>
                <li>Kami akan menghubungi Anda setelah estimasi biaya selesai.</li>
                <li>Barang tidak diambil lebih dari <strong>30 hari</strong> menjadi tanggung jawab pemilik.</li>
            </ul>
        </div>

        <!-- ===== TANDA TANGAN ===== -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td width="50%">
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $namaToko }}</div>
                        <div class="signature-label">Penerima Barang</div>
                    </td>
                    <td width="50%">
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $service->customer_name }}</div>
                        <div class="signature-label">Penyerah Barang</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== FOOTER ===== -->
        <div class="footer">
            Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB &nbsp;·&nbsp;
            <strong>{{ $namaToko }}</strong>
        </div>

    </div>
</body>

</html>