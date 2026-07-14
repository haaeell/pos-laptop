<table>
    {{-- HEADER --}}
    <tr>
        <td colspan="9" style="font-size:18px; font-weight:bold; text-align:center; padding:8px 0;">
            LAPORAN PENJUALAN
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align:center; color:#6b7280; padding-bottom:4px;">
            Periode {{ $from->format('d M Y') }} - {{ $to->format('d M Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="9" style="padding:6px 0;"></td>
    </tr>

    {{-- TABLE HEADER --}}
    <tr>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px; text-align:center;">#</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px;">Invoice</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px;">Sumber</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px;">Tanggal</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px; text-align:right;">Grand Total</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px; text-align:right;">Profit</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px;">Pembayaran</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px;">Status</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px; text-align:right;">Sisa Tagihan</th>
    </tr>

    {{-- DATA --}}
    @foreach ($transactions as $i => $sale)
        @php
            $statusLabel = match ($sale->payment_status) {
                'paid' => 'LUNAS',
                'partial' => 'SEBAGIAN',
                default => 'HUTANG',
            };
        @endphp
        <tr>
            <td style="border:1px solid #e5e7eb; padding:5px 10px; text-align:center;">{{ $i + 1 }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px;">{{ $sale->invoice_number }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px;">{{ strtoupper($sale->source) }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px;">{{ $sale->date->format('d M Y H:i') }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px; text-align:right;">Rp
                {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px; text-align:right;">Rp
                {{ number_format($sale->benefit, 0, ',', '.') }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px;">{{ strtoupper($sale->payment_method) }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px;">{{ $statusLabel }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px; text-align:right;">
                {{ $sale->payment_status !== 'paid' ? 'Rp ' . number_format($sale->remaining_amount, 0, ',', '.') : '-' }}
            </td>
        </tr>
    @endforeach

    {{-- SPACER --}}
    <tr>
        <td colspan="9" style="padding:6px 0;"></td>
    </tr>

    {{-- SUMMARY --}}
    <tr>
        <td colspan="4" style="padding:4px 10px; text-align:right; color:#6b7280;">Total Profit</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold;">Rp
            {{ number_format($totalProfit, 0, ',', '.') }}</td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4" style="padding:4px 10px; text-align:right; color:#e11d48;">Bonus / Loss</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold; color:#e11d48;">Rp
            {{ number_format($bonusLoss, 0, ',', '.') }}</td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4" style="padding:4px 10px; text-align:right; color:#6b7280;">Total Penjualan</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold;">Rp
            {{ number_format($totalSales, 0, ',', '.') }}</td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4" style="padding:4px 10px; text-align:right; color:#2563eb;">Penjualan Online</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold; color:#2563eb;">Rp
            {{ number_format($totalOnlineSales, 0, ',', '.') }}</td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4" style="padding:4px 10px; text-align:right; color:#b91c1c;">Piutang / Belum Tertagih</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold; color:#b91c1c;">- Rp
            {{ number_format($totalPiutang, 0, ',', '.') }}</td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4" style="padding:4px 10px; text-align:right; color:#6b7280;">Kas Diterima (Penjualan)</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold;">Rp
            {{ number_format($totalDiterima, 0, ',', '.') }}</td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4" style="padding:4px 10px; text-align:right; color:#ea580c;">Total Pengeluaran</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold; color:#ea580c;">- Rp
            {{ number_format($totalExpenses, 0, ',', '.') }}</td>
        <td colspan="4"></td>
    </tr>
    <tr style="background:#eef2ff;">
        <td colspan="4" style="padding:6px 10px; text-align:right; font-weight:bold;">Jumlah Saldo</td>
        <td style="padding:6px 10px; text-align:right; font-weight:bold;">Rp
            {{ number_format($totalDiterima - $totalExpenses, 0, ',', '.') }}</td>
        <td colspan="4"></td>
    </tr>
</table>
