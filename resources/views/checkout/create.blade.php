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
                position: sticky;
                top: 90px;
            }

            .checkout-bottom-bar {
                position: static;
                background: transparent;
                border-top: 0;
                box-shadow: none;
                padding: 0;
                margin-top: 18px;
            }

            .checkout-bottom-bar-inner {
                max-width: 100%;
                justify-content: flex-end;
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
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .courier-card.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .courier-card .name {
            font-size: 13px;
            font-weight: 700;
        }

        .courier-card .duration {
            font-size: 11.5px;
            color: var(--muted);
        }

        .courier-card .price {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--primary);
            white-space: nowrap;
        }

        .courier-hint {
            font-size: 12.5px;
            color: var(--muted);
            padding: 10px 0;
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
                        <input type="hidden" name="address_id" id="selectedAddressId">
                        <input type="hidden" name="courier_company" id="selectedCourierCompany">
                        <input type="hidden" name="courier_type" id="selectedCourierType">

                        <div class="checkout-card">
                            <h3><i class="fa-solid fa-box"></i> Produk Dipesan</h3>
                            @foreach ($lines as $line)
                                <div class="checkout-item-row">
                                    <span>{{ $line['product']->name }} × {{ $line['qty'] }}</span>
                                    <strong>Rp
                                        {{ number_format($line['qty'] * $line['product']->selling_price, 0, ',', '.') }}</strong>
                                </div>
                            @endforeach
                        </div>

                        <div class="checkout-card">
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

                        <div class="checkout-card">
                            <h3><i class="fa-solid fa-truck-fast"></i> Pilih Kurir</h3>
                            <div id="courierList" class="courier-list">
                                <p class="courier-hint">Pilih alamat pengiriman untuk melihat opsi kurir.</p>
                            </div>
                        </div>

                        <div class="checkout-card">
                            <div class="form-group">
                                <label for="notes">Catatan (opsional)</label>
                                <textarea id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </form>

                    <aside class="checkout-summary-sidebar">
                        <div class="checkout-card">
                            <h3><i class="fa-solid fa-receipt"></i> Ringkasan Belanja</h3>
                            <div class="checkout-item-row">
                                <span>Subtotal Produk</span>
                                <strong id="summaryItemsSubtotal">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</strong>
                            </div>
                            <div class="checkout-item-row">
                                <span>Ongkos Kirim</span>
                                <strong id="summaryShippingCostRow">Rp 0</strong>
                            </div>
                            <div class="checkout-item-row" style="font-weight:800;color:var(--primary);border-top:1px solid var(--line);padding-top:12px;margin-top:4px;">
                                <span>Total</span>
                                <strong id="summaryGrandTotalCard">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</strong>
                            </div>
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
                Buat Pesanan
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
        const selectedAddressIdInput = document.getElementById('selectedAddressId');
        const selectedCourierCompanyInput = document.getElementById('selectedCourierCompany');
        const selectedCourierTypeInput = document.getElementById('selectedCourierType');
        const courierListEl = document.getElementById('courierList');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const WILAYAH_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';
        const DEFAULT_MAP_CENTER = [-6.563246, 107.760467];

        let addresses = @json($addresses);
        let selectedCourierLabel = '';
        let selectedShippingCost = 0;
        let naMap = null;
        let naMarker = null;

        function formatRupiah(v) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0);
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
            const grandTotal = itemsSubtotal + shippingCost;
            document.getElementById('summaryGrandTotal').innerText = formatRupiah(grandTotal);
            document.getElementById('summaryShippingCostRow').innerText = formatRupiah(shippingCost);
            document.getElementById('summaryGrandTotalCard').innerText = formatRupiah(grandTotal);
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
            courierListEl.innerHTML = '<p class="courier-hint">Memuat opsi kurir...</p>';
            selectedCourierCompanyInput.value = '';
            selectedCourierTypeInput.value = '';
            selectedCourierLabel = '';
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

                courierListEl.innerHTML = pricing.map((p) => `
                    <div class="courier-card" data-company="${p.courier_code}" data-type="${p.type}" data-price="${p.price}"
                        data-label="${p.courier_name} - ${p.courier_service_name}">
                        <div>
                            <div class="name">${p.courier_name} - ${p.courier_service_name}</div>
                            <div class="duration">Estimasi ${p.duration || '-'}</div>
                        </div>
                        <div class="price">${formatRupiah(p.price)}</div>
                    </div>
                `).join('');

                courierListEl.querySelectorAll('.courier-card').forEach(card => {
                    card.addEventListener('click', function () {
                        courierListEl.querySelectorAll('.courier-card').forEach(c => c.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedCourierCompanyInput.value = this.dataset.company;
                        selectedCourierTypeInput.value = this.dataset.type;
                        selectedCourierLabel = this.dataset.label;
                        updateSummary(Number(this.dataset.price));
                    });
                });

                courierListEl.querySelector('.courier-card')?.click();
            } catch (e) {
                courierListEl.innerHTML = '<p class="courier-hint">Gagal memuat opsi kurir.</p>';
            }
        }

        function openAddressModal() {
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

        // --- init ---
        renderAddressSummary();
        if (addresses.length) {
            const initial = addresses.find(a => a.is_default) || addresses[0];
            selectAddress(initial.id);
        }

        window.applyScrollReveal?.();

        // --- validation + confirmation before real submit (AJAX) ---
        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            e.preventDefault();

            if (!selectedAddressIdInput.value) {
                Swal.fire({ icon: 'warning', title: 'Alamat Belum Dipilih', text: 'Mohon pilih atau tambahkan alamat pengiriman terlebih dahulu.' });
                return;
            }
            if (!selectedCourierCompanyInput.value) {
                Swal.fire({ icon: 'warning', title: 'Kurir Belum Dipilih', text: 'Mohon pilih kurir pengiriman terlebih dahulu.' });
                return;
            }

            const selectedAddress = addresses.find(a => String(a.id) === String(selectedAddressIdInput.value));
            const grandTotal = itemsSubtotal + selectedShippingCost;

            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Pesanan',
                html: `
                    <div style="text-align:left;font-size:13.5px;">
                        <p style="margin-bottom:8px;"><strong>Alamat:</strong><br>${selectedAddress.label} — ${selectedAddress.recipient_name}<br>${selectedAddress.address_detail}, ${selectedAddress.district}, ${selectedAddress.city}</p>
                        <p style="margin-bottom:8px;"><strong>Kurir:</strong><br>${selectedCourierLabel}</p>
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
