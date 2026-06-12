<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Produk</title>
    <style>
        @page {
            margin: 24px 26px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #0f172a;
            margin: 0;
            background: #ffffff;
        }

        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 14px;
            margin-bottom: 16px;
        }

        .header table,
        .meta table,
        .summary table,
        .data-table,
        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .store-name {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.4px;
            color: #111827;
        }

        .report-title {
            margin-top: 6px;
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
        }

        .store-meta,
        .print-meta {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.5;
        }

        .print-meta {
            text-align: right;
        }

        .meta,
        .summary {
            margin-bottom: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }

        .meta td,
        .summary td {
            padding: 8px 10px;
            vertical-align: top;
        }

        .meta .label,
        .summary .label {
            color: #64748b;
            width: 140px;
        }

        .meta .value,
        .summary .value {
            font-weight: 700;
            color: #0f172a;
        }

        .data-table {
            table-layout: fixed;
            page-break-inside: auto;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tbody tr {
            page-break-inside: avoid;
        }

        .data-table th {
            background: #e2e8f0;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            text-align: left;
        }

        .data-table td {
            border: 1px solid #dbe4ee;
            padding: 7px 6px;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        .name {
            font-weight: 700;
            color: #111827;
        }

        .muted {
            color: #64748b;
            font-size: 9px;
            margin-top: 2px;
        }

        .description {
            color: #475569;
            font-size: 8.8px;
            line-height: 1.45;
            margin-top: 4px;
            white-space: pre-line;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 8.5px;
            font-weight: 700;
        }

        .badge-available,
        .badge-show {
            background: #dcfce7;
            color: #166534;
        }

        .badge-sold {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-bonus {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-hide {
            background: #e2e8f0;
            color: #475569;
        }

        .empty {
            text-align: center;
            padding: 24px 12px;
            color: #64748b;
            font-style: italic;
        }

        .footer {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e1;
            font-size: 9px;
            color: #64748b;
        }

        .footer .right {
            text-align: right;
        }
    </style>
</head>

<body>
    @php
        use Carbon\Carbon;

        Carbon::setLocale('id');

        $namaToko = $settings['nama_toko'] ?? 'Barokah Computer';
        $alamat = $settings['alamat'] ?? 'Alamat toko belum diatur';
        $statusLabels = [
            'available' => 'Tersedia',
            'sold' => 'Terjual',
            'bonus' => 'Bonus',
        ];
        $catalogLabels = [
            'active' => 'Produk tampil di katalog',
            'inactive' => 'Disembunyikan dari katalog',
        ];
        $activeCount = $products->where('is_active', true)->count();
        $inactiveCount = $products->where('is_active', false)->count();
    @endphp

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="store-name">{{ $namaToko }}</div>
                    <div class="report-title">Laporan Data Produk</div>
                    <div class="store-meta">
                        {{ $alamat }}<br>
                        @if($contacts->count())
                            {{ $contacts->pluck('label')->zip($contacts->pluck('phone'))->map(fn($c) => $c[0] . ': ' . $c[1])->implode(' | ') }}
                        @endif
                    </div>
                </td>
                <td class="print-meta">
                    Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB<br>
                    Total data: {{ $products->count() }} produk
                </td>
            </tr>
        </table>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td>
                    <span class="label">Kategori</span><br>
                    <span class="value">{{ $filters['category'] ?: 'Semua kategori' }}</span>
                </td>
                <td>
                    <span class="label">Brand</span><br>
                    <span class="value">{{ $filters['brand'] ?: 'Semua brand' }}</span>
                </td>
                <td>
                    <span class="label">Status produk</span><br>
                    <span
                        class="value">{{ $filters['status'] ? ($statusLabels[$filters['status']] ?? $filters['status']) : 'Semua status' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td>
                    <span class="label">Nilai jual total</span><br>
                    <span class="value">Rp {{ number_format($products->sum('selling_price'), 0, ',', '.') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 34px;" class="text-center">No</th>
                <th style="width: 86px;">Kode</th>
                <th>Produk</th>
                <th style="width: 92px;">Kategori</th>
                <th style="width: 86px;">Brand</th>
                <th style="width: 58px;" class="text-center">Kondisi</th>
                <th style="width: 76px;" class="text-right">Harga Jual</th>
                <th style="width: 70px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="nowrap">{{ $product->product_code }}</td>
                    <td>
                        <div class="name">{{ $product->name }}</div>
                        @if($product->description)
                            <div class="description">
                                {{ trim(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $product->description))) }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $product->category?->name ?? '-' }}</td>
                    <td>{{ $product->brand?->name ?? '-' }}</td>
                    <td class="text-center">{{ $product->condition === 'new' ? 'Baru' : 'Bekas' }}</td>
                    <td class="text-right nowrap">{{ number_format($product->selling_price, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span
                            class="badge {{ $product->status === 'sold' ? 'badge-sold' : ($product->status === 'bonus' ? 'badge-bonus' : 'badge-available') }}">
                            {{ $statusLabels[$product->status] ?? $product->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty">Tidak ada data produk yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td>Laporan ini tidak menampilkan harga beli produk.</td>
                <td class="right">Dokumen dibuat otomatis oleh sistem.</td>
            </tr>
        </table>
    </div>
</body>

</html>