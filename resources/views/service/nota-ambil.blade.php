<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nota Pengambilan {{ $service->service_number }}</title>
    <style>
        @page {
            margin: 0;
        }

        html {
            height: auto;
            overflow: hidden;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            height: auto;
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

        .divider {
            border: none;
            border-top: 1px solid #f3f4f6;
            margin: 10px 0;
        }

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

        .keluhan-label {
            font-size: 8px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            margin-top: 5px;
            display: block;
        }

        .keluhan-text {
            font-size: 10px;
            color: #374151;
        }

        .teknisi-note {
            font-size: 10px;
            color: #059669;
            margin-top: 3px;
        }

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

        .summary-wrapper {
            margin-bottom: 10px;
        }

        .summary-table {
            width: 250px;
            margin-left: auto;
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

        .footer {
            margin-top: 10px;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 8px;
            padding-bottom: 0;
            margin-bottom: 0;
            color: #9ca3af;
            font-size: 8px;
        }
    </style>
</head>

<body>
    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');

        $logo = $settings['logo'] ?? null;
        $namaToko = $settings['nama_toko'] ?? 'Barokah Computer';
        $alamat = $settings['alamat'] ?? '';
        $garansiHari = 14;
        $tanggalAmbil = $service->taken_at ?? $service->done_at ?? now();
        $tanggalGaransi = Carbon::parse($tanggalAmbil)->addDays($garansiHari);

        $spareParts = is_array($service->spare_parts)
            ? $service->spare_parts
            : json_decode($service->spare_parts ?? '[]', true);
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
                        @if(isset($contacts) && $contacts->count())
                                            {{ $contacts->pluck('label')->zip($contacts->pluck('phone'))
                            ->map(fn($c) => $c[0] . ': ' . $c[1])
                            ->implode(' | ') }}
                        @endif
                    </div>
                </td>
                <td class="slip-label">
                    <h2>NOTA AMBIL</h2>
                    <span class="badge">{{ $service->service_number }}</span>
                </td>
            </tr>
        </table>

        <hr class="divider">

        <table class="info-section">
            <tr>
                <td width="50%">
                    <span class="info-title">Informasi Konsumen</span>
                    <strong style="font-size:12px; color:#111827;">{{ $service->customer_name }}</strong><br>
                    <span style="font-size:9px; color:#6b7280;">{{ $service->customer_phone ?? '-' }}</span>
                </td>
                <td width="50%" class="text-right">
                    <span class="info-title">Waktu Service</span>
                    <strong style="font-size:11px;">Masuk: {{ $service->created_at->format('d/m/Y H:i') }}</strong><br>
                    <span style="font-size:9px; color:#6b7280;">
                        Selesai: {{ $service->done_at?->format('d/m/Y H:i') ?? '-' }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="device-card">
            <div class="device-title">
                {{ trim($service->device_brand . ' ' . $service->device_type) ?: 'Perangkat' }}
            </div>
            @if($service->device_sn)
                <div class="device-sn">SN: {{ $service->device_sn }}</div>
            @endif
            <span class="keluhan-label">Keluhan</span>
            <div class="keluhan-text">{{ $service->complaint }}</div>
            @if($service->technician_notes)
                <div class="teknisi-note">✓ {{ $service->technician_notes }}</div>
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" width="36px">#</th>
                    <th class="text-left">Komponen Biaya</th>
                    <th class="text-right" width="120px">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $row = 1; @endphp
                @if($service->service_cost > 0)
                    <tr>
                        <td class="text-center">{{ $row++ }}</td>
                        <td class="text-left"><strong>Jasa Service</strong></td>
                        <td class="text-right" style="color:#059669; font-weight:700;">
                            Rp {{ number_format($service->service_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                @foreach($spareParts as $sp)
                    <tr>
                        <td class="text-center">{{ $row++ }}</td>
                        <td class="text-left">
                            <strong>Sparepart</strong><br>
                            <small style="color:#6b7280;">{{ $sp['name'] }}</small>
                        </td>
                        <td class="text-right" style="color:#059669; font-weight:700;">
                            Rp {{ number_format($sp['price'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-wrapper">
            <table class="summary-table">
                @if($service->spare_part_cost > 0)
                    <tr>
                        <td class="text-left" style="color:#6b7280;">Harga Sparepart</td>
                        <td class="text-right">
                            Rp {{ number_format($service->spare_part_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                @if($service->service_cost > 0)
                    <tr>
                        <td class="text-left" style="color:#6b7280;">Harga Jasa</td>
                        <td class="text-right">
                            Rp {{ number_format($service->service_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                <tr class="grand-total-row">
                    <td class="text-left">GRAND TOTAL</td>
                    <td class="text-right">
                        Rp {{ number_format($service->total_cost, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="notes-box">
            <div class="notes-title">Keterangan Garansi</div>
            <ul>
                <li>
                    Masa garansi <strong>{{ $garansiHari }} hari</strong> terhitung sejak
                    <strong>{{ Carbon::parse($tanggalAmbil)->translatedFormat('d F Y') }}</strong>
                    s/d <strong>{{ $tanggalGaransi->translatedFormat('d F Y') }}</strong>.
                </li>
                <li>Garansi tidak berlaku untuk kerusakan fisik, cairan, atau kesalahan penggunaan.</li>
                <li>Wajib menyertakan nota ini saat klaim garansi.</li>
            </ul>
        </div>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td width="50%">
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $settings['nama_toko'] ?? 'Barokah Computer' }}</div>
                    </td>
                    <td width="50%">
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $service->customer_name }}</div>
                        <div class="signature-label">Penerima</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB &nbsp;·&nbsp;
            <strong>{{ $namaToko }}</strong>
        </div>

    </div>
</body>


</html>