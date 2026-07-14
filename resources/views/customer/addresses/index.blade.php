@extends('layouts.catalog')

@section('title', 'Alamat Saya | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .addr-section {
            padding: 40px 0 60px;
        }

        .addr-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .addr-header h1 {
            font-size: 24px;
        }

        .addr-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .addr-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px;
            position: relative;
        }

        .addr-card.is-default {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .addr-label-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .addr-label {
            font-weight: 700;
            font-size: 13.5px;
        }

        .addr-default-badge {
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 999px;
        }

        .addr-card p {
            font-size: 12.5px;
            color: #475467;
            margin-bottom: 3px;
        }

        .addr-actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            font-size: 12px;
        }

        .addr-actions button,
        .addr-actions a {
            border: 0;
            background: none;
            color: var(--primary);
            font-weight: 700;
            cursor: pointer;
            padding: 0;
        }

        .addr-actions .danger {
            color: var(--danger);
        }

        .addr-empty {
            text-align: center;
            padding: 60px 0;
            color: var(--muted);
        }

        .addr-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(16, 24, 40, .5);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .addr-modal-overlay.open {
            display: flex;
        }

        .addr-modal {
            background: #fff;
            border-radius: 18px;
            width: min(520px, 100%);
            max-height: 90vh;
            overflow-y: auto;
            padding: 26px;
        }

        .addr-modal h3 {
            font-size: 17px;
            margin-bottom: 18px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 13px;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
        }

        .addr-modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }

        @media(max-width:640px) {
            .addr-grid {
                grid-template-columns: 1fr;
            }

            .form-row,
            .form-row-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <section class="addr-section">
        <div class="container">
            <div class="addr-header">
                <h1><i class="fa-solid fa-location-dot"></i> Alamat Saya</h1>
                <button type="button" class="btn btn-primary" onclick="openAddressModal()">
                    <i class="fa-solid fa-plus"></i> Tambah Alamat
                </button>
            </div>

            <div id="addrEmptyState" class="addr-empty" style="{{ $addresses->isEmpty() ? '' : 'display:none;' }}">
                <p>Anda belum menyimpan alamat.</p>
            </div>
            <div class="addr-grid" id="addrGrid" style="{{ $addresses->isEmpty() ? 'display:none;' : '' }}"></div>
        </div>
    </section>

    <div class="addr-modal-overlay" id="addressModalOverlay">
        <div class="addr-modal">
            <h3 id="addressModalTitle">Tambah Alamat</h3>
            <form id="addressForm" method="POST" action="{{ route('customer.addresses.store') }}">
                @csrf
                <div id="addressMethodField"></div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Label Alamat</label>
                        <input type="text" name="label" id="f_label" placeholder="Rumah / Kantor" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Penerima</label>
                        <input type="text" name="recipient_name" id="f_recipient_name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>No. HP Penerima</label>
                        <input type="text" name="recipient_phone" id="f_recipient_phone" required>
                    </div>
                    <div class="form-group">
                        <label>Kode Pos (opsional)</label>
                        <input type="text" name="postal_code" id="f_postal_code">
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Provinsi</label>
                        <select id="f_province_select" required>
                            <option value="">-- Pilih --</option>
                        </select>
                        <input type="hidden" name="province" id="f_province">
                    </div>
                    <div class="form-group">
                        <label>Kota</label>
                        <select id="f_city_select" required disabled>
                            <option value="">-- Pilih --</option>
                        </select>
                        <input type="hidden" name="city" id="f_city">
                    </div>
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <select id="f_district_select" required disabled>
                            <option value="">-- Pilih --</option>
                        </select>
                        <input type="hidden" name="district" id="f_district">
                    </div>
                </div>

                <div class="form-group">
                    <label>Detail Alamat</label>
                    <textarea name="address_detail" id="f_address_detail" rows="3" required></textarea>
                </div>

                <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin-bottom:10px;">
                    <input type="checkbox" name="is_default" id="f_is_default" value="1">
                    Jadikan alamat utama
                </label>

                <div class="addr-modal-actions">
                    <button type="button" class="btn btn-light" style="flex:1;" onclick="closeAddressModal()">Batal</button>
                    <button type="submit" class="btn btn-primary" style="flex:1;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let addresses = @json($addresses);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const WILAYAH_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';
        const provinceSelect = document.getElementById('f_province_select');
        const citySelect = document.getElementById('f_city_select');
        const districtSelect = document.getElementById('f_district_select');

        function renderAddressGrid() {
            const gridEl = document.getElementById('addrGrid');
            const emptyEl = document.getElementById('addrEmptyState');

            if (!addresses.length) {
                gridEl.style.display = 'none';
                emptyEl.style.display = 'block';
                return;
            }

            emptyEl.style.display = 'none';
            gridEl.style.display = 'grid';

            gridEl.innerHTML = addresses.map(a => `
                <div class="addr-card ${a.is_default ? 'is-default' : ''}">
                    <div class="addr-label-row">
                        <span class="addr-label">${a.label}</span>
                        ${a.is_default ? '<span class="addr-default-badge">Utama</span>' : ''}
                    </div>
                    <p><strong>${a.recipient_name}</strong> (${a.recipient_phone})</p>
                    <p>${a.address_detail}</p>
                    <p style="color:var(--muted);">${a.district}, ${a.city}, ${a.province}${a.postal_code ? ' ' + a.postal_code : ''}</p>
                    <div class="addr-actions">
                        <button type="button" onclick='editAddress(${JSON.stringify(a)})'>Ubah</button>
                        ${!a.is_default ? `<button type="button" onclick="setDefaultAddress(${a.id})">Jadikan Utama</button>` : ''}
                        <button type="button" class="danger" onclick="confirmDeleteAddress(${a.id})">Hapus</button>
                    </div>
                </div>
            `).join('');
        }
        renderAddressGrid();

        async function setDefaultAddress(id) {
            Swal.fire({ title: 'Menyimpan...', didOpen: () => Swal.showLoading(), allowOutsideClick: false, showConfirmButton: false });

            try {
                const res = await fetch(`/akun/alamat/${id}/default`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (!res.ok) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                    return;
                }

                addresses = addresses.map(a => ({ ...a, is_default: a.id === id }));
                renderAddressGrid();
                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1300, showConfirmButton: false });
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan.' });
            }
        }

        function confirmDeleteAddress(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Alamat?',
                text: 'Alamat ini akan dihapus permanen.',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B42318',
            }).then(result => {
                if (result.isConfirmed) deleteAddress(id);
            });
        }

        async function deleteAddress(id) {
            Swal.fire({ title: 'Menghapus...', didOpen: () => Swal.showLoading(), allowOutsideClick: false, showConfirmButton: false });

            try {
                const res = await fetch(`/akun/alamat/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (!res.ok) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                    return;
                }

                const wasDefault = addresses.find(a => a.id === id)?.is_default;
                addresses = addresses.filter(a => a.id !== id);
                if (wasDefault && addresses.length) addresses[0].is_default = true;
                renderAddressGrid();
                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1300, showConfirmButton: false });
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan.' });
            }
        }

        function fillSelect(select, items, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>` +
                items.map(i => `<option value="${i.name}" data-id="${i.id}">${i.name}</option>`).join('');
        }

        function loadProvinces() {
            fetch(`${WILAYAH_BASE}/provinces.json`)
                .then(r => r.json())
                .then(data => fillSelect(provinceSelect, data, '-- Pilih Provinsi --'));
        }
        loadProvinces();

        provinceSelect.addEventListener('change', function () {
            document.getElementById('f_province').value = this.value;
            citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            citySelect.disabled = true;
            districtSelect.disabled = true;
            document.getElementById('f_city').value = '';
            document.getElementById('f_district').value = '';

            const id = this.selectedOptions[0]?.dataset.id;
            if (!id) return;
            fetch(`${WILAYAH_BASE}/regencies/${id}.json`)
                .then(r => r.json())
                .then(data => { fillSelect(citySelect, data, '-- Pilih Kota --'); citySelect.disabled = false; });
        });

        citySelect.addEventListener('change', function () {
            document.getElementById('f_city').value = this.value;
            districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            districtSelect.disabled = true;
            document.getElementById('f_district').value = '';

            const id = this.selectedOptions[0]?.dataset.id;
            if (!id) return;
            fetch(`${WILAYAH_BASE}/districts/${id}.json`)
                .then(r => r.json())
                .then(data => { fillSelect(districtSelect, data, '-- Pilih Kecamatan --'); districtSelect.disabled = false; });
        });

        districtSelect.addEventListener('change', function () {
            document.getElementById('f_district').value = this.value;
        });

        function resetAddressForm() {
            document.getElementById('addressForm').reset();
            document.getElementById('addressForm').action = '{{ route('customer.addresses.store') }}';
            document.getElementById('addressMethodField').innerHTML = '';
            document.getElementById('addressModalTitle').innerText = 'Tambah Alamat';
            citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            citySelect.disabled = true;
            districtSelect.disabled = true;
        }

        function openAddressModal() {
            resetAddressForm();
            document.getElementById('addressModalOverlay').classList.add('open');
        }

        function closeAddressModal() {
            document.getElementById('addressModalOverlay').classList.remove('open');
        }

        async function editAddress(address) {
            resetAddressForm();
            document.getElementById('addressModalTitle').innerText = 'Ubah Alamat';
            document.getElementById('addressForm').action = `/akun/alamat/${address.id}`;
            document.getElementById('addressMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('f_label').value = address.label;
            document.getElementById('f_recipient_name').value = address.recipient_name;
            document.getElementById('f_recipient_phone').value = address.recipient_phone;
            document.getElementById('f_postal_code').value = address.postal_code || '';
            document.getElementById('f_address_detail').value = address.address_detail;
            document.getElementById('f_is_default').checked = !!address.is_default;

            document.getElementById('f_province').value = address.province;
            document.getElementById('f_city').value = address.city;
            document.getElementById('f_district').value = address.district;

            // populate cascading selects with matching selected values
            const provinces = await (await fetch(`${WILAYAH_BASE}/provinces.json`)).json();
            fillSelect(provinceSelect, provinces, '-- Pilih Provinsi --');
            const province = provinces.find(p => p.name === address.province);
            if (province) {
                provinceSelect.value = province.name;
                const cities = await (await fetch(`${WILAYAH_BASE}/regencies/${province.id}.json`)).json();
                fillSelect(citySelect, cities, '-- Pilih Kota --');
                citySelect.disabled = false;
                const city = cities.find(c => c.name === address.city);
                if (city) {
                    citySelect.value = city.name;
                    const districts = await (await fetch(`${WILAYAH_BASE}/districts/${city.id}.json`)).json();
                    fillSelect(districtSelect, districts, '-- Pilih Kecamatan --');
                    districtSelect.disabled = false;
                    districtSelect.value = address.district;
                }
            }

            document.getElementById('addressModalOverlay').classList.add('open');
        }

        document.getElementById('addressModalOverlay').addEventListener('click', function (e) {
            if (e.target === this) closeAddressModal();
        });

        document.getElementById('addressForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!document.getElementById('f_province').value || !document.getElementById('f_city').value || !document.getElementById('f_district').value) {
                Swal.fire({ icon: 'warning', title: 'Lengkapi Alamat', text: 'Mohon pilih Provinsi, Kota, dan Kecamatan.' });
                return;
            }

            const form = e.target;
            const isEdit = !!document.getElementById('addressMethodField').innerHTML;
            const submitBtn = form.querySelector('button[type=submit]');
            submitBtn.disabled = true;
            showLoading();

            const payload = {
                label: document.getElementById('f_label').value,
                recipient_name: document.getElementById('f_recipient_name').value,
                recipient_phone: document.getElementById('f_recipient_phone').value,
                postal_code: document.getElementById('f_postal_code').value,
                province: document.getElementById('f_province').value,
                city: document.getElementById('f_city').value,
                district: document.getElementById('f_district').value,
                address_detail: document.getElementById('f_address_detail').value,
                is_default: document.getElementById('f_is_default').checked ? 1 : 0,
            };

            try {
                const res = await fetch(form.action, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();
                hideLoading();

                if (!res.ok) {
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Gagal menyimpan alamat.');
                    Swal.fire({ icon: 'error', title: 'Gagal', text: firstError });
                    return;
                }

                if (payload.is_default) {
                    addresses = addresses.map(a => ({ ...a, is_default: false }));
                }

                if (isEdit) {
                    addresses = addresses.map(a => a.id === data.address.id ? data.address : a);
                } else {
                    addresses.push(data.address);
                }

                renderAddressGrid();
                closeAddressModal();
                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1300, showConfirmButton: false });
            } catch (err) {
                hideLoading();
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan.' });
            } finally {
                submitBtn.disabled = false;
            }
        });
    </script>
@endpush
