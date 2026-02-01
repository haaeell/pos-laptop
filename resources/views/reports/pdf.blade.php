<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
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

        .summary {
            margin-top: 20px;
            width: 100%;
        }

        .summary td {
            border: none;
            padding: 6px 0;
        }

        .summary .label {
            color: #6b7280;
        }

        .summary .value {
            font-weight: bold;
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #9ca3af;
            text-align: right;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="title">Laporan Penjualan</div>
        <div class="subtitle">
            Periode {{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}
        </div>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:20%">Invoice</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:20%" class="text-right">Grand Total</th>
                <th style="width:20%" class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $i => $sale)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                    <td class="text-right">
                        Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($sale->benefit, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; float: right; width: 300px;">
        <table style="width: 100%;">
            <tr>
                <td>Profit Penjualan</td>
                <td style="text-align: right;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bonus/Loss</td>
                <td style="text-align: right;">Rp {{ number_format($bonusLoss, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="color: #ea580c;">Total Pengeluaran</td>
                <td style="text-align: right; color: #ea580c;">- Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                </td>
            </tr>
            <tr style="font-weight: bold; border-top: 1px solid #000;">
                <td>Profit Bersih</td>
                <td style="text-align: right;">Rp
                    {{ number_format($totalProfit + $bonusLoss - $totalExpenses, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak pada {{ now()->format('d M Y H:i') }}
    </div>

</body>

</html>
