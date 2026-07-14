<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        .header {
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #e5e7eb;
        }

        td {
            padding: 8px;
            border: 1px solid #e5e7eb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            float: right;
            width: 320px;
        }

        .summary table {
            width: 100%;
        }

        .summary td {
            border: none;
            padding: 5px 0;
            font-size: 11px;
        }

        .summary .label {
            color: #6b7280;
        }

        .summary .value {
            font-weight: bold;
            text-align: right;
        }

        .summary .minus {
            color: #dc2626;
        }

        .summary .divider td {
            border-top: 1px solid #d1d5db;
            padding-top: 6px;
        }

        .summary .total td {
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid #111827;
            padding-top: 6px;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #9ca3af;
            text-align: right;
            clear: both;
        }

        .badge {
            background: #f1f5f9;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-paid {
            background: #d1fae5;
            color: #047857;
        }

        .badge-partial {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #b91c1c;
        }

        .sisa-text {
            font-size: 8px;
            color: #6b7280;
            text-transform: none;
            display: block;
            margin-top: 2px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="title">Laporan Keuangan</div>
        <div class="subtitle">
            Periode {{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:18%">Invoice</th>
                <th style="width:10%">Sumber</th>
                <th style="width:16%">Tanggal</th>
                <th style="width:14%" class="text-right">Grand Total</th>
                <th style="width:14%" class="text-right">Profit</th>
                <th style="width:10%" class="text-center">Metode</th>
                <th style="width:13%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $i => $sale)
                @php
                    $badgeClass = match ($sale->payment_status) {
                        'paid' => 'badge-paid',
                        'partial' => 'badge-partial',
                        default => 'badge-unpaid',
                    };
                    $statusLabel = match ($sale->payment_status) {
                        'paid' => 'Lunas',
                        'partial' => 'Sebagian',
                        default => 'Hutang',
                    };
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $sale->invoice_number }}</td>
                    <td><span class="badge">{{ $sale->source }}</span></td>
                    <td>{{ $sale->date->format('d M Y H:i') }}</td>
                    <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($sale->benefit, 0, ',', '.') }}</td>
                    <td class="text-center"><span class="badge">{{ $sale->payment_method }}</span></td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                        @if ($sale->payment_status !== 'paid')
                            <span class="sisa-text">Sisa Rp {{ number_format($sale->remaining_amount, 0, ',', '.') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Penjualan</td>
                <td class="value">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Penjualan Online</td>
                <td class="value">Rp {{ number_format($totalOnlineSales, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label minus">Piutang / Belum Tertagih</td>
                <td class="value minus">- Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
            </tr>
            <tr class="divider">
                <td class="label">Total Penjualan</td>
                <td class="value">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Bonus / (Loss)</td>
                <td class="value">Rp {{ number_format($bonusLoss, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Pendapatan Service</td>
                <td class="value">Rp {{ number_format($totalServices, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Penambahan Modal</td>
                <td class="value">Rp {{ number_format($totalPenambahanModal, 0, ',', '.') }}</td>
            </tr>
            <tr class="divider">
                <td class="label minus">Total Pengeluaran</td>
                <td class="value minus">- Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label minus">Cicilan Modal</td>
                <td class="value minus">- Rp {{ number_format($totalCicilan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label minus">Gaji Karyawan</td>
                <td class="value minus">- Rp {{ number_format($totalGajiKaryawan, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td class="label">Jumlah Saldo</td>
                <td class="value">
                    Rp
                    {{ number_format($totalSales - $totalExpenses + $totalPenambahanModal + $totalServices - $totalCicilan - $totalGajiKaryawan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td class="label" style="padding-top:10px;">Total Asset</td>
                <td class="value" style="padding-top:10px;">Rp {{ number_format($totalAsset, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak pada {{ now()->format('d M Y H:i') }}
    </div>

</body>

</html>
