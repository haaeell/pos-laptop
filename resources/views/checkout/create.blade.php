@extends('layouts.catalog')

@section('title', 'Checkout | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        @media(max-width:640px) {
            body {
                padding-bottom: 156px !important;
            }
        }

        #waFloat {
            display: none !important;
        }

        footer {
            display: none !important;
        }

        .checkout-section {
            padding: 24px 0 170px;
        }

        .checkout-wrap {
            max-width: 640px;
            margin: 0 auto;
        }

        .checkout-summary-sidebar .checkout-card h3 {
            font-size: 14.5px;
            margin-bottom: 14px;
        }

        .checkout-summary-submit-btn {
            display: none;
        }

        .checkout-summary-submit-btn {
            width: 100%;
            margin-top: 16px;
        }

        @media(min-width:900px) {
            .checkout-section {
                padding-bottom: 44px;
            }

            .checkout-wrap {
                max-width: 1100px;
            }

            .checkout-grid {
                display: grid;
                grid-template-columns: 1fr 340px;
                gap: 24px;
                align-items: start;
            }

            .checkout-summary-sidebar {
                align-self: start;
            }

            .checkout-bottom-bar {
                display: none;
            }

            .checkout-summary-submit-btn {
                display: block;
            }
        }

        .checkout-section h1 {
            font-size: 21px;
            margin-bottom: 18px;
        }

        .checkout-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 14px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
        }

        .checkout-card h3 {
            font-size: 14.5px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkout-item-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 9px 0;
            border-bottom: 1px dashed var(--line);
            gap: 10px;
        }

        .checkout-item-row:last-child {
            border-bottom: 0;
        }

        .checkout-product-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .checkout-product-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: linear-gradient(180deg, #ffffff, #fbfdff);
        }

        .checkout-product-thumb {
            width: 72px;
            height: 72px;
            border-radius: 14px;
            overflow: hidden;
            background: #f1f5f9;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            color: #cbd5e1;
        }

        .checkout-product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .checkout-product-copy {
            flex: 1;
            min-width: 0;
        }

        .checkout-product-title {
            display: block;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.45;
            margin-bottom: 4px;
            color: var(--text);
        }

        .checkout-product-meta {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .checkout-product-pricing {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .checkout-product-unit-price {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .checkout-product-strike {
            font-size: 11.5px;
            color: #98a2b3;
            text-decoration: line-through;
        }

        .checkout-product-price {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
        }

        .checkout-product-subtotal {
            text-align: right;
        }

        .checkout-product-subtotal-label {
            display: block;
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 2px;
        }

        .checkout-product-subtotal-value {
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
        }

        /* --- selected address summary card --- */
        .addr-summary-card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .addr-summary-empty {
            font-size: 13px;
            color: var(--muted);
        }

        .addr-summary-body strong {
            display: block;
            font-size: 13.5px;
            margin-bottom: 4px;
        }

        .addr-summary-body p {
            font-size: 12.5px;
            color: var(--muted);
            margin: 0;
        }

        .addr-change-btn {
            border: 1px solid var(--primary);
            color: var(--primary);
            background: var(--primary-soft);
            font-size: 12px;
            font-weight: 700;
            padding: 7px 12px;
            border-radius: 999px;
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* --- courier list --- */
        .courier-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .courier-card {
            border: 1px solid #dbe4ef;
            border-radius: 18px;
            padding: 14px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            background: linear-gradient(180deg, #ffffff, #fcfdff);
            transition: .2s ease;
        }

        .courier-card.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft), 0 16px 30px rgba(23, 92, 211, .12);
        }

        .courier-card:hover {
            transform: translateY(-2px);
            border-color: #bfd3fb;
        }

        .courier-card-main {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 0;
            flex: 1;
        }

        .courier-card-logo {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e5edf6;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            overflow: hidden;
            color: #94a3b8;
        }

        .courier-card-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 7px;
        }

        .courier-card-copy {
            min-width: 0;
        }

        .courier-card .name {
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .courier-card .duration {
            font-size: 12px;
            color: var(--muted);
        }

        .courier-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .courier-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
            font-size: 11px;
            font-weight: 700;
        }

        .courier-card-side {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .courier-card .price {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
            white-space: nowrap;
        }

        .courier-card-check {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 1.5px solid #cbd5e1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            background: #fff;
            transition: .2s ease;
        }

        .courier-card.selected .courier-card-check {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
        }

        .courier-hint {
            font-size: 12.5px;
            color: var(--muted);
            padding: 10px 0;
        }

        .delivery-options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .delivery-option {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: 196px;
            border: 1px solid #dbe4ef;
            border-radius: 20px;
            padding: 16px;
            cursor: pointer;
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, .08), transparent 34%),
                linear-gradient(180deg, #ffffff, #f8fbff);
            transition: .22s ease;
            overflow: hidden;
        }

        .delivery-option:hover {
            transform: translateY(-2px);
            border-color: #bfd3fb;
            box-shadow: 0 18px 34px rgba(23, 92, 211, .08);
        }

        .delivery-option.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft), 0 20px 38px rgba(23, 92, 211, .14);
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, .16), transparent 36%),
                linear-gradient(180deg, #ffffff, #f4f8ff);
        }

        .delivery-option-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .delivery-option-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef4ff;
            color: var(--primary);
            font-size: 20px;
            box-shadow: inset 0 0 0 1px rgba(23, 92, 211, .08);
        }

        .delivery-option.selected .delivery-option-icon {
            background: linear-gradient(135deg, #175CD3, #3B82F6);
            color: #fff;
            box-shadow: 0 12px 24px rgba(23, 92, 211, .22);
        }

        .delivery-option-check {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 1.5px solid #cbd5e1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            background: #fff;
            transition: .2s ease;
            flex-shrink: 0;
        }

        .delivery-option.selected .delivery-option-check {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
        }

        .delivery-option-copy strong {
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
            line-height: 1.3;
        }

        .delivery-option-copy span {
            display: block;
            font-size: 12.5px;
            color: var(--muted);
            line-height: 1.6;
        }

        .delivery-option-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: auto;
        }

        .delivery-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 30px;
            padding: 0 10px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #dbe4ef;
            color: #475467;
            font-size: 11.5px;
            font-weight: 700;
        }

        .delivery-option.selected .delivery-badge {
            border-color: #bfd3fb;
            color: var(--primary);
            background: #f8fbff;
        }

        .delivery-heading-copy p {
            margin-top: 6px;
            font-size: 12.5px;
            color: var(--muted);
        }

        .delivery-note {
            margin-top: 12px;
            font-size: 12.5px;
            color: var(--muted);
            line-height: 1.6;
            padding: 12px 14px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
        }

        .pickup-map-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .pickup-map-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid #dbe4ef;
            background: #fff;
            color: var(--text);
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
        }

        .pickup-map-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #f8fbff;
        }

        .referral-inline {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .referral-inline .form-group {
            flex: 1;
            min-width: 220px;
        }

        .referral-apply-btn {
            border: 1px solid var(--primary);
            background: var(--primary-soft);
            color: var(--primary);
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
        }

        .referral-apply-btn:hover {
            background: #dbeafe;
        }

        .referral-note-box {
            margin-top: 12px;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 12.5px;
            line-height: 1.55;
            display: none;
        }

        .referral-note-box.success {
            display: block;
            background: #ecfdf3;
            border: 1px solid #abefc6;
            color: #067647;
        }

        .referral-note-box.error {
            display: block;
            background: #fef3f2;
            border: 1px solid #fecdca;
            color: #b42318;
        }

        .delivery-hidden {
            display: none;
        }

        .shipping-trust-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 700;
            color: #16a34a;
            margin: 4px 0 10px;
        }

        .courier-selected-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 10px;
            background: #f8fafc;
        }

        .courier-selected-logo img {
            height: 28px;
            max-width: 90px;
            object-fit: contain;
        }

        .courier-selected-logo span {
            font-size: 13px;
            font-weight: 700;
            color: var(--muted);
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group textarea,
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 13.5px;
            outline: none;
            background: #fff;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
        }

        .coord-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 12px;
        }

        .coord-input {
            background: #F8FAFC !important;
            color: #475467;
        }

        .map-picker {
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            margin-top: 14px;
        }

        .map-picker-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
            flex-wrap: wrap;
        }

        .map-picker-head strong {
            display: block;
            font-size: 13px;
        }

        .map-picker-head span {
            font-size: 11.5px;
            color: var(--muted);
        }

        .map-picker-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .map-picker-btn {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
        }

        .map-picker-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .leaflet-map {
            height: 260px;
        }

        .map-picker-note {
            padding: 10px 14px 14px;
            font-size: 11.5px;
            color: var(--muted);
        }

        /* --- sticky bottom bar, Shopee-style --- */
        .checkout-bottom-bar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 86px;
            background: #fff;
            border-top: 1px solid var(--line);
            box-shadow: 0 -6px 20px rgba(16, 24, 40, .06);
            z-index: 60;
            padding: 12px 16px;
            padding-bottom: 12px;
        }

        .checkout-bottom-bar-inner {
            max-width: 640px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .checkout-bottom-total-label {
            font-size: 11px;
            color: var(--muted);
        }

        .checkout-bottom-total-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
        }

        .checkout-bottom-bar .btn {
            padding: 13px 26px;
            font-size: 13.5px;
            white-space: nowrap;
        }

        .summary-kicker {
            display: block;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #98a2b3;
            margin-bottom: 6px;
        }

        .summary-total-panel {
            margin-top: 12px;
            padding: 16px;
            border-radius: 18px;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 28%),
                linear-gradient(135deg, #175CD3, #0f1176);
            color: #fff;
            box-shadow: 0 18px 34px rgba(23, 92, 211, .18);
        }

        .summary-total-label {
            display: block;
            font-size: 12px;
            color: rgba(255, 255, 255, .78);
            margin-bottom: 6px;
        }

        .summary-total-value {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -.03em;
        }

        /* --- address picker modal / bottom sheet --- */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(16, 24, 40, .5);
            z-index: 100;
            display: none;
            align-items: flex-end;
            justify-content: center;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-sheet {
            background: #fff;
            width: 100%;
            max-width: 560px;
            max-height: 88vh;
            border-radius: 20px 20px 0 0;
            display: flex;
            flex-direction: column;
            animation: sheetUp .22s ease-out;
        }

        @keyframes sheetUp {
            from {
                transform: translateY(24px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-sheet-handle {
            width: 42px;
            height: 4px;
            background: var(--line);
            border-radius: 999px;
            margin: 10px auto 0;
        }

        .modal-sheet-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px 10px;
            border-bottom: 1px solid var(--line);
        }

        .modal-sheet-header h3 {
            font-size: 15px;
        }

        .modal-sheet-close {
            border: 0;
            background: none;
            font-size: 18px;
            color: var(--muted);
            cursor: pointer;
            line-height: 1;
            padding: 4px;
        }

        .modal-sheet-body {
            padding: 14px 18px 18px;
            overflow-y: auto;
        }

        .addr-pick-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 14px;
        }

        .addr-pick-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            cursor: pointer;
            position: relative;
        }

        .addr-pick-card.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .addr-pick-card strong {
            display: block;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .addr-pick-card p {
            font-size: 12px;
            color: var(--muted);
            margin: 0;
        }

        .addr-pick-badge {
            position: absolute;
            top: 12px;
            right: 14px;
            font-size: 10px;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-soft);
            padding: 3px 9px;
            border-radius: 999px;
        }

        .addr-add-toggle {
            width: 100%;
            border: 1.5px dashed var(--line);
            background: #fafafa;
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
        }

        .addr-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 14px;
        }

        .addr-form-grid.cols-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .addr-form-grid .form-group {
            margin-bottom: 0;
        }

        .addr-form-full {
            margin-top: 12px;
        }

        .addr-form-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }

        @media(min-width:560px) {
            .modal-overlay {
                align-items: center;
            }

            .modal-sheet {
                border-radius: 18px;
                max-height: 84vh;
            }

            .modal-sheet-handle {
                display: none;
            }
        }

        @media(max-width:520px) {
            .courier-card {
                padding: 12px;
                border-radius: 16px;
            }

            .courier-card-logo {
                width: 40px;
                height: 40px;
                border-radius: 12px;
            }

            .courier-card .price {
                font-size: 14px;
            }

            .checkout-product-item {
                padding: 10px;
                border-radius: 14px;
            }

            .checkout-product-thumb {
                width: 58px;
                height: 58px;
                border-radius: 12px;
            }

            .checkout-product-title {
                font-size: 13px;
            }

            .checkout-product-price,
            .checkout-product-subtotal-value {
                font-size: 12.5px;
            }

            .delivery-options {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .delivery-option {
                min-height: auto;
                border-radius: 18px;
            }

            .addr-form-grid,
            .addr-form-grid.cols-3,
            .coord-grid {
                grid-template-columns: 1fr;
            }

            .checkout-bottom-total-value {
                font-size: 16px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="checkout-section">
        <div class="container">
            <div class="checkout-wrap">
                <h1><i class="fa-solid fa-receipt"></i> Checkout</h1>

                @if ($errors->any())
                    <div class="auth-alert" style="margin-bottom:14px;">{{ $errors->first() }}</div>
                @endif

                <div class="checkout-grid">
                    <form action="{{ route('checkout.store', [], false) }}" method="POST" id="checkoutForm">
                        @csrf
                        @if ($buyNowProductId)
                            <input type="hidden" name="product_id" value="{{ $buyNowProductId }}">
                            <input type="hidden" name="qty" value="{{ $buyNowQty }}">
                        @endif
                        <input type="hidden" name="delivery_method" id="deliveryMethod" value="shipping">
                        <input type="hidden" name="address_id" id="selectedAddressId">
                        <input type="hidden" name="courier_company" id="selectedCourierCompany">
                        <input type="hidden" name="courier_type" id="selectedCourierType">

                        <div class="checkout-card">
                            <h3><i class="fa-solid fa-box"></i> Produk Dipesan</h3>
                            <div class="checkout-product-list">
                                @foreach ($lines as $line)
                                    @php
                                        $product = $line['product'];
                                        $qty = $line['qty'];
                                        $lineSubtotal = $qty * $product->selling_price;
                                        $hasStrikePrice = $product->strike_price && (float) $product->strike_price > (float) $product->selling_price;
                                    @endphp
                                    <div class="checkout-product-item">
                                        <div class="checkout-product-thumb">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                                            @else
                                                <i class="fa-solid fa-image" style="font-size:26px;"></i>
                                            @endif
                                        </div>
                                        <div class="checkout-product-copy">
                                            <span class="checkout-product-title">{{ $product->name }}</span>
                                            <div class="checkout-product-meta">Qty {{ $qty }}</div>
                                            <div class="checkout-product-pricing">
                                                <div class="checkout-product-unit-price">
                                                    @if ($hasStrikePrice)
                                                        <span class="checkout-product-strike">Rp {{ number_format($product->strike_price, 0, ',', '.') }}</span>
                                                    @endif
                                                    <span class="checkout-product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="checkout-product-subtotal">
                                                    <span class="checkout-product-subtotal-label">Subtotal</span>
                                                    <span class="checkout-product-subtotal-value">Rp {{ number_format($lineSubtotal, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="checkout-card">
                            <h3><i class="fa-solid fa-truck-fast"></i> Metode Pengambilan</h3>
                            <div class="delivery-heading-copy">
                                <p>Pilih cara paling nyaman untuk menerima pesanan Anda.</p>
                            </div>
                            <div class="delivery-options" id="deliveryOptions">
                                <div class="delivery-option selected" data-method="shipping">
                                    <div class="delivery-option-top">
                                        <span class="delivery-option-icon"><i class="fa-solid fa-truck-fast"></i></span>
                                        <span class="delivery-option-check"><i class="fa-solid fa-check"></i></span>
                                    </div>
                                    <div class="delivery-option-copy">
                                        <strong>Pengiriman ke Alamat</strong>
                                        <span>Pesanan dikirim ke alamat pilihan Anda melalui kurir yang tersedia.</span>
                                    </div>
                                    <div class="delivery-option-meta">
                                        <span class="delivery-badge"><i class="fa-solid fa-shield-halved"></i> Aman & terlacak</span>
                                        <span class="delivery-badge"><i class="fa-solid fa-box-open"></i> Praktis dari rumah</span>
                                    </div>
                                </div>
                                <div class="delivery-option" data-method="pickup">
                                    <div class="delivery-option-top">
                                        <span class="delivery-option-icon"><i class="fa-solid fa-store"></i></span>
                                        <span class="delivery-option-check"><i class="fa-solid fa-check"></i></span>
                                    </div>
                                    <div class="delivery-option-copy">
                                        <strong>Ambil di Toko</strong>
                                        <span>Ambil pesanan langsung di toko tanpa ongkir dan tanpa proses pengiriman.</span>
                                    </div>
                                    <div class="delivery-option-meta">
                                        <span class="delivery-badge"><i class="fa-solid fa-money-bill-wave"></i> Tanpa ongkir</span>
                                        <span class="delivery-badge"><i class="fa-solid fa-clock"></i> Lebih fleksibel</span>
                                    </div>
                                </div>
                            </div>
                            <p class="delivery-note">
                                Jika Anda ingin datang langsung ke toko, pilih <strong>Ambil di Toko</strong>. Jika ingin pesanan sampai ke alamat Anda, pilih <strong>Pengiriman ke Alamat</strong>.
                            </p>
                        </div>

                        <div class="checkout-card" id="shippingAddressCard">
                            <h3><i class="fa-solid fa-location-dot"></i> Alamat Pengiriman</h3>
                            <div class="addr-summary-card" id="addressSummaryCard">
                                <div class="addr-summary-empty" id="addressSummaryEmpty">
                                    Anda belum punya alamat tersimpan.
                                </div>
                                <div class="addr-summary-body" id="addressSummaryBody" style="display:none;">
                                    <strong id="addressSummaryTitle"></strong>
                                    <p id="addressSummaryText"></p>
                                </div>
                                <button type="button" class="addr-change-btn" onclick="openAddressModal()">
                                    <span id="addressChangeBtnLabel">{{ $addresses->isEmpty() ? 'Tambah Alamat' : 'Ganti' }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="checkout-card" id="shippingCourierCard">
                            <h3><i class="fa-solid fa-truck-fast"></i> Pilih Kurir</h3>
                            <p class="shipping-trust-badge"><i class="fa-solid fa-shield-halved"></i> Pengiriman Terpercaya</p>
                            <div id="selectedCourierLogo" class="courier-selected-logo" style="display:none;">
                                <img id="selectedCourierLogoImg" src="" alt="">
                                <span id="selectedCourierLogoLabel"></span>
                            </div>
                            <div id="courierList" class="courier-list">
                                <p class="courier-hint">Pilih alamat pengiriman untuk melihat opsi kurir.</p>
                            </div>
                        </div>

                        <div class="checkout-card" id="pickupInfoCard" style="display:none;">
                            <h3><i class="fa-solid fa-store"></i> Info Pickup</h3>
                            <div class="addr-summary-body" style="display:block;">
                                <strong>{{ $namaToko }}</strong>
                                <p>{{ $alamat }}</p>
                            </div>
                            <div class="pickup-map-actions">
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(trim($namaToko . ' ' . $alamat)) }}"
                                    target="_blank" rel="noopener noreferrer" class="pickup-map-btn">
                                    <i class="fa-solid fa-map-location-dot"></i> Buka Maps
                                </a>
                                <button type="button" class="pickup-map-btn" data-copy-text="https://www.google.com/maps/search/?api=1&query={{ urlencode(trim($namaToko . ' ' . $alamat)) }}">
                                    <i class="fa-regular fa-copy"></i> Salin Link Maps
                                </button>
                            </div>
                            <p class="delivery-note" style="margin-top:12px;">
                                Pembayaran tetap dilakukan seperti biasa. Setelah lunas, customer tinggal datang ke toko dan konfirmasi ke admin.
                            </p>
                        </div>

                    </form>

                    <aside class="checkout-summary-sidebar">
                        <div class="checkout-card">
                            <h3><i class="fa-solid fa-ticket"></i> Kode Referral</h3>
                            <div class="referral-inline">
                                <div class="form-group">
                                    <label for="referralCode">Kode Referral</label>
                                    <input type="text" id="referralCode" name="referral_code" form="checkoutForm" value="{{ old('referral_code') }}"
                                        placeholder="Contoh: SALES-ANDI" style="text-transform:uppercase;">
                                </div>
                                <button type="button" class="referral-apply-btn" id="applyReferralBtn">
                                    Cek Kode
                                </button>
                            </div>
                            <p class="delivery-note">
                                Masukkan kode referral yang Anda miliki untuk mendapatkan potongan harga hingga
                                <strong>Rp {{ number_format($referralDiscountSetting, 0, ',', '.') }}</strong>,
                                sesuai ketentuan yang berlaku.
                            </p>
                            <div id="referralFeedback" class="referral-note-box"></div>
                        </div>

                        <div class="checkout-card">
                            <div class="form-group">
                                <label for="notes">Catatan (opsional)</label>
                                <textarea id="notes" name="notes" form="checkoutForm" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="checkout-card">
                            <h3><i class="fa-solid fa-receipt"></i> Ringkasan Belanja</h3>
                            <span class="summary-kicker">Order Summary</span>
                            <div class="checkout-item-row">
                                <span>Subtotal Produk</span>
                                <strong id="summaryItemsSubtotal">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</strong>
                            </div>
                            <div class="checkout-item-row">
                                <span>Ongkos Kirim</span>
                                <strong id="summaryShippingCostRow">Rp 0</strong>
                            </div>
                            <div class="checkout-item-row" id="summaryReferralRow" style="display:none;color:#067647;">
                                <span>Diskon Referral</span>
                                <strong id="summaryReferralDiscount">- Rp 0</strong>
                            </div>
                            <div class="summary-total-panel">
                                <span class="summary-total-label">Total Pembayaran</span>
                                <strong id="summaryGrandTotalCard" class="summary-total-value">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</strong>
                            </div>

                            <button type="submit" form="checkoutForm" class="btn btn-primary checkout-summary-submit-btn">
                                <i class="fa-solid fa-bag-shopping"></i> Buat Pesanan
                            </button>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <div class="checkout-bottom-bar">
        <div class="checkout-bottom-bar-inner">
            <div>
                <div class="checkout-bottom-total-label">Total Pembayaran</div>
                <div class="checkout-bottom-total-value" id="summaryGrandTotal">Rp
                    {{ number_format($itemsSubtotal, 0, ',', '.') }}</div>
            </div>
            <button type="submit" form="checkoutForm" class="btn btn-primary" id="checkoutSubmitBtn">
                <i class="fa-solid fa-bag-shopping"></i> Buat Pesanan
            </button>
        </div>
    </div>

    {{-- Address picker modal (bottom sheet on mobile, centered dialog on desktop) --}}
    <div class="modal-overlay" id="addressModalOverlay">
        <div class="modal-sheet">
            <div class="modal-sheet-handle"></div>
            <div class="modal-sheet-header">
                <h3><i class="fa-solid fa-location-dot"></i> Pilih Alamat Pengiriman</h3>
                <button type="button" class="modal-sheet-close" onclick="closeAddressModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-sheet-body">
                <div class="addr-pick-list" id="addressPickList"></div>

                <button type="button" class="addr-add-toggle" id="addAddressToggleBtn" onclick="toggleAddAddressForm()">
                    <i class="fa-solid fa-plus"></i> Tambah Alamat Baru
                </button>

                <form id="newAddressForm" style="display:none;">
                    <div class="addr-form-grid">
                        <div class="form-group">
                            <label>Label Alamat</label>
                            <input type="text" name="label" id="na_label" placeholder="Rumah / Kantor" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Penerima</label>
                            <input type="text" name="recipient_name" id="na_recipient_name" required>
                        </div>
                    </div>
                    <div class="addr-form-grid">
                        <div class="form-group">
                            <label>No. HP Penerima</label>
                            <input type="text" name="recipient_phone" id="na_recipient_phone" required>
                        </div>
                        <div class="form-group">
                            <label>Kode Pos (opsional)</label>
                            <input type="text" name="postal_code" id="na_postal_code">
                        </div>
                    </div>
                    <div class="addr-form-grid cols-3">
                        <div class="form-group">
                            <label>Provinsi</label>
                            <select id="na_province_select" required>
                                <option value="">-- Pilih --</option>
                            </select>
                            <input type="hidden" name="province" id="na_province">
                        </div>
                        <div class="form-group">
                            <label>Kota</label>
                            <select id="na_city_select" required disabled>
                                <option value="">-- Pilih --</option>
                            </select>
                            <input type="hidden" name="city" id="na_city">
                        </div>
                        <div class="form-group">
                            <label>Kecamatan</label>
                            <select id="na_district_select" required disabled>
                                <option value="">-- Pilih --</option>
                            </select>
                            <input type="hidden" name="district" id="na_district">
                        </div>
                    </div>
                    <div class="form-group addr-form-full">
                        <label>Detail Alamat</label>
                        <textarea name="address_detail" id="na_address_detail" rows="3" required></textarea>
                    </div>
                    <div class="coord-grid">
                        <div class="form-group">
                            <label>Latitude</label>
                            <input type="text" name="latitude" id="na_latitude" class="coord-input"
                                placeholder="-6.1234567" readonly>
                        </div>
                        <div class="form-group">
                            <label>Longitude</label>
                            <input type="text" name="longitude" id="na_longitude" class="coord-input"
                                placeholder="107.1234567" readonly>
                        </div>
                    </div>
                    <div class="map-picker">
                        <div class="map-picker-head">
                            <div>
                                <strong>Pilih titik alamat di peta</strong>
                                <span>Klik peta atau geser marker supaya koordinat akurat.</span>
                            </div>
                            <div class="map-picker-actions">
                                <button type="button" class="map-picker-btn" id="naUseCurrentLocationBtn">Lokasi Saya</button>
                                <button type="button" class="map-picker-btn" id="naResetMapBtn">Reset Titik</button>
                            </div>
                        </div>
                        <div id="na_map" class="leaflet-map"></div>
                        <div class="map-picker-note">Koordinat akan tersimpan bersama alamat customer dan dipakai untuk referensi pengiriman.</div>
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin-top:12px;">
                        <input type="checkbox" name="is_default" id="na_is_default" value="1">
                        Jadikan alamat utama
                    </label>
                    <div class="addr-form-actions">
                        <button type="button" class="btn btn-light" style="flex:1;" onclick="toggleAddAddressForm(false)">Batal</button>
                        <button type="submit" class="btn btn-primary" style="flex:1;" id="saveNewAddressBtn">Simpan &amp; Gunakan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const itemsSubtotal = {{ (float) $itemsSubtotal }};
        const referralDiscountSetting = {{ (float) $referralDiscountSetting }};
        const storeName = @json($namaToko);
        const storeAddress = @json($alamat);
        const selectedAddressIdInput = document.getElementById('selectedAddressId');
        const selectedCourierCompanyInput = document.getElementById('selectedCourierCompany');
        const selectedCourierTypeInput = document.getElementById('selectedCourierType');
        const deliveryMethodInput = document.getElementById('deliveryMethod');
        const courierListEl = document.getElementById('courierList');
        const shippingAddressCard = document.getElementById('shippingAddressCard');
        const shippingCourierCard = document.getElementById('shippingCourierCard');
        const pickupInfoCard = document.getElementById('pickupInfoCard');
        const referralCodeInput = document.getElementById('referralCode');
        const applyReferralBtn = document.getElementById('applyReferralBtn');
        const referralFeedback = document.getElementById('referralFeedback');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const WILAYAH_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';
        const DEFAULT_MAP_CENTER = [-6.563246, 107.760467];

        let addresses = @json($addresses);
        let selectedCourierLabel = '';
        let selectedShippingCost = 0;
        let selectedDeliveryMethod = 'shipping';
        let selectedReferralData = null;
        let naMap = null;
        let naMarker = null;

        function formatRupiah(v) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0);
        }

        async function copyTextValue(text) {
            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(text);
                } else {
                    const tempInput = document.createElement('input');
                    tempInput.value = text;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    tempInput.remove();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Link maps berhasil disalin.',
                    timer: 1400,
                    showConfirmButton: false,
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Link maps belum bisa disalin. Silakan coba lagi.',
                });
            }
        }

        function normalizedReferralCode() {
            return (referralCodeInput.value || '').trim().toUpperCase();
        }

        function currentReferralDiscount() {
            if (!selectedReferralData?.valid) {
                return 0;
            }

            const totalBeforeDiscount = itemsSubtotal + selectedShippingCost;
            return Math.min(
                Number(selectedReferralData.discount_setting || 0),
                Number(selectedReferralData.fee_before_discount || 0),
                Math.max(0, totalBeforeDiscount)
            );
        }

        function renderReferralFeedback(type, html = '') {
            referralFeedback.className = 'referral-note-box';
            referralFeedback.innerHTML = '';

            if (!type) {
                return;
            }

            referralFeedback.classList.add(type);
            referralFeedback.innerHTML = html;
        }

        function normalizeRegionName(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .replace(/kabupaten|kab\.|kota administrasi|kota|provinsi|kecamatan|kec\.|kelurahan|desa/gi, '')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function setSelectDisplayValue(select, value, placeholder) {
            if (!value) {
                return;
            }

            const options = Array.from(select.options);
            const matched = options.find((option) => normalizeRegionName(option.value) === normalizeRegionName(value));

            if (matched) {
                select.value = matched.value;
                return;
            }

            const customOption = document.createElement('option');
            customOption.value = value;
            customOption.textContent = value;
            customOption.dataset.custom = '1';
            customOption.selected = true;
            select.appendChild(customOption);
            select.disabled = false;
        }

        async function fetchJson(url) {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Request failed');
            }
            return response.json();
        }

        function fillSelect(select, items, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>` +
                items.map(i => `<option value="${i.name}" data-id="${i.id}">${i.name}</option>`).join('');
        }

        function updateSummary(shippingCost) {
            selectedShippingCost = shippingCost;
            const referralDiscount = currentReferralDiscount();
            const grandTotal = Math.max(0, (itemsSubtotal + shippingCost) - referralDiscount);
            document.getElementById('summaryGrandTotal').innerText = formatRupiah(grandTotal);
            document.getElementById('summaryShippingCostRow').innerText = formatRupiah(shippingCost);
            document.getElementById('summaryGrandTotalCard').innerText = formatRupiah(grandTotal);
            document.getElementById('summaryReferralRow').style.display = referralDiscount > 0 ? 'flex' : 'none';
            document.getElementById('summaryReferralDiscount').innerText = '- ' + formatRupiah(referralDiscount);
        }

        async function applyReferralCode() {
            const code = normalizedReferralCode();

            if (!code) {
                selectedReferralData = null;
                renderReferralFeedback(null);
                updateSummary(selectedShippingCost);
                return;
            }

            applyReferralBtn.disabled = true;
            applyReferralBtn.innerText = 'Memeriksa...';

            try {
                const res = await fetch('{{ route('checkout.referral.validate', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        referral_code: code,
                        delivery_method: selectedDeliveryMethod,
                        address_id: selectedAddressIdInput.value || null,
                        courier_company: selectedCourierCompanyInput.value || null,
                        courier_type: selectedCourierTypeInput.value || null,
                        shipping_cost: selectedShippingCost,
                        ...extraParams(),
                    }),
                });

                const data = await res.json();

                if (!res.ok) {
                    selectedReferralData = null;
                    renderReferralFeedback('error', data.message || 'Kode referral tidak valid.');
                    updateSummary(selectedShippingCost);
                    return;
                }

                selectedReferralData = {
                    ...data,
                    valid: true,
                };

                const actualDiscount = Math.min(
                    Number(data.discount_setting || 0),
                    Number(data.fee_before_discount || 0),
                    itemsSubtotal + selectedShippingCost
                );

                renderReferralFeedback('success', `
                    <strong>${data.marketing_name}</strong><br>
                    Kode <strong>${data.referral_code}</strong> aktif.
                    Customer mendapat diskon <strong>${formatRupiah(actualDiscount)}</strong>.
                `);
                referralCodeInput.value = data.referral_code;
                updateSummary(selectedShippingCost);
            } catch (error) {
                selectedReferralData = null;
                renderReferralFeedback('error', 'Gagal memeriksa kode referral. Silakan coba lagi.');
                updateSummary(selectedShippingCost);
            } finally {
                applyReferralBtn.disabled = false;
                applyReferralBtn.innerText = 'Cek Kode';
            }
        }

        function setDeliveryMethod(method) {
            selectedDeliveryMethod = method;
            deliveryMethodInput.value = method;

            document.querySelectorAll('.delivery-option').forEach(option => {
                option.classList.toggle('selected', option.dataset.method === method);
            });

            const isPickup = method === 'pickup';
            shippingAddressCard.classList.toggle('delivery-hidden', isPickup);
            shippingCourierCard.classList.toggle('delivery-hidden', isPickup);
            pickupInfoCard.style.display = isPickup ? 'block' : 'none';

            if (isPickup) {
                selectedAddressIdInput.value = '';
                selectedCourierCompanyInput.value = '';
                selectedCourierTypeInput.value = '';
                selectedCourierLabel = '';
                courierListEl.innerHTML = '<p class="courier-hint">Pickup sendiri tidak memerlukan pilihan kurir.</p>';
                document.getElementById('selectedCourierLogo').style.display = 'none';
                updateSummary(0);
                renderAddressSummary();
            } else {
                pickupInfoCard.style.display = 'none';

                if (addresses.length && !selectedAddressIdInput.value) {
                    const initial = addresses.find(a => a.is_default) || addresses[0];
                    if (initial) {
                        selectAddress(initial.id);
                        return;
                    }
                }

                if (selectedAddressIdInput.value) {
                    loadRatesForAddress(selectedAddressIdInput.value);
                }
            }
        }

        function extraParams() {
            const params = {};
            const productIdInput = document.querySelector('input[name=product_id]');
            const qtyInput = document.querySelector('input[name=qty]');
            if (productIdInput) params.product_id = productIdInput.value;
            if (qtyInput) params.qty = qtyInput.value;
            return params;
        }

        function renderAddressSummary() {
            if (selectedDeliveryMethod === 'pickup') {
                const emptyEl = document.getElementById('addressSummaryEmpty');
                const bodyEl = document.getElementById('addressSummaryBody');
                const btnLabel = document.getElementById('addressChangeBtnLabel');

                emptyEl.style.display = 'none';
                bodyEl.style.display = 'block';
                btnLabel.innerText = 'Pickup';
                document.getElementById('addressSummaryTitle').innerText = storeName;
                document.getElementById('addressSummaryText').innerText = storeAddress;
                return;
            }

            const selected = addresses.find(a => String(a.id) === String(selectedAddressIdInput.value));
            const emptyEl = document.getElementById('addressSummaryEmpty');
            const bodyEl = document.getElementById('addressSummaryBody');
            const btnLabel = document.getElementById('addressChangeBtnLabel');

            if (!selected) {
                emptyEl.style.display = 'block';
                bodyEl.style.display = 'none';
                btnLabel.innerText = addresses.length ? 'Pilih' : 'Tambah Alamat';
                return;
            }

            emptyEl.style.display = 'none';
            bodyEl.style.display = 'block';
            btnLabel.innerText = 'Ganti';
            document.getElementById('addressSummaryTitle').innerText =
                `${selected.label} — ${selected.recipient_name} (${selected.recipient_phone})`;
            document.getElementById('addressSummaryText').innerText =
                `${selected.address_detail}, ${selected.district}, ${selected.city}, ${selected.province}`;
        }

        function renderAddressPickList() {
            const listEl = document.getElementById('addressPickList');
            if (!addresses.length) {
                listEl.innerHTML = '';
                return;
            }
            listEl.innerHTML = addresses.map(a => `
                <div class="addr-pick-card ${String(a.id) === String(selectedAddressIdInput.value) ? 'selected' : ''}" data-id="${a.id}">
                    ${a.is_default ? '<span class="addr-pick-badge">Utama</span>' : ''}
                    <strong>${a.label} — ${a.recipient_name} (${a.recipient_phone})</strong>
                    <p>${a.address_detail}, ${a.district}, ${a.city}, ${a.province}</p>
                </div>
            `).join('');

            listEl.querySelectorAll('.addr-pick-card').forEach(card => {
                card.addEventListener('click', function () {
                    selectAddress(this.dataset.id);
                    closeAddressModal();
                });
            });
        }

        function selectAddress(addressId) {
            selectedAddressIdInput.value = addressId;
            renderAddressSummary();
            renderAddressPickList();
            loadRatesForAddress(addressId);
        }

        async function loadRatesForAddress(addressId) {
            if (selectedDeliveryMethod === 'pickup') {
                updateSummary(0);
                return;
            }

            courierListEl.innerHTML = '<p class="courier-hint">Memuat opsi kurir...</p>';
            selectedCourierCompanyInput.value = '';
            selectedCourierTypeInput.value = '';
            selectedCourierLabel = '';
            document.getElementById('selectedCourierLogo').style.display = 'none';
            updateSummary(0);

            try {
                const res = await fetch('{{ route('checkout.rates', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ address_id: addressId, ...extraParams() }),
                });

                const data = await res.json();

                if (!res.ok) {
                    courierListEl.innerHTML = `<p class="courier-hint">${data.message || 'Gagal memuat opsi kurir.'}</p>`;
                    return;
                }

                const pricing = (data.pricing || []).sort((a, b) => Number(a.price || 0) - Number(b.price || 0));
                if (!pricing.length) {
                    courierListEl.innerHTML = '<p class="courier-hint">Tidak ada opsi kurir untuk alamat ini.</p>';
                    return;
                }

                const lowestPrice = Number(pricing[0]?.price || 0);

                courierListEl.innerHTML = pricing.map((p, index) => `
                    <div class="courier-card" data-company="${p.courier_code}" data-type="${p.type}" data-price="${p.price}"
                        data-label="${p.courier_name} - ${p.courier_service_name}" data-logo="${p.courier_logo_url || ''}">
                        <div class="courier-card-main">
                            <div class="courier-card-logo">
                                ${p.courier_logo_url ? `<img src="${p.courier_logo_url}" alt="${p.courier_name}">` : `<i class="fa-solid fa-truck"></i>`}
                            </div>
                            <div class="courier-card-copy">
                                <div class="name">${p.courier_name} - ${p.courier_service_name}</div>
                                <div class="duration">Estimasi ${p.duration || '-'}</div>
                                <div class="courier-card-meta">
                                    ${Number(p.price || 0) === lowestPrice ? '<span class="courier-chip"><i class="fa-solid fa-tag"></i> Termurah</span>' : ''}
                                    ${index === 0 ? '<span class="courier-chip"><i class="fa-solid fa-star"></i> Rekomendasi</span>' : ''}
                                </div>
                            </div>
                        </div>
                        <div class="courier-card-side">
                            <div class="price">${formatRupiah(p.price)}</div>
                            <span class="courier-card-check"><i class="fa-solid fa-check"></i></span>
                        </div>
                    </div>
                `).join('');

                const selectedLogoWrap = document.getElementById('selectedCourierLogo');
                const selectedLogoImg = document.getElementById('selectedCourierLogoImg');
                const selectedLogoLabel = document.getElementById('selectedCourierLogoLabel');

                courierListEl.querySelectorAll('.courier-card').forEach(card => {
                    card.addEventListener('click', function () {
                        courierListEl.querySelectorAll('.courier-card').forEach(c => c.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedCourierCompanyInput.value = this.dataset.company;
                        selectedCourierTypeInput.value = this.dataset.type;
                        selectedCourierLabel = this.dataset.label;
                        updateSummary(Number(this.dataset.price));

                        if (this.dataset.logo) {
                            selectedLogoImg.src = this.dataset.logo;
                            selectedLogoLabel.textContent = this.dataset.label;
                            selectedLogoWrap.style.display = 'flex';
                        } else {
                            selectedLogoWrap.style.display = 'none';
                        }
                    });
                });

                courierListEl.querySelector('.courier-card')?.click();
            } catch (e) {
                courierListEl.innerHTML = '<p class="courier-hint">Gagal memuat opsi kurir.</p>';
            }
        }

        function openAddressModal() {
            if (selectedDeliveryMethod === 'pickup') {
                return;
            }
            renderAddressPickList();
            document.getElementById('addressModalOverlay').classList.add('open');
        }

        function closeAddressModal() {
            document.getElementById('addressModalOverlay').classList.remove('open');
            toggleAddAddressForm(false);
        }

        document.getElementById('addressModalOverlay').addEventListener('click', function (e) {
            if (e.target === this) closeAddressModal();
        });

        // --- Add-new-address inline form (submits via fetch, no page reload) ---
        const naProvinceSelect = document.getElementById('na_province_select');
        const naCitySelect = document.getElementById('na_city_select');
        const naDistrictSelect = document.getElementById('na_district_select');
        const naProvinceInput = document.getElementById('na_province');
        const naCityInput = document.getElementById('na_city');
        const naDistrictInput = document.getElementById('na_district');
        const naPostalCodeInput = document.getElementById('na_postal_code');
        const naAddressDetailInput = document.getElementById('na_address_detail');
        const naLatitudeInput = document.getElementById('na_latitude');
        const naLongitudeInput = document.getElementById('na_longitude');

        fetchJson(`${WILAYAH_BASE}/provinces.json`)
            .then(data => fillSelect(naProvinceSelect, data, '-- Pilih Provinsi --'))
            .catch(() => {});

        naProvinceSelect.addEventListener('change', function () {
            naProvinceInput.value = this.value;
            naCitySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            naDistrictSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            naCitySelect.disabled = true;
            naDistrictSelect.disabled = true;
            naCityInput.value = '';
            naDistrictInput.value = '';

            const id = this.selectedOptions[0]?.dataset.id;
            if (!id) return;
            fetchJson(`${WILAYAH_BASE}/regencies/${id}.json`)
                .then(data => { fillSelect(naCitySelect, data, '-- Pilih Kota --'); naCitySelect.disabled = false; });
        });

        naCitySelect.addEventListener('change', function () {
            naCityInput.value = this.value;
            naDistrictSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            naDistrictSelect.disabled = true;
            naDistrictInput.value = '';

            const id = this.selectedOptions[0]?.dataset.id;
            if (!id) return;
            fetchJson(`${WILAYAH_BASE}/districts/${id}.json`)
                .then(data => { fillSelect(naDistrictSelect, data, '-- Pilih Kecamatan --'); naDistrictSelect.disabled = false; });
        });

        naDistrictSelect.addEventListener('change', function () {
            naDistrictInput.value = this.value;
        });

        function setCheckoutMapPoint(lat, lng, { syncAddress = true } = {}) {
            naLatitudeInput.value = Number(lat).toFixed(7);
            naLongitudeInput.value = Number(lng).toFixed(7);

            const point = [Number(lat), Number(lng)];
            if (naMarker) {
                naMarker.setLatLng(point);
            } else {
                naMarker = L.marker(point, { draggable: true }).addTo(naMap);
                naMarker.on('dragend', () => {
                    const markerPoint = naMarker.getLatLng();
                    setCheckoutMapPoint(markerPoint.lat, markerPoint.lng, { syncAddress: true });
                });
            }

            naMap.setView(point, Math.max(naMap.getZoom(), 15));

            if (syncAddress) {
                reverseGeocodeCheckoutPoint(lat, lng);
            }
        }

        async function reverseGeocodeCheckoutPoint(lat, lng) {
            try {
                const data = await fetchJson(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=id`);
                const address = data.address || {};
                const province = address.state || address.region || '';
                const city = address.city || address.county || address.municipality || address.town || '';
                const district = address.city_district || address.suburb || address.state_district || address.village || address.town || '';
                const detailParts = [
                    address.road,
                    address.house_number,
                    address.neighbourhood,
                    address.hamlet,
                    address.residential,
                ].filter(Boolean);

                if (province) {
                    naProvinceInput.value = province;
                    setSelectDisplayValue(naProvinceSelect, province, '-- Pilih Provinsi --');
                }

                if (city) {
                    naCityInput.value = city;
                    setSelectDisplayValue(naCitySelect, city, '-- Pilih Kota --');
                }

                if (district) {
                    naDistrictInput.value = district;
                    setSelectDisplayValue(naDistrictSelect, district, '-- Pilih Kecamatan --');
                }

                if (address.postcode && !naPostalCodeInput.value) {
                    naPostalCodeInput.value = address.postcode;
                }

                if (detailParts.length) {
                    naAddressDetailInput.value = detailParts.join(', ');
                } else if (!naAddressDetailInput.value && data.display_name) {
                    naAddressDetailInput.value = data.display_name;
                }
            } catch (error) {
                console.warn('Reverse geocoding gagal', error);
            }
        }

        function initCheckoutMap() {
            if (naMap) {
                return;
            }

            naMap = L.map('na_map', { zoomControl: true }).setView(DEFAULT_MAP_CENTER, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(naMap);

            naMap.on('click', (event) => {
                setCheckoutMapPoint(event.latlng.lat, event.latlng.lng, { syncAddress: true });
            });

            document.getElementById('naUseCurrentLocationBtn').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    Swal.fire({ icon: 'warning', title: 'Lokasi Tidak Tersedia', text: 'Browser ini tidak mendukung geolocation.' });
                    return;
                }

                navigator.geolocation.getCurrentPosition((position) => {
                    setCheckoutMapPoint(position.coords.latitude, position.coords.longitude, { syncAddress: true });
                }, () => {
                    Swal.fire({ icon: 'warning', title: 'Lokasi Gagal Diambil', text: 'Izinkan akses lokasi atau pilih titik langsung di peta.' });
                }, {
                    enableHighAccuracy: true,
                    timeout: 12000,
                });
            });

            document.getElementById('naResetMapBtn').addEventListener('click', () => {
                naLatitudeInput.value = '';
                naLongitudeInput.value = '';
                if (naMarker) {
                    naMap.removeLayer(naMarker);
                    naMarker = null;
                }
                naMap.setView(DEFAULT_MAP_CENTER, 12);
            });
        }

        function toggleAddAddressForm(show) {
            if (selectedDeliveryMethod === 'pickup') {
                return;
            }
            const formEl = document.getElementById('newAddressForm');
            const listEl = document.getElementById('addressPickList');
            const toggleBtn = document.getElementById('addAddressToggleBtn');
            const shouldShow = show === undefined ? formEl.style.display === 'none' : show;

            formEl.style.display = shouldShow ? 'block' : 'none';
            listEl.style.display = shouldShow ? 'none' : 'flex';
            toggleBtn.style.display = shouldShow ? 'none' : 'block';

            if (shouldShow) {
                initCheckoutMap();
                setTimeout(() => naMap.invalidateSize(), 180);
            }

            if (!shouldShow) {
                formEl.reset();
                naCitySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                naDistrictSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                naCitySelect.disabled = true;
                naDistrictSelect.disabled = true;
                naProvinceInput.value = '';
                naCityInput.value = '';
                naDistrictInput.value = '';
                naLatitudeInput.value = '';
                naLongitudeInput.value = '';
                if (naMarker) {
                    naMap.removeLayer(naMarker);
                    naMarker = null;
                }
                naMap?.setView(DEFAULT_MAP_CENTER, 12);
            }
        }

        document.getElementById('newAddressForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!document.getElementById('na_province').value || !document.getElementById('na_city').value || !document.getElementById('na_district').value) {
                Swal.fire({ icon: 'warning', title: 'Lengkapi Alamat', text: 'Mohon pilih Provinsi, Kota, dan Kecamatan.' });
                return;
            }

            const saveBtn = document.getElementById('saveNewAddressBtn');
            saveBtn.disabled = true;
            saveBtn.innerText = 'Menyimpan...';

            const payload = {
                label: document.getElementById('na_label').value,
                recipient_name: document.getElementById('na_recipient_name').value,
                recipient_phone: document.getElementById('na_recipient_phone').value,
                postal_code: document.getElementById('na_postal_code').value,
                province: document.getElementById('na_province').value,
                city: document.getElementById('na_city').value,
                district: document.getElementById('na_district').value,
                address_detail: document.getElementById('na_address_detail').value,
                latitude: naLatitudeInput.value || null,
                longitude: naLongitudeInput.value || null,
                is_default: document.getElementById('na_is_default').checked ? 1 : 0,
            };

            try {
                const res = await fetch('{{ route('customer.addresses.store', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!res.ok) {
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Gagal menyimpan alamat.');
                    Swal.fire({ icon: 'error', title: 'Gagal', text: firstError });
                    return;
                }

                if (payload.is_default) {
                    addresses = addresses.map(a => ({ ...a, is_default: false }));
                }
                addresses.push(data.address);
                addresses.sort((a, b) => Number(b.is_default) - Number(a.is_default));

                toggleAddAddressForm(false);
                selectAddress(data.address.id);
                closeAddressModal();
                Swal.fire({ icon: 'success', title: 'Alamat Tersimpan', timer: 1400, showConfirmButton: false });
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan, silakan coba lagi.' });
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Simpan & Gunakan';
            }
        });

        referralCodeInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
            selectedReferralData = null;
            renderReferralFeedback(null);
            updateSummary(selectedShippingCost);
        });

        referralCodeInput.addEventListener('blur', function () {
            this.value = normalizedReferralCode();
        });

        applyReferralBtn.addEventListener('click', applyReferralCode);

        // --- init ---
        document.querySelectorAll('.delivery-option').forEach(option => {
            option.addEventListener('click', function () {
                setDeliveryMethod(this.dataset.method);
            });
        });

        document.querySelectorAll('[data-copy-text]').forEach((button) => {
            button.addEventListener('click', function () {
                copyTextValue(this.dataset.copyText);
            });
        });

        renderAddressSummary();
        if (addresses.length) {
            const initial = addresses.find(a => a.is_default) || addresses[0];
            if (initial) {
                selectAddress(initial.id);
            }
        }
        setDeliveryMethod('shipping');

        if (normalizedReferralCode()) {
            applyReferralCode();
        }

        window.applyScrollReveal?.();

        // --- validation + confirmation before real submit (AJAX) ---
        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const grandTotal = Math.max(0, (itemsSubtotal + selectedShippingCost) - currentReferralDiscount());
            const isPickup = selectedDeliveryMethod === 'pickup';
            const selectedAddress = !isPickup ? addresses.find(a => String(a.id) === String(selectedAddressIdInput.value)) : null;
            const referralCode = normalizedReferralCode();

            if (!isPickup && !selectedAddressIdInput.value) {
                Swal.fire({ icon: 'warning', title: 'Alamat Belum Dipilih', text: 'Mohon pilih atau tambahkan alamat pengiriman terlebih dahulu.' });
                return;
            }
            if (!isPickup && !selectedCourierCompanyInput.value) {
                Swal.fire({ icon: 'warning', title: 'Kurir Belum Dipilih', text: 'Mohon pilih kurir pengiriman terlebih dahulu.' });
                return;
            }
            if (referralCode && (!selectedReferralData?.valid || selectedReferralData.referral_code !== referralCode)) {
                Swal.fire({ icon: 'warning', title: 'Cek Kode Referral', text: 'Silakan cek kode referral terlebih dahulu sebelum membuat pesanan.' });
                return;
            }

            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Pesanan',
                html: `
                    <div style="text-align:left;font-size:13.5px;">
                        ${isPickup
                            ? `<p style="margin-bottom:8px;"><strong>Pickup:</strong><br>${storeName}<br>${storeAddress}</p>`
                            : `<p style="margin-bottom:8px;"><strong>Alamat:</strong><br>${selectedAddress.label} — ${selectedAddress.recipient_name}<br>${selectedAddress.address_detail}, ${selectedAddress.district}, ${selectedAddress.city}</p>
                               <p style="margin-bottom:8px;"><strong>Kurir:</strong><br>${selectedCourierLabel}</p>`
                        }
                        ${selectedReferralData?.valid
                            ? `<p style="margin-bottom:8px;"><strong>Referral:</strong><br>${selectedReferralData.referral_code} (${selectedReferralData.marketing_name})<br>Diskon ${formatRupiah(currentReferralDiscount())}</p>`
                            : ''
                        }
                        <p><strong>Total Bayar:</strong> ${formatRupiah(grandTotal)}</p>
                        <p style="color:#B42318;font-size:12px;margin-top:10px;">Selesaikan pembayaran dalam 30 menit setelah pesanan dibuat, atau pesanan otomatis dibatalkan.</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Buat Pesanan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2563eb',
            }).then(result => {
                if (result.isConfirmed) {
                    submitCheckout(e.target);
                }
            });
        });

        async function submitCheckout(form) {
            showLoading();

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                });

                const data = await res.json();

                if (!res.ok) {
                    hideLoading();
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan, silakan coba lagi.' })
                        .then(() => {
                            if (data.redirect) window.location.href = data.redirect;
                        });
                    return;
                }

                window.location.href = data.redirect;
            } catch (err) {
                hideLoading();
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan, silakan coba lagi.' });
            }
        }
    </script>
@endpush
