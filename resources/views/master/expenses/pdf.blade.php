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
            color: #000;
            background: #fff;
        }

        /* ── HEADER ── */
        .header {
            padding: 20px 24px 14px;
            border-bottom: 2px solid #000;
            margin-bottom: 0;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
            padding: 0;
        }

        .header .right {
            text-align: right;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
        }

        .header .company {
            font-size: 13px;
            font-weight: bold;
        }

        .header .sub {
            font-size: 10px;
            color: #444;
            margin-top: 3px;
        }

        /* ── META & SUMMARY ── */
        .meta {
            padding: 10px 24px;
            border-bottom: 1px solid #ccc;
        }

        .meta table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 2px 0;
            vertical-align: top;
            width: 50%;
            font-size: 11px;
        }

        .meta .label {
            color: #444;
        }

        .meta .value {
            font-weight: bold;
        }

        .summary {
            padding: 8px 24px 10px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 14px;
        }

        .summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            padding: 2px 0;
            font-size: 11px;
        }

        .summary .label {
            color: #444;
            width: 200px;
        }

        .summary .value {
            font-weight: bold;
        }

        /* ── TABLE ── */
        .table-wrap {
            padding: 0 24px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            /* Kunci: biarkan DomPDF auto page-break */
            page-break-inside: auto;
        }

        /* Repeat thead di setiap halaman baru */
        .data-table thead {
            display: table-header-group;
        }

        .data-table tfoot {
            display: table-footer-group;
        }

        .data-table thead th {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10.5px;
        }

        .data-table thead th.right {
            text-align: right;
        }

        .data-table thead th.center {
            text-align: center;
        }

        .data-table tbody td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            vertical-align: top;
        }

        /* Jangan pisah satu baris ke dua halaman */
        .data-table tbody tr {
            page-break-inside: avoid;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #f9f9f9;
        }

        .col-no {
            width: 30px;
            text-align: center;
        }

        .col-date {
            width: 76px;
        }

        .col-cat {
            width: 90px;
        }

        .col-amt {
            text-align: right;
            white-space: nowrap;
        }

        .td-desc {
            font-size: 9.5px;
            color: #666;
            margin-top: 2px;
        }

        .data-table tfoot td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            font-weight: bold;
            background: #f0f0f0;
        }

        .tfoot-label {
            text-align: right;
        }

        .tfoot-total {
            text-align: right;
            white-space: nowrap;
        }

        /* ── FOOTER ── */
        .footer {
            padding: 8px 24px 0;
            margin-top: 12px;
            border-top: 1px solid #ccc;
            font-size: 9.5px;
            color: #666;
        }

        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer td {
            padding: 0;
        }

        .footer .right {
            text-align: right;
        }

        .empty {
            text-align: center;
            padding: 24px;
            color: #888;
            font-style: italic;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="company">Barokah Komputer</div>
                    <h1>Laporan Pengeluaran</h1>
                </td>
                <td class="right">
                    <div class="sub">
                        Periode:
                        @if($from && $to)
                            {{ \Carbon\Carbon::parse($from)->isoFormat('D MMM Y') }} s/d
                            {{ \Carbon\Carbon::parse($to)->isoFormat('D MMM Y') }}
                        @elseif($from)
                            Mulai {{ \Carbon\Carbon::parse($from)->isoFormat('D MMM Y') }}
                        @elseif($to)
                            Sampai {{ \Carbon\Carbon::parse($to)->isoFormat('D MMM Y') }}
                        @else
                            Semua Periode
                        @endif
                    </div>
                    <div class="sub">Dicetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }} WIB</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- META --}}
    <div class="meta">
        <table>
            <tr>
                <td>
                    <span class="label">Dibuat oleh&nbsp;&nbsp;: </span>
                    <span class="value">{{ Auth::user()->name ?? 'Sistem' }}</span>
                </td>
                <td>
                    <span class="label">Jumlah transaksi&nbsp;&nbsp;: </span>
                    <span class="value">{{ $expenses->count() }} transaksi</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- SUMMARY --}}
    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Pengeluaran</td>
                <td class="value">: Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- TABLE — satu tabel, DomPDF atur page break --}}
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-no center">No</th>
                    <th class="col-date">Tanggal</th>
                    <th>Keterangan</th>
                    <th class="col-cat">Kategori</th>
                    <th class="right">Nominal (Rp)</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="4" class="tfoot-label">Total Pengeluaran</td>
                    <td class="tfoot-total">{{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            <tbody>
                @forelse($expenses as $i => $expense)
                    <tr>
                        <td class="col-no" style="text-align:center">{{ $i + 1 }}</td>
                        <td class="col-date">
                            {{ \Carbon\Carbon::parse($expense->entry_date)->isoFormat('D MMM Y') }}
                        </td>
                        <td>
                            {{ $expense->title }}
                            @if($expense->description)
                                <div class="td-desc">
                                    {{ \Illuminate\Support\Str::limit($expense->description, 70) }}
                                </div>
                            @endif
                        </td>
                        <td class="col-cat">{{ $expense->category ?? 'Umum' }}</td>
                        <td class="col-amt">{{ number_format($expense->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            Tidak ada data pengeluaran pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <table>
            <tr>
                <td>Dicetak otomatis oleh sistem.</td>
                <td class="right">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }} WIB</td>
            </tr>
        </table>
    </div>

</body>

</html>