<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Arsip Buku {{ $book->starts_at->format('d/m/Y') }}</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #17243b; background: #f4f6fa; font-size: 12px; margin: 0; }
        main { max-width: 1050px; margin: 24px auto; background: #fff; padding: 32px; }
        h1 { margin: 0 0 8px; font-size: 25px; color: #1e3a8a; }
        h2 { font-size: 16px; margin: 28px 0 12px; }
        p { color: #475569; }
        .summary { background: #eef2ff; padding: 18px; margin: 22px 0; border: 1px solid #c7d2fe; }
        .summary strong { font-size: 21px; color: #1e3a8a; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #e9eef8; text-align: left; color: #334155; }
        th, td { border-bottom: 1px solid #dbe2eb; padding: 9px 7px; vertical-align: top; }
        .right { text-align: right; }
        .actions { margin-bottom: 20px; }
        .actions a { color: #1d4ed8; margin-right: 16px; }
        @media print { body { background: white; } main { margin: 0; padding: 0; } .actions { display: none; } }
    </style>
</head>
<body>
<main>
    <nav class="actions"><a href="{{ route('books.index') }}">Kembali</a><a href="{{ route('books.archive.pdf', $book) }}">Unduh PDF</a></nav>
    <h1>Arsip Laporan Keuangan</h1>
    <p>Periode {{ $book->starts_at->format('d/m/Y') }} – {{ $book->ends_at->format('d/m/Y') }}</p>
    @if (isset($snapshot['legacy_summary']))
        <div class="summary">{{ $snapshot['legacy_summary'] }}</div>
        @foreach ($snapshot['legacy_tables'] ?? [] as $rows)
            <h2>Rincian tersimpan</h2>
            <table>
                @foreach ($rows as $cells)
                    <tr>@foreach ($cells as $cell)<td>{{ $cell }}</td>@endforeach</tr>
                @endforeach
            </table>
        @endforeach
    @else
        @php($metrics = $snapshot['metrics'] ?? [])
        <div class="summary">Jumlah saldo<br><strong>Rp {{ number_format($metrics['totalSaldo'] ?? 0, 0, ',', '.') }}</strong></div>
        <h2>Ringkasan</h2>
        <table>
            <thead><tr><th>Komponen</th><th class="right">Jumlah (Rp)</th></tr></thead>
            <tbody>
            @foreach ([
                'Saldo awal' => 'openingBalance', 'Pembayaran piutang buku lama' => 'priorReceipts',
                'Penjualan' => 'totalSales', 'Sewa' => 'totalRentalSales',
                'Profit' => 'totalProfit', 'Jasa servis' => 'totalServices',
                'Penambahan modal' => 'totalPenambahanModal', 'Pengeluaran' => 'totalExpenses',
                'Pembelian tukar tambah' => 'totalTradeInPurchases', 'Cicilan modal' => 'totalCicilan',
                'Gaji karyawan' => 'totalGajiKaryawan', 'Piutang' => 'totalPiutang',
            ] as $label => $key)
                @continue($key === 'openingBalance' && (float) ($metrics[$key] ?? 0) === 0.0)
                <tr><td>{{ $label }}</td><td class="right">{{ number_format($metrics[$key] ?? 0, 0, ',', '.') }}</td></tr>
            @endforeach
            </tbody>
        </table>
    @endif
    @if (!empty($snapshot['transactions']))
        <h2>Rincian Transaksi</h2>
        <table>
            <thead><tr><th>Invoice</th><th>Sumber</th><th>Tanggal</th><th class="right">Total</th><th class="right">Profit</th><th>Status</th></tr></thead>
            <tbody>
            @foreach ($snapshot['transactions'] as $row)
                <tr>
                    <td>{{ $row['invoice_number'] }}</td><td>{{ $row['source'] }}</td><td>{{ $row['date'] }}</td>
                    <td class="right">{{ number_format($row['grand_total'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['benefit'], 0, ',', '.') }}</td>
                    <td>{{ $row['payment_status'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</main>
</body>
</html>
