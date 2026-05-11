<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $detail->employee->full_name }}</title>
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

        .slip-label {
            text-align: right;
            vertical-align: top;
        }

        .slip-label h2 {
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

        .logo {
            height: 50px;
            max-width: 140px;
            object-fit: contain;
            margin-bottom: 6px;
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

        /* ===== ITEMS TABLE ===== */
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

        .deduction-row td {
            color: #DC2626;
            background-color: #fff5f5;
            font-style: italic;
        }

        /* ===== SUMMARY ===== */
        .summary-wrapper {
            margin-top: 20px;
        }

        .summary-table {
            width: 280px;
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

        /* ===== SIGNATURE ===== */
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

        /* ===== NOTES BOX ===== */
        .notes-box {
            margin-top: 25px;
            padding: 12px 14px;
            border: 1px dashed #9ca3af;
            border-radius: 6px;
            font-size: 10px;
            color: #374151;
            background-color: #f9fafb;
        }

        .notes-title {
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
            color: #9ca3af;
            font-size: 9px;
        }
    </style>
</head>

<body>
    @php
        $logo = $settings['logo'] ?? 'logo.jpeg';
        $namaToko = $settings['nama_toko'] ?? 'Barokah Computer';
        $alamat = $settings['alamat'] ?? 'Alamat toko belum diatur';

        $months = [
            '',
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];
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
                    <h2>SLIP GAJI</h2>
                    <span class="badge">
                        Periode: {{ $months[$payroll->period_month] }} {{ $payroll->period_year }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- ===== INFO KARYAWAN ===== -->
        <table class="info-section">
            <tr>
                <td width="50%">
                    <span class="info-title">Data Karyawan</span>
                    <strong>{{ $detail->employee->full_name }}</strong><br>
                    <span style="font-size:10px; color:#6b7280;">
                        {{ $detail->employee->position }}
                    </span><br>
                    <span style="font-size:10px; color:#6b7280;">
                        No. Pegawai: {{ $detail->employee->employee_number }}
                    </span>
                </td>

                <td width="50%" class="text-right">
                    <span class="info-title">Info Pembayaran</span>
                    <strong>
                        {{ $detail->employee->bank_name ?? '-' }}
                        @if($detail->employee->account_number)
                            – {{ $detail->employee->account_number }}
                        @endif
                    </strong><br>
                    <span style="font-size:10px; color:#6b7280;">
                        Tgl Rilis: {{ \Carbon\Carbon::parse($payroll->release_date) }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- ===== TABEL KOMPONEN ===== -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" width="40px">#</th>
                    <th class="text-left">Komponen Gaji</th>
                    <th class="text-right" width="130px">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>

                <!-- Gaji Pokok -->
                <tr>
                    <td class="text-center">1</td>
                    <td class="text-left">
                        <strong>Gaji Pokok</strong>
                    </td>
                    <td class="text-right" style="color:#059669; font-weight:700;">
                        Rp {{ number_format($detail->basic_salary, 0, ',', '.') }}
                    </td>
                </tr>

                @if ($detail->sales_bonus > 0)
                    <tr>
                        <td class="text-center">2</td>
                        <td class="text-left">
                            <strong>Bonus Penjualan</strong><br>
                            <small style="color:#6b7280;">
                                {{ $detail->total_transactions }} transaksi terbayar
                            </small>
                        </td>
                        <td class="text-right" style="color:#059669; font-weight:700;">
                            Rp {{ number_format($detail->sales_bonus, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif

                @if ($detail->technician_fee > 0)
                    <tr>
                        <td class="text-center">{{ $detail->sales_bonus > 0 ? 3 : 2 }}</td>
                        <td class="text-left">
                            <strong>Jasa Teknisi</strong>
                        </td>
                        <td class="text-right" style="color:#059669; font-weight:700;">
                            Rp {{ number_format($detail->technician_fee, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif

                @if ($detail->other_allowance > 0)
                    <tr>
                        <td class="text-center">•</td>
                        <td class="text-left">
                            <strong>Tunjangan Lainnya</strong>
                        </td>
                        <td class="text-right" style="color:#059669; font-weight:700;">
                            Rp {{ number_format($detail->other_allowance, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif

                @if ($detail->deduction > 0)
                    <tr class="deduction-row">
                        <td class="text-center">–</td>
                        <td class="text-left">
                            <strong>Potongan</strong>
                        </td>
                        <td class="text-right" style="font-weight:700;">
                            – Rp {{ number_format($detail->deduction, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif

            </tbody>
        </table>

        <!-- ===== SUMMARY ===== -->
        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td class="text-left" style="color:#6b7280;">
                        Total Pendapatan
                    </td>
                    <td class="text-right">
                        Rp
                        {{ number_format($detail->basic_salary + $detail->sales_bonus + $detail->technician_fee + $detail->other_allowance, 0, ',', '.') }}
                    </td>
                </tr>
                @if ($detail->deduction > 0)
                    <tr>
                        <td class="text-left" style="color:#DC2626;">Potongan</td>
                        <td class="text-right" style="color:#DC2626;">
                            – Rp {{ number_format($detail->deduction, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                <tr class="grand-total-row">
                    <td class="text-left">GAJI BERSIH</td>
                    <td class="text-right">
                        Rp {{ number_format($detail->net_salary, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== CATATAN ===== -->
        <div class="notes-box">
            <div class="notes-title">Keterangan</div>
            <ul style="margin: 6px 0 0 16px; padding: 0;">
                <li>Slip gaji ini merupakan dokumen resmi yang dicetak otomatis oleh sistem.</li>
                <li>Harap simpan slip ini sebagai bukti penerimaan gaji periode
                    {{ $months[$payroll->period_month] }} {{ $payroll->period_year }}.
                </li>
                <li>Untuk pertanyaan atau keberatan, hubungi HRD / Finance dalam 3 hari kerja.</li>
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
                    </td>
                    <td width="50%">
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $detail->employee->full_name }}</div>
                        <div class="signature-label">Karyawan</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== FOOTER ===== -->
        <div class="footer">
            <p>
                Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB &nbsp;·&nbsp;
                <strong>{{ $namaToko }}</strong>
            </p>
        </div>

    </div>
</body>

</html>