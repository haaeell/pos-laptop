<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Resi {{ $order->order_number }}</title>
    <style>
        @page { margin: 3mm; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #000;
            font-family: DejaVu Sans, sans-serif;
            font-size: 7.5px;
            line-height: 1.25;
        }

        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }

        .label {
            border: 1.5px solid #000;
            width: 100%;
        }

        .row { border-bottom: 1.5px solid #000; }
        .row:last-child { border-bottom: 0; }

        .header { height: 56px; }
        .header td { vertical-align: middle; padding: 5px 6px; }

        .courier-cell { width: 26%; }
        .courier-logo { max-width: 100%; max-height: 30px; object-fit: contain; }
        .courier-mark {
            display: inline-block;
            color: #000;
            font-size: 14px;
            font-weight: 900;
            line-height: .95;
            text-transform: uppercase;
        }
        .courier-sub {
            display: block;
            margin-top: 2px;
            color: #000;
            font-size: 6px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .platform-cell { text-align: center; }
        .platform-mark {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -.5px;
        }
        .platform-url { font-size: 7px; font-weight: 700; }

        .paid-cell { width: 25%; text-align: right; }
        .paid {
            display: inline-block;
            border: 1.5px solid #000;
            padding: 4px 5px;
            font-size: 7px;
            font-weight: 900;
        }

        .waybill { padding: 6px 9px 4px; text-align: center; }
        .barcode > div { margin: 0 auto; }
        .waybill-number { margin-top: 2px; font-size: 10px; }
        .waybill-number strong { font-size: 11px; }

        .service { padding: 5px 8px; text-align: center; font-size: 8px; }
        .service strong { font-size: 8.5px; }

        .split > td { width: 50%; }
        .split > td + td { border-left: 1.5px solid #000; }

        .reference { padding: 5px 6px; height: 58px; }
        .reference-title { font-weight: 700; font-size: 7px; }
        .reference-number { margin-top: 1px; font-size: 7px; }

        .facts { padding: 7px 8px; font-size: 8px; }
        .facts table td { padding-bottom: 7px; }
        .facts table td:first-child { width: 45%; }

        .address { padding: 5px 6px; height: 70px; }
        .address-title { font-size: 7px; font-weight: 700; }
        .address-name { margin-top: 1px; font-weight: 800; font-size: 8px; }
        .address-line { margin-top: 1px; }
        .store-logo-cell { width: 28px; padding-left: 4px; text-align: right; }
        .store-logo { width: 25px; height: 20px; }

        .goods { padding: 5px 6px; min-height: 35px; }
        .goods-title { width: 20%; white-space: nowrap; }
        .goods-list { font-size: 7px; }
        .goods-item { margin-bottom: 2px; }

        .notes { padding: 5px 6px; height: 28px; }
        .notes-title { display: inline-block; width: 20%; }

        .footer { padding: 4px 6px; text-align: center; font-size: 6.5px; }
        .footer strong { display: block; font-size: 7px; }
    </style>
</head>

<body>
    @php
        $storeName = $settings['nama_toko'] ?? 'Barokah Computer';
        $storeAddress = $settings['biteship_origin_address'] ?? ($settings['alamat'] ?? '-');
        $storeContact = $settings['biteship_origin_contact_name'] ?? $storeName;
        $storePhone = $settings['biteship_origin_contact_phone'] ?? '-';
        $waybill = (string) ($order->courier_waybill_id ?: $order->courier_tracking_id ?: $order->order_number);
        $reference = (string) $order->order_number;
        $waybillBarcode = DNS1D::getBarcodeHTML($waybill, 'C128', 1.2, 35, '#000000');
        $referenceBarcode = DNS1D::getBarcodeHTML($reference, 'C128', 1, 25, '#000000');
        $totalQuantity = (int) $order->items->sum('qty');
        $totalWeight = (int) $order->items->sum(fn ($item) => ($item->product?->weight ?? 1000) * $item->qty);
        $weightLabel = $totalWeight >= 1000
            ? rtrim(rtrim(number_format($totalWeight / 1000, 2, '.', ''), '0'), '.') . ' Kg'
            : $totalWeight . ' Gram';
        $serviceName = $order->courier_service_name ?: strtoupper((string) $order->courier_type);
        $routeCode = strtoupper((string) ($order->courier_routing_code ?: '-'));
    @endphp

    <div class="label">
        <div class="row header">
            <table>
                <tr>
                    <td class="courier-cell">
                        @if ($courierLogoPath)
                            <img class="courier-logo" src="{{ $courierLogoPath }}" alt="{{ $courierMeta['label'] }}">
                        @else
                            <span class="courier-mark">{{ $courierMeta['label'] }}</span>
                        @endif
                        <span class="courier-sub">DELIVERY SERVICE</span>
                    </td>
                    <td class="platform-cell">
                        <div class="platform-mark">{{ $storeName }}</div>
                    </td>
                    <td class="paid-cell">
                        <span class="paid">SUDAH DIBAYAR</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="row waybill">
            <div class="barcode">{!! $waybillBarcode !!}</div>
            <div class="waybill-number">Nomor Resi - <strong>{{ $waybill }}</strong></div>
        </div>

        <div class="row service">
            <div>Ongkos Kirim: <strong>Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</strong></div>
            <div>Jenis Layanan - <strong>{{ $serviceName ?: '-' }}</strong> &nbsp; Kode Rute - <strong>{{ $routeCode }}</strong></div>
        </div>

        <div class="row">
            <table class="split">
                <tr>
                    <td class="reference">
                        <div class="reference-title">Reference Number</div>
                        <div class="barcode">{!! $referenceBarcode !!}</div>
                        <div class="reference-number">{{ $reference }}</div>
                    </td>
                    <td class="facts">
                        <table>
                            <tr><td>Quantity:</td><td><strong>{{ $totalQuantity }} Pcs</strong></td></tr>
                            <tr><td>Weight:</td><td><strong>{{ $weightLabel }}</strong></td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="row">
            <table class="split">
                <tr>
                    <td class="address">
                        <div class="address-title">Alamat Penerima:</div>
                        <div class="address-name">{{ $order->recipient_name }}</div>
                        <div>{{ $order->recipient_phone }}</div>
                        <div class="address-line">{{ $order->address_detail }}</div>
                        <div class="address-line">{{ collect([$order->district, $order->city, $order->province])->filter()->join(', ') }}</div>
                    </td>
                    <td class="address">
                        <table>
                            <tr>
                                <td>
                                    <div class="address-title">Alamat Pengirim:</div>
                                    <div class="address-name">{{ $storeContact }}</div>
                                    <div>{{ $storePhone }}</div>
                                    <div class="address-line">{{ $storeAddress }}</div>
                                </td>
                                @if ($logoPath)
                                    <td class="store-logo-cell"><img class="store-logo" src="{{ $logoPath }}" width="25" height="20" alt="Logo toko"></td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="row goods">
            <table>
                <tr>
                    <td class="goods-title">Jenis Barang :</td>
                    <td class="goods-list">
                        @foreach ($order->items->take(4) as $item)
                            <div class="goods-item"><strong>{{ $item->qty }}x</strong> {{ $item->product_name }} - Goods</div>
                        @endforeach
                        @if ($order->items->count() > 4)
                            <div>+ {{ $order->items->count() - 4 }} barang lainnya</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="row notes">
            <span class="notes-title">Catatan :</span>
            <span>{{ $order->notes ?: 'Tidak Ada' }}</span>
        </div>

        <div class="footer">
            Terima kasih telah berbelanja
            <strong>{{ $storeName }}</strong>
        </div>
    </div>
</body>

</html>
