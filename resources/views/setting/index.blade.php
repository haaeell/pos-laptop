@extends('layouts.app')

@section('title', 'Setting Toko')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
    <div class="max-w-4xl mx-auto">

        <!-- HEADER -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Pengaturan Toko</h1>
            <p class="text-sm text-slate-500 mt-1">
                Kelola identitas toko, informasi, dan lokasi peta
            </p>
        </div>

        <form method="POST" action="/settings" enctype="multipart/form-data"
            class="bg-white rounded-2xl shadow-sm border p-6 space-y-8">
            @csrf

            <!-- ================= LOGO ================= -->
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Logo Toko</h3>

                <div class="flex items-center gap-5">
                    <div class="w-24 h-24 rounded-xl border flex items-center justify-center bg-slate-50 overflow-hidden">
                        @if(isset($settings['logo']))
                            <img src="{{ asset('storage/' . $settings['logo']) }}" class="w-full h-full object-contain">
                        @else
                            <i class="fa-solid fa-image text-slate-300 text-3xl"></i>
                        @endif
                    </div>

                    <div>
                        <input type="file" name="logo" class="block text-sm text-slate-600
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-xl file:border-0
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100">
                        <p class="text-xs text-slate-500 mt-1">
                            PNG / JPG, disarankan ukuran persegi
                        </p>
                    </div>
                </div>
            </div>

            <!-- ================= INFORMASI TOKO ================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-600">
                        Nama Toko
                    </label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-store absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="nama_toko" value="{{ $settings['nama_toko'] ?? '' }}"
                            placeholder="Nama toko anda" class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm
                                                  focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-600">
                        Jam Operasional
                    </label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="jam_buka" value="{{ $settings['jam_buka'] ?? '' }}"
                            placeholder="09.00 - 21.00" class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm
                                                  focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                </div>

            </div>

            <!-- ================= ALAMAT ================= -->
            <div>
                <label class="text-xs font-semibold uppercase text-slate-600">
                    Alamat Toko
                </label>
                <textarea name="alamat" rows="3" placeholder="Alamat lengkap toko"
                    class="w-full mt-1 rounded-xl border px-4 py-3 text-sm
                                             focus:ring-2 focus:ring-indigo-500/30">{{ $settings['alamat'] ?? '' }}</textarea>
            </div>

            <!-- ================= DESKRIPSI ================= -->
            <div>
                <label class="text-xs font-semibold uppercase text-slate-600">
                    Deskripsi Singkat
                </label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang toko"
                    class="w-full mt-1 rounded-xl border px-4 py-3 text-sm
                                             focus:ring-2 focus:ring-indigo-500/30">{{ $settings['deskripsi'] ?? '' }}</textarea>
            </div>

            <!-- ================= SEO & FAVICON ================= -->
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-1">SEO &amp; Favicon</h3>
                <p class="text-xs text-slate-500 mb-3">
                    Kata kunci ini membantu toko Anda muncul di pencarian Google (mis. "toko komputer Subang").
                </p>

                <label class="text-xs font-semibold uppercase text-slate-600">Kata Kunci SEO (pisahkan dengan koma)</label>
                <textarea name="meta_keywords" rows="2" placeholder="Barokah Computer Subang, toko komputer Subang, jual laptop Subang, service laptop Subang"
                    class="w-full mt-1 mb-5 rounded-xl border px-4 py-3 text-sm
                                             focus:ring-2 focus:ring-indigo-500/30">{{ $settings['meta_keywords'] ?? '' }}</textarea>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach ([
                        'favicon_512' => 'Favicon 512x512',
                        'favicon_48' => 'Favicon 48x48',
                        'favicon_32' => 'Favicon 32x32',
                    ] as $field => $label)
                        <div>
                            <label class="text-xs font-semibold uppercase text-slate-600">{{ $label }}</label>
                            <div class="flex items-center gap-3 mt-1">
                                <div class="w-14 h-14 rounded-lg border flex items-center justify-center bg-slate-50 overflow-hidden shrink-0">
                                    @if(isset($settings[$field]))
                                        <img src="{{ asset('storage/' . $settings[$field]) }}" class="w-full h-full object-contain">
                                    @else
                                        <i class="fa-solid fa-image text-slate-300"></i>
                                    @endif
                                </div>
                                <input type="file" name="{{ $field }}" accept="image/png" class="block text-xs text-slate-600
                                                  file:mr-2 file:py-1.5 file:px-3
                                                  file:rounded-lg file:border-0
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100">
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 mt-2">Format PNG, ukuran sesuai label. Jika kosong, pakai logo toko sebagai fallback.</p>
            </div>

            <!-- ================= MIDTRANS ================= -->
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-1">Integrasi Pembayaran (Midtrans)</h3>
                <p class="text-xs text-slate-500 mb-3">
                    Ambil Server Key &amp; Client Key dari dashboard Midtrans Anda (Settings &rarr; Access Keys).
                    Server Key bersifat rahasia dan tidak pernah dikirim ke browser pelanggan.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-semibold uppercase text-slate-600">Server Key</label>
                        <input type="password" name="midtrans_server_key"
                            value="{{ $settings['midtrans_server_key'] ?? '' }}" placeholder="SB-Mid-server-xxxxx"
                            autocomplete="off"
                            class="w-full mt-1 px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500/30">
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase text-slate-600">Client Key</label>
                        <input type="text" name="midtrans_client_key"
                            value="{{ $settings['midtrans_client_key'] ?? '' }}" placeholder="SB-Mid-client-xxxxx"
                            autocomplete="off"
                            class="w-full mt-1 px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                </div>

                <label class="flex items-center gap-2 mt-4 text-sm text-slate-700">
                    <input type="hidden" name="midtrans_is_production" value="0">
                    <input type="checkbox" name="midtrans_is_production" value="1"
                        {{ ($settings['midtrans_is_production'] ?? '0') === '1' ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Mode Produksi (nonaktifkan untuk mode Sandbox/Testing)
                </label>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border">
                    <label class="text-xs font-semibold uppercase text-slate-600">Notification URL Midtrans</label>
                    <p class="text-xs text-slate-500 mb-1.5">
                        Salin URL ini ke dashboard Midtrans pada menu Settings &rarr; Payment Notification URL.
                    </p>
                    <input type="text" readonly value="{{ $midtransNotificationUrl }}" onclick="this.select()"
                        class="w-full px-3 py-2 rounded-lg border bg-white text-xs font-mono text-slate-600">
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-1">Referral Marketing</h3>
                <p class="text-xs text-slate-500 mb-3">
                    Nominal ini menjadi potongan harga customer saat kode referral marketing dipakai di checkout.
                    Potongan otomatis memotong fee marketing dan akan dibatasi agar fee tidak pernah minus.
                </p>

                <div class="max-w-md">
                    <label class="text-xs font-semibold uppercase text-slate-600">Nominal Diskon Referral</label>
                    <div class="relative mt-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                        <input type="text" id="referralDiscountAmountText"
                            value="{{ number_format((float) ($settings['referral_discount_amount'] ?? 0), 0, ',', '.') }}"
                            placeholder="0"
                            class="w-full pl-12 pr-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500/30">
                        <input type="hidden" name="referral_discount_amount" id="referralDiscountAmount"
                            value="{{ (int) ($settings['referral_discount_amount'] ?? 0) }}">
                    </div>
                </div>
            </div>

            <!-- ================= BITESHIP ================= -->
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-1">Integrasi Pengiriman (Biteship)</h3>
                <p class="text-xs text-slate-500 mb-3">
                    Ambil API Key dari dashboard Biteship Anda (Pengaturan &rarr; Tambah Kunci API). Kunci dengan awalan
                    <code>biteship_test.</code> untuk mode uji coba, <code>biteship_live.</code> untuk produksi.
                </p>

                <div class="mb-4">
                    <label class="text-xs font-semibold uppercase text-slate-600">API Key</label>
                    <input type="password" name="biteship_api_key" value="{{ $settings['biteship_api_key'] ?? '' }}"
                        placeholder="biteship_test.xxxxx" autocomplete="off"
                        class="w-full mt-1 px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-slate-600">Nama Kontak Pengirim</label>
                        <input type="text" name="biteship_origin_contact_name"
                            value="{{ $settings['biteship_origin_contact_name'] ?? '' }}"
                            class="w-full mt-1 px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-slate-600">No. HP Kontak Pengirim</label>
                        <input type="text" name="biteship_origin_contact_phone"
                            value="{{ $settings['biteship_origin_contact_phone'] ?? '' }}"
                            class="w-full mt-1 px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-xs font-semibold uppercase text-slate-600">Alamat Pengambilan (Pickup)</label>
                    <textarea name="biteship_origin_address" rows="2" placeholder="Alamat lengkap toko untuk pickup kurir"
                        id="biteshipOriginAddress"
                        class="w-full mt-1 rounded-xl border px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500/30">{{ $settings['biteship_origin_address'] ?? '' }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-slate-600">Latitude Pickup</label>
                        <input type="text" name="biteship_origin_latitude" id="biteshipOriginLatitude"
                            value="{{ $settings['biteship_origin_latitude'] ?? '' }}" readonly
                            class="w-full mt-1 px-4 py-2.5 rounded-xl border text-sm bg-slate-50 text-slate-600 focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-slate-600">Longitude Pickup</label>
                        <input type="text" name="biteship_origin_longitude" id="biteshipOriginLongitude"
                            value="{{ $settings['biteship_origin_longitude'] ?? '' }}" readonly
                            class="w-full mt-1 px-4 py-2.5 rounded-xl border text-sm bg-slate-50 text-slate-600 focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                </div>

                <div class="mb-4 rounded-2xl border overflow-hidden bg-white">
                    <div class="px-4 py-3 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <strong class="block text-sm text-slate-700">Pilih titik pickup di peta</strong>
                            <span class="text-xs text-slate-500">Klik peta atau geser marker supaya titik pengambilan kurir lebih akurat.</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="biteshipUseCurrentLocationBtn"
                                class="px-3 py-2 rounded-full border text-xs font-semibold text-slate-700 hover:border-indigo-400 hover:text-indigo-600">
                                Lokasi Saya
                            </button>
                            <button type="button" id="biteshipResetMapBtn"
                                class="px-3 py-2 rounded-full border text-xs font-semibold text-slate-700 hover:border-indigo-400 hover:text-indigo-600">
                                Reset Titik
                            </button>
                        </div>
                    </div>
                    <div id="biteshipOriginMap" style="height: 320px;"></div>
                    <div class="px-4 py-3 text-xs text-slate-500 border-t bg-slate-50">
                        Koordinat ini disimpan untuk alamat pengiriman admin dan membantu proses pickup serta cek ongkir.
                    </div>
                </div>

                <div class="mb-2 relative">
                    <label class="text-xs font-semibold uppercase text-slate-600">Lokasi Asal (untuk Cek Ongkir)</label>
                    <input type="text" id="biteshipAreaSearch" autocomplete="off"
                        placeholder="Ketik nama kecamatan/kota, mis. Binong Subang"
                        class="w-full mt-1 px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500/30">
                    <input type="hidden" name="biteship_origin_area_id" id="biteshipOriginAreaId"
                        value="{{ $settings['biteship_origin_area_id'] ?? '' }}">
                    <div id="biteshipAreaResults"
                        class="absolute z-10 left-0 right-0 bg-white border rounded-xl shadow-lg mt-1 hidden max-h-56 overflow-y-auto">
                    </div>
                    <p class="text-xs text-slate-500 mt-1.5">
                        Lokasi tersimpan saat ini:
                        <strong id="biteshipAreaCurrent">{{ $settings['biteship_origin_area_name'] ?? 'Belum diatur' }}</strong>
                    </p>
                    <input type="hidden" name="biteship_origin_area_name" id="biteshipOriginAreaName"
                        value="{{ $settings['biteship_origin_area_name'] ?? '' }}">
                </div>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border">
                    <label class="text-xs font-semibold uppercase text-slate-600">Webhook URL</label>
                    <p class="text-xs text-slate-500 mb-1.5">
                        Salin URL ini ke dashboard Biteship (Pengaturan &rarr; Webhook) agar update status pengiriman
                        masuk otomatis.
                    </p>
                    <input type="text" readonly value="{{ $biteshipWebhookUrl }}" onclick="this.select()"
                        class="w-full px-3 py-2 rounded-lg border bg-white text-xs font-mono text-slate-600">
                </div>
            </div>

            <!-- ================= ACTION ================= -->
            <div class="pt-4 flex justify-end">
                <button class="px-6 py-2.5 bg-gradient-to-br from-indigo-600 to-blue-600
                                       text-white rounded-xl text-sm font-semibold
                                       hover:opacity-90 transition">
                    <i class="fa-solid fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        let biteshipSearchTimeout = null;
        const referralDiscountAmountText = document.getElementById('referralDiscountAmountText');
        const referralDiscountAmount = document.getElementById('referralDiscountAmount');
        const biteshipAreaSearch = document.getElementById('biteshipAreaSearch');
        const biteshipAreaResults = document.getElementById('biteshipAreaResults');
        const biteshipOriginLatitude = document.getElementById('biteshipOriginLatitude');
        const biteshipOriginLongitude = document.getElementById('biteshipOriginLongitude');
        const biteshipOriginAddress = document.getElementById('biteshipOriginAddress');
        const DEFAULT_MAP_CENTER = [-6.563246, 107.760467];
        let biteshipMap = null;
        let biteshipMarker = null;

        async function fetchJson(url, options = undefined) {
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error('Request failed');
            }
            return response.json();
        }

        async function searchBiteshipAreaByQuery(q) {
            return fetchJson('{{ route('settings.biteship.search-area') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ q }),
            });
        }

        biteshipAreaSearch.addEventListener('input', function () {
            clearTimeout(biteshipSearchTimeout);
            const q = this.value.trim();

            if (q.length < 3) {
                biteshipAreaResults.classList.add('hidden');
                return;
            }

            biteshipSearchTimeout = setTimeout(async () => {
                const data = await searchBiteshipAreaByQuery(q);
                const areas = data.areas || [];

                if (!areas.length) {
                    biteshipAreaResults.innerHTML = '<div class="px-4 py-2.5 text-xs text-slate-400">Tidak ditemukan</div>';
                } else {
                    biteshipAreaResults.innerHTML = areas.map(a => `
                        <div class="px-4 py-2.5 text-xs hover:bg-indigo-50 cursor-pointer border-b last:border-0" data-id="${a.id}" data-name="${a.name}">
                            ${a.name}
                        </div>
                    `).join('');
                }
                biteshipAreaResults.classList.remove('hidden');

                biteshipAreaResults.querySelectorAll('[data-id]').forEach(el => {
                    el.addEventListener('click', function () {
                        document.getElementById('biteshipOriginAreaId').value = this.dataset.id;
                        document.getElementById('biteshipOriginAreaName').value = this.dataset.name;
                        document.getElementById('biteshipAreaCurrent').innerText = this.dataset.name;
                        biteshipAreaSearch.value = this.dataset.name;
                        biteshipAreaResults.classList.add('hidden');
                    });
                });
            }, 350);
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#biteshipAreaSearch') && !e.target.closest('#biteshipAreaResults')) {
                biteshipAreaResults.classList.add('hidden');
            }
        });

        function setBiteshipMapPoint(lat, lng, { syncAddress = true } = {}) {
            biteshipOriginLatitude.value = Number(lat).toFixed(7);
            biteshipOriginLongitude.value = Number(lng).toFixed(7);

            const point = [Number(lat), Number(lng)];
            if (biteshipMarker) {
                biteshipMarker.setLatLng(point);
            } else {
                biteshipMarker = L.marker(point, { draggable: true }).addTo(biteshipMap);
                biteshipMarker.on('dragend', () => {
                    const markerPoint = biteshipMarker.getLatLng();
                    setBiteshipMapPoint(markerPoint.lat, markerPoint.lng, { syncAddress: true });
                });
            }

            biteshipMap.setView(point, Math.max(biteshipMap.getZoom(), 15));

            if (syncAddress) {
                reverseGeocodeBiteshipPoint(lat, lng);
            }
        }

        async function reverseGeocodeBiteshipPoint(lat, lng) {
            try {
                const data = await fetchJson(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=id`);
                const address = data.address || {};
                const areaQuery = [address.city_district || address.suburb || address.state_district || address.village, address.city || address.county || address.municipality || address.town]
                    .filter(Boolean)
                    .join(' ');

                const detailParts = [
                    address.road,
                    address.house_number,
                    address.neighbourhood,
                    address.hamlet,
                    address.residential,
                    address.suburb,
                ].filter(Boolean);

                if (detailParts.length) {
                    biteshipOriginAddress.value = detailParts.join(', ');
                } else if (data.display_name) {
                    biteshipOriginAddress.value = data.display_name;
                }

                if (areaQuery.length >= 3) {
                    biteshipAreaSearch.value = areaQuery;
                    const areaData = await searchBiteshipAreaByQuery(areaQuery);
                    const bestArea = areaData.areas?.[0];
                    if (bestArea) {
                        document.getElementById('biteshipOriginAreaId').value = bestArea.id;
                        document.getElementById('biteshipOriginAreaName').value = bestArea.name;
                        document.getElementById('biteshipAreaCurrent').innerText = bestArea.name;
                    }
                }
            } catch (error) {
                console.warn('Reverse geocoding pickup gagal', error);
            }
        }

        function initBiteshipMap() {
            if (biteshipMap) {
                return;
            }

            biteshipMap = L.map('biteshipOriginMap', { zoomControl: true }).setView(DEFAULT_MAP_CENTER, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(biteshipMap);

            biteshipMap.on('click', (event) => {
                setBiteshipMapPoint(event.latlng.lat, event.latlng.lng, { syncAddress: true });
            });

            document.getElementById('biteshipUseCurrentLocationBtn').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    Swal.fire({ icon: 'warning', title: 'Lokasi Tidak Tersedia', text: 'Browser ini tidak mendukung geolocation.' });
                    return;
                }

                navigator.geolocation.getCurrentPosition((position) => {
                    setBiteshipMapPoint(position.coords.latitude, position.coords.longitude, { syncAddress: true });
                }, () => {
                    Swal.fire({ icon: 'warning', title: 'Lokasi Gagal Diambil', text: 'Izinkan akses lokasi atau pilih titik langsung di peta.' });
                }, {
                    enableHighAccuracy: true,
                    timeout: 12000,
                });
            });

            document.getElementById('biteshipResetMapBtn').addEventListener('click', () => {
                biteshipOriginLatitude.value = '';
                biteshipOriginLongitude.value = '';
                if (biteshipMarker) {
                    biteshipMap.removeLayer(biteshipMarker);
                    biteshipMarker = null;
                }
                biteshipMap.setView(DEFAULT_MAP_CENTER, 12);
            });

            if (biteshipOriginLatitude.value && biteshipOriginLongitude.value) {
                setBiteshipMapPoint(biteshipOriginLatitude.value, biteshipOriginLongitude.value, { syncAddress: false });
            }
        }

        if (referralDiscountAmountText && referralDiscountAmount) {
            referralDiscountAmountText.addEventListener('input', function () {
                const raw = this.value.replace(/\D/g, '');
                referralDiscountAmount.value = raw || 0;
                this.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
            });
        }

        initBiteshipMap();
    </script>
@endpush
