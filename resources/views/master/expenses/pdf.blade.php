<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
        }

        /* ---- HEADER ---- */
        .header {
            background: #4f46e5;
            color: white;
            padding: 20px 28px 18px;
        }

        .header-inner {
            width: 100%;
            border-collapse: collapse;
        }

        .header-inner td {
            vertical-align: middle;
            padding: 0;
        }

        .header-inner .header-right {
            text-align: right;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        .header p {
            font-size: 10px;
            margin-top: 4px;
            opacity: 0.8;
        }

        .header-badge {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.18);
            padding: 4px 10px;
            color: #fff;
        }

        /* ---- META INFO ---- */
        .meta {
            margin: 14px 28px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 10px 14px;
            width: 50%;
            vertical-align: top;
        }

        .meta-table td+td {
            border-left: 1px solid #e2e8f0;
        }

        .meta-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 11px;
            font-weight: 600;
            color: #1e293b;
        }

        /* ---- SUMMARY CARDS ---- */
        .summary {
            margin: 0 28px 14px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .summary-table td:first-child {
            padding-right: 7px;
        }

        .summary-table td:last-child {
            padding-left: 7px;
        }

        .summary-card {
            padding: 12px 14px;
        }

        .card-total {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .card-count {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .card-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-total .card-label {
            color: #dc2626;
        }

        .card-count .card-label {
            color: #64748b;
        }

        .card-value {
            font-size: 16px;
            font-weight: 700;
            margin-top: 5px;
        }

        .card-total .card-value {
            color: #b91c1c;
        }

        .card-count .card-value {
            color: #334155;
        }

        /* ---- TABLE ---- */
        .table-wrap {
            margin: 0 28px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            background: #4f46e5;
            color: white;
        }

        .data-table thead th {
            padding: 8px 8px;
            text-align: left;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table tbody tr.even {
            background: #f8fafc;
        }

        .data-table tbody tr.odd {
            background: #ffffff;
        }

        .data-table tbody td {
            padding: 7px 8px;
            font-size: 10.5px;
            vertical-align: top;
        }

        /* Kolom lebar eksplisit */
        .col-no {
            width: 26px;
            text-align: center;
            color: #94a3b8;
        }

        .col-date {
            width: 70px;
            color: #475569;
            white-space: nowrap;
        }

        .col-title {
            color: #1e293b;
            font-weight: 600;
        }

        .col-category {
            width: 88px;
        }

        .col-amount {
            width: 108px;
            text-align: right;
            color: #dc2626;
            font-weight: 700;
            white-space: nowrap;
        }

        .td-desc {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 400;
            margin-top: 2px;
        }

        .td-badge {
            display: inline-block;
            padding: 2px 7px;
            background: #e2e8f0;
            font-size: 8.5px;
            font-weight: 700;
            color: #475569;
        }

        /* TFOOT */
        .data-table tfoot tr {
            background: #fef2f2;
        }

        .data-table tfoot td {
            padding: 9px 8px;
            font-weight: 700;
            font-size: 11px;
            border-top: 2px solid #fecaca;
        }

        .tfoot-label {
            color: #991b1b;
            text-align: right;
        }

        .tfoot-total {
            color: #b91c1c;
            text-align: right;
            white-space: nowrap;
        }

        /* ---- PAGE BREAK ---- */
        .page-break {
            page-break-before: always;
        }

        /* Header ulang di halaman ke-2+ */
        .header-repeat {
            background: #4f46e5;
            color: white;
            padding: 12px 28px;
            margin-bottom: 10px;
        }

        .header-repeat p {
            font-size: 9px;
            opacity: 0.8;
        }

        .header-repeat h2 {
            font-size: 13px;
            font-weight: 700;
        }

        /* ---- FOOTER ---- */
        .footer {
            margin: 14px 28px 0;
            padding-top: 9px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            padding: 0;
            font-size: 9px;
            color: #94a3b8;
            vertical-align: middle;
        }

        .footer-right {
            text-align: right;
        }

        /* ---- EMPTY STATE ---- */
        .empty {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
            font-size: 11px;
        }
    </style>
</head>

<body>

    {{-- ===================== HEADER ===================== --}}
    <div class="header">
        <table class="header-inner">
            <tr>
                <td>
                    <h1>Laporan Pengeluaran</h1>
                    <p>Dicetak pada {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y — HH:mm') }} WIB</p>
                </td>
                <td class="header-right">
                    <span class="header-badge">Halaman 1</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===================== META INFO ===================== --}}
    <div class="meta">
        <table class="meta-table">
            <tr>
                <td>
                    <div class="meta-label">Periode</div>
                    <div class="meta-value">
                        @if($from && $to)
                            {{ \Carbon\Carbon::parse($from)->isoFormat('D MMM Y') }} —
                            {{ \Carbon\Carbon::parse($to)->isoFormat('D MMM Y') }}
                        @elseif($from)
                            Mulai {{ \Carbon\Carbon::parse($from)->isoFormat('D MMM Y') }}
                        @elseif($to)
                            Sampai {{ \Carbon\Carbon::parse($to)->isoFormat('D MMM Y') }}
                        @else
                            Semua Periode
                        @endif
                    </div>
                </td>
                <td>
                    <div class="meta-label">Dibuat oleh</div>
                    <div class="meta-value">{{ Auth::user()->name ?? 'Sistem' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===================== SUMMARY ===================== --}}
    <div class="summary">
        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-card card-total">
                        <div class="card-label">Total Pengeluaran</div>
                        <div class="card-value">Rp {{ number_format($total, 0, ',', '.') }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-card card-count">
                        <div class="card-label">Jumlah Transaksi</div>
                        <div class="card-value">{{ $expenses->count() }} Transaksi</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===================== TABLE (chunked per page) ===================== --}}
    @php
        // Halaman pertama lebih sedikit baris karena ada header + meta + summary
        $firstPageRows = 18;
        $otherPageRows = 28;

        $firstChunk = $expenses->slice(0, $firstPageRows);
        $remaining = $expenses->slice($firstPageRows);
        $otherChunks = $remaining->chunk($otherPageRows);

        $allChunks = collect([$firstChunk])->merge($otherChunks);
        $totalChunks = $allChunks->count();
    @endphp

    @foreach($allChunks as $chunkIndex => $chunk)

        {{-- Page break + repeat header untuk halaman ke-2 dst --}}
        @if($chunkIndex > 0)
            <div class="page-break"></div>
            <div class="header-repeat">
                <table class="header-inner">
                    <tr>
                        <td>
                            <h2>Laporan Pengeluaran</h2>
                            <p>
                                @if($from && $to)
                                    Periode {{ \Carbon\Carbon::parse($from)->isoFormat('D MMM Y') }} —
                                    {{ \Carbon\Carbon::parse($to)->isoFormat('D MMM Y') }}
                                @else
                                    Semua Periode
                                @endif
                            </p>
                        </td>
                        <td class="header-right">
                            <span class="header-badge">Halaman {{ $chunkIndex + 1 }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th class="col-date">Tanggal</th>
                        <th>Judul</th>
                        <th class="col-category">Kategori</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chunk as $expense)
                        @php
                            $nomor = $expenses->search(fn($e) => $e->id === $expense->id) + 1;
                            $rowClass = $nomor % 2 === 0 ? 'even' : 'odd';
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="col-no">{{ $nomor }}</td>
                            <td class="col-date">
                                {{ \Carbon\Carbon::parse($expense->entry_date)->isoFormat('D MMM Y') }}
                            </td>
                            <td class="col-title">
                                {{ $expense->title }}
                                @if($expense->description)
                                    <div class="td-desc">
                                        {{ \Illuminate\Support\Str::limit($expense->description, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="td-badge">{{ $expense->category ?? 'Umum' }}</span>
                            </td>
                            <td>
                                Rp {{ number_format($expense->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">
                                Tidak ada data pengeluaran pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Total hanya di chunk terakhir --}}
                @if($loop->last && $expenses->count() > 0)
                    <tfoot>
                        <tr>
                            <td colspan="4" class="tfoot-label">TOTAL PENGELUARAN</td>
                            <td class="tfoot-total">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Footer di setiap halaman --}}
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td>Laporan ini dibuat secara otomatis oleh sistem.</td>
                    <td class="footer-right">
                        Halaman {{ $chunkIndex + 1 }} dari {{ $totalChunks }}
                    </td>
                </tr>
            </table>
        </div>

    @endforeach

</body>

</html>