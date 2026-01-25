<table>
    {{-- TITLE --}}
    <tr>
        <td colspan="6" style="font-size:16px;font-weight:bold;text-align:center;padding:8px;">
            LAPORAN PENJUALAN
        </td>
    </tr>

    <tr>
        <td colspan="6" style="text-align:center;font-size:11px;color:#555;padding-bottom:12px;">
            Periode {{ \Carbon\Carbon::parse(request()->from)->format('d M Y') }} s/d
            {{ \Carbon\Carbon::parse(request()->to)->format('d M Y')  }}
        </td>
    </tr>

    {{-- SPACER --}}
    <tr>
        <td colspan="6"></td>
    </tr>

    {{-- TABLE HEADER --}}
    <tr>
        <th style="border:1px solid #000;background:#f3f4f6;font-weight:bold;width:40px;text-align:center;">
            No
        </th>
        <th style="border:1px solid #000;background:#f3f4f6;font-weight:bold;width:160px;">
            Invoice
        </th>
        <th style="border:1px solid #000;background:#f3f4f6;font-weight:bold;width:120px;">
            Tanggal
        </th>
        <th style="border:1px solid #000;background:#f3f4f6;font-weight:bold;width:140px;text-align:right;">
            Grand Total
        </th>
        <th style="border:1px solid #000;background:#f3f4f6;font-weight:bold;width:120px;text-align:right;">
            Profit
        </th>
        <th style="border:1px solid #000;background:#f3f4f6;font-weight:bold;width:120px;text-align:center;">
            Pembayaran
        </th>
    </tr>

    {{-- DATA --}}
    @php
        $totalGrand = 0;
        $totalProfit = 0;
    @endphp
    @foreach ($sales as $i => $sale)
        <tr>
            <td style="border:1px solid #000;text-align:center;">
                {{ $i + 1 }}
            </td>
            <td style="border:1px solid #000;">
                {{ $sale->invoice_number }}
            </td>
            <td style="border:1px solid #000;">
                {{ $sale->created_at->format('Y-m-d') }}
            </td>
            <td style="border:1px solid #000;text-align:right;">
                {{ $sale->grand_total }}
            </td>
            <td style="border:1px solid #000;text-align:right;">
                {{ $sale->benefit }}
            </td>
            <td style="border:1px solid #000;text-align:center;">
                {{ strtoupper($sale->payment_method) }}
            </td>
        </tr>

        @php
            $totalGrand += $sale->grand_total;
            $totalProfit += $sale->benefit;
        @endphp
    @endforeach

    <tr>
        <td colspan="3" style="text-align:center;font-weight:bold;">TOTAL</td>
        <td style="text-align:right;font-weight:bold;">{{ number_format($totalGrand, 0, ',', '.') }}</td>
        <td style="text-align:right;font-weight:bold;">{{ number_format($totalProfit, 0, ',', '.') }}</td>
        <td></td>
    </tr>
</table>