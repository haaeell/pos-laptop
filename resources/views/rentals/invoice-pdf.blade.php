<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice Sewa {{ $rental->rental_number }}</title>
    <style>
        @page {
            margin: 28px 32px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            overflow-wrap: break-word;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #6b7280;
            font-size: 9px;
        }

        .header-table {
            margin-bottom: 14px;
        }

        .brand-name {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
        }

        .brand-details {
            color: #6b7280;
            font-size: 9px;
            margin-top: 3px;
        }

        .logo {
            height: 44px;
            max-width: 130px;
            margin-bottom: 4px;
        }

        .slip-label {
            text-align: right;
            width: 38%;
        }

        .slip-label h2 {
            margin: 0;
            font-size: 22px;
            color: #9ca3af;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            background: #f3f4f6;
            border-radius: 4px;
            font-size: 9px;
            color: #374151;
            margin: 4px 0;
        }

        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 10px 0;
        }

        .info-section {
            margin-bottom: 12px;
            table-layout: fixed;
        }

        .info-section td {
            width: 50%;
            padding-right: 12px;
        }

        .info-title {
            display: block;
            text-transform: uppercase;
            font-size: 8px;
            font-weight: bold;
            color: #9ca3af;
            margin-bottom: 2px;
        }

        .device-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px 0 5px;
        }

        .items-table {
            margin-bottom: 10px;
            table-layout: fixed;
        }

        .items-table th {
            background: #f9fafb;
            color: #374151;
            font-weight: bold;
            font-size: 8px;
            padding: 7px 6px;
            border-bottom: 2px solid #1f2937;
            text-align: left;
        }

        .items-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }

        .items-table th.text-right,
        .items-table td.text-right {
            text-align: right;
        }

        .items-table th.text-center,
        .items-table td.text-center {
            text-align: center;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .summary-table {
            width: 55%;
            margin: 8px 0 14px auto;
            page-break-inside: avoid;
        }

        .summary-table td {
            padding: 4px 0;
        }

        .grand-total-row td {
            padding-top: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            border-top: 1px solid #1f2937;
        }

        .notes-box {
            margin: 12px 0;
            padding: 8px 12px;
            border: 1px dashed #9ca3af;
            border-radius: 6px;
            font-size: 9px;
            color: #374151;
            background: #f9fafb;
            page-break-inside: avoid;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .notes-box ul {
            margin: 0 0 0 14px;
            padding: 0;
        }

        .notes-box li {
            margin-bottom: 3px;
        }

        .signature-section {
            margin-top: 14px;
            page-break-inside: avoid;
        }

        .signature-table {
            text-align: center;
            table-layout: fixed;
        }

        .signature-box {
            height: 42px;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            margin: 0 32px 4px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10px;
        }

        .footer {
            margin-top: 12px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            color: #9ca3af;
            font-size: 8px;
        }

        .preserve-lines {
            white-space: pre-line;
        }
    </style>
</head>

<body>
    @php
        $logo = $settings['logo'] ?? null;
        $namaToko = $settings['nama_toko'] ?? 'Barokah Computer';
        $days = count($rental->rental_dates);
        $money = fn($value) => 'Rp ' .
            number_format((float) $value, (float) $value == floor((float) $value) ? 0 : 2, ',', '.');
    @endphp
    <table class="header-table">
        <tr>
            <td>
                @if ($logo && is_file(public_path('storage/' . $logo)))
                    <img src="{{ public_path('storage/' . $logo) }}" class="logo" alt="Logo toko">
                @endif
                <div class="brand-name">{{ $namaToko }}</div>
                <div class="brand-details">{{ $settings['alamat'] ?? '' }}
                    @if (isset($contacts) && $contacts->isNotEmpty())
                        <br>{{ $contacts->map(fn($contact) => $contact->label . ': ' . $contact->phone)->join(' | ') }}
                    @endif
                </div>
            </td>
            <td class="slip-label">
                <h2>INVOICE SEWA</h2><span class="badge">{{ $rental->rental_number }}</span>
                <div class="muted">Tanggal dibuat: {{ $rental->created_at->format('d/m/Y H:i') }}</div>
                <div class="muted">Petugas: {{ $rental->user?->name ?? '-' }}</div>
            </td>
        </tr>
    </table>
    <hr class="divider">
    <table class="info-section">
        <tr>
            <td><span class="info-title">Informasi Penyewa / Instansi</span><strong
                    style="font-size:12px">{{ $rental->renter_name }}</strong>
                <div class="preserve-lines">{{ $rental->address }}</div>
                <div>HP / WhatsApp: {{ $rental->phone }}</div>
            </td>
            <td><span class="info-title">Penanggung Jawab</span><strong>{{ $rental->person_in_charge }}</strong><span
                    class="info-title" style="margin-top:6px">Keperluan Sewa</span>{{ $rental->purpose }}</td>
        </tr>
    </table>
    <div class="device-card">
        <table class="info-section" style="margin-bottom:0">
            <tr>
                <td><span class="info-title">Tanggal Pemakaian ({{ $days }}
                        hari)</span><strong>{{ collect($rental->rental_dates)->sort()->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m/Y'))->join(', ') }}</strong>
                    <div class="muted">Jumlah laptop: {{ $rental->total_qty }} unit</div>
                </td>
                <td><span class="info-title">Rencana
                        Pengembalian</span><strong>{{ $rental->planned_return_date->format('d/m/Y') }}</strong>
                    <div>Status barang:
                        <strong>{{ $rental->status === 'returned' ? 'Sudah dikembalikan' : 'Sedang disewa' }}</strong>
                    </div>
                    @if ($rental->returned_at)
                        <div class="muted">Dikembalikan: {{ $rental->returned_at->format('d/m/Y H:i') }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="section-title">Rincian Laptop & Biaya Sewa</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%">No.</th>
                <th style="width:38%">LAPTOP / KODE / KELENGKAPAN</th>
                <th class="text-center" style="width:7%">UNIT</th>
                <th class="text-center" style="width:7%">HARI</th>
                <th class="text-right" style="width:21%">TARIF / UNIT / HARI</th>
                <th class="text-right" style="width:23%">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rental->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $item->product_name }}</strong>
                        <div class="muted">Kode: {{ $item->product_code }}</div>
                        <div>Kelengkapan: {{ $item->accessories ?: '-' }}</div>
                        <div>Kondisi awal: {{ $item->condition_out ?: '-' }}</div>
                    </td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-center">{{ $days }}</td>
                    <td class="text-right">{{ $money($rental->daily_rate) }}</td>
                    <td class="text-right"><strong>{{ $money($item->qty * $days * $rental->daily_rate) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="muted">Perhitungan: {{ $rental->total_qty }} unit × {{ $days }} hari pemakaian ×
        {{ $money($rental->daily_rate) }} = {{ $money($rental->rental_total) }}.<br>Hanya tanggal pemakaian yang
        tercantum di atas yang dihitung. Tarif berlaku untuk seluruh unit pada transaksi ini.</p>
    <table class="summary-table">
        <tr>
            <td>Subtotal biaya sewa</td>
            <td class="text-right">{{ $money($rental->rental_total) }}</td>
        </tr>
        <tr>
            <td>Denda{{ $rental->status === 'active' ? ' (belum ada)' : '' }}</td>
            <td class="text-right">{{ $money($rental->fine_amount) }}</td>
        </tr>
        <tr class="grand-total-row">
            <td>TOTAL BIAYA</td>
            <td class="text-right">{{ $money($rental->grand_total) }}</td>
        </tr>
    </table>
    @if ($rental->status === 'returned')
        <div class="section-title">Pemeriksaan Pengembalian</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:38%">LAPTOP / KODE</th>
                    <th style="width:25%">KONDISI KEMBALI</th>
                    <th style="width:37%">KENDALA / KERUSAKAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rental->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}<div class="muted">{{ $item->product_code }} ·
                                {{ $item->qty }} unit</div>
                        </td>
                        <td>{{ $item->condition_in ?? '-' }}</td>
                        <td class="preserve-lines">{{ $item->return_issue ?: 'Tidak ada kendala tercatat.' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($rental->return_notes)
            <div class="device-card"><span class="info-title">Catatan Pengembalian / Keterangan Denda</span>
                <div class="preserve-lines">{{ $rental->return_notes }}</div>
            </div>
        @endif
    @else
        <p class="muted">Kondisi pengembalian belum diperiksa. Denda, bila ada, akan dicatat setelah pengembalian.</p>
    @endif
    <div class="notes-box">
        <div class="notes-title">Keterangan Penting & Aturan Sewa</div>@include('rentals.partials.terms')
    </div>
    <div class="signature-section">
        <p class="muted text-center">Kondisi dan kelengkapan perangkat diperiksa bersama saat serah terima.</p>
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-box"></div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $namaToko }}</div>
                    <div class="muted">Petugas / Pihak Toko</div>
                </td>
                <td>
                    <div class="signature-box"></div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $rental->person_in_charge }}</div>
                    <div class="muted">Penyewa / Penanggung Jawab</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="footer">Dicetak pada: {{ now()->format('d F Y, H:i') }} ·
        <strong>{{ $namaToko }}</strong><br>Invoice ini merupakan rincian biaya sewa, bukan bukti pelunasan
        pembayaran.</div>
</body>

</html>
