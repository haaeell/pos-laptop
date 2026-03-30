<table>
    {{-- HEADER --}}
    <tr>
        <td colspan="6" style="font-size:18px; font-weight:bold; text-align:center; padding:8px 0;">
            LAPORAN PENJUALAN
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align:center; color:#6b7280; padding-bottom:4px;">
            Periode {{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="6" style="padding:6px 0;"></td>
    </tr>

    {{-- TABLE HEADER --}}
    <tr>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px; text-align:center;">#</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px;">Invoice</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px;">Tanggal</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px; text-align:right;">Grand Total</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px; text-align:right;">Profit</th>
        <th style="background:#f3f4f6; border:1px solid #d1d5db; padding:6px 10px;">Pembayaran</th>
    </tr>

    {{-- DATA --}}
    @foreach ($sales as $i => $sale)
        <tr>
            <td style="border:1px solid #e5e7eb; padding:5px 10px; text-align:center;">{{ $i + 1 }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px;">{{ $sale->invoice_number }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px;">{{ $sale->created_at->format('d M Y H:i') }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px; text-align:right;">Rp
                {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px; text-align:right;">Rp
                {{ number_format($sale->benefit, 0, ',', '.') }}</td>
            <td style="border:1px solid #e5e7eb; padding:5px 10px;">{{ strtoupper($sale->payment_method) }}</td>
        </tr>
    @endforeach

    {{-- SPACER --}}
    <tr>
        <td colspan="6" style="padding:6px 0;"></td>
    </tr>

    {{-- SUMMARY --}}
    <tr>
        <td colspan="3" style="padding:4px 10px; text-align:right; color:#6b7280;">Total Profit</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold;">Rp
            {{ number_format($totalProfit, 0, ',', '.') }}</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="3" style="padding:4px 10px; text-align:right; color:#e11d48;">Bonus / Loss</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold; color:#e11d48;">Rp
            {{ number_format($bonusLoss, 0, ',', '.') }}</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="3" style="padding:4px 10px; text-align:right; color:#6b7280;">Total Penjualan</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold;">Rp
            {{ number_format($totalSales, 0, ',', '.') }}</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="3" style="padding:4px 10px; text-align:right; color:#ea580c;">Total Pengeluaran</td>
        <td style="padding:4px 10px; text-align:right; font-weight:bold; color:#ea580c;">- Rp
            {{ number_format($totalExpenses, 0, ',', '.') }}</td>
        <td colspan="2"></td>
    </tr>
    <tr style="background:#eef2ff;">
        <td colspan="3" style="padding:6px 10px; text-align:right; font-weight:bold;">Jumlah Saldo</td>
        <td style="padding:6px 10px; text-align:right; font-weight:bold;">Rp
            {{ number_format($totalSales - $totalExpenses, 0, ',', '.') }}</td>
        <td colspan="2"></td>
    </tr>
</table>