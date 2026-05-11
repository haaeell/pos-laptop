@extends('layouts.app')
@section('title', 'Service')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Service</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Service</li>
                    </ol>
                </nav>
            </div>
            <button onclick="openCreateModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                + Tambah Service
            </button>
        </div>

        @php
            $stats = [
                ['label' => 'Menunggu', 'key' => 'pending', 'color' => 'bg-slate-100 text-slate-700'],
                ['label' => 'Estimasi', 'key' => 'estimated', 'color' => 'bg-yellow-100 text-yellow-700'],
                ['label' => 'Dikerjakan', 'key' => 'in_progress', 'color' => 'bg-indigo-100 text-indigo-700'],
                ['label' => 'Selesai', 'key' => 'done', 'color' => 'bg-green-100 text-green-700'],
            ];
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
            @foreach ($stats as $stat)
                <div class="rounded-xl p-4 {{ $stat['color'] }}">
                    <div class="text-xs font-bold uppercase">{{ $stat['label'] }}</div>
                    <div class="text-2xl font-bold mt-1">
                        {{ $services->where('status', $stat['key'])->count() }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">No. Service</th>
                        <th class="px-4 py-3">Konsumen</th>
                        <th class="px-4 py-3">Perangkat</th>
                        <th class="px-4 py-3">Keluhan</th>
                        <th class="px-4 py-3 text-right">Total Biaya</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $svc)
                        <tr class="hover:bg-slate-50 border-b border-slate-100">
                            <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">
                                {{ $svc->service_number }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $svc->customer_name }}</div>
                                <div class="text-xs text-slate-400">{{ $svc->customer_phone }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ $svc->device_brand }} {{ $svc->device_type }}</div>
                                @if($svc->device_sn)
                                    <div class="text-xs text-slate-400">SN: {{ $svc->device_sn }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <div class="truncate text-slate-600">{{ $svc->complaint }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">
                                @if($svc->total_cost > 0)
                                    Rp {{ number_format($svc->total_cost, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 text-xs">Belum diestimasi</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $svc->status_color }}">
                                    {{ $svc->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1 flex-wrap">
                                    <div class="tooltip-wrap">
                                        <button onclick='openDetailModal(@json($svc->load("technicians.employee")))'
                                            class="action-btn bg-indigo-500 hover:bg-indigo-600 text-white">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <span class="tooltip-text">Detail</span>
                                    </div>
                                    <div class="tooltip-wrap">
                                        <a href="/services/{{ $svc->id }}/print-receive" target="_blank"
                                            class="action-btn bg-slate-500 hover:bg-slate-600 text-white">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                        <span class="tooltip-text">Nota Terima</span>
                                    </div>
                                    @if ($svc->status === 'pending')
                                        <div class="tooltip-wrap">
                                            <button onclick='openEstimateModal(@json($svc))'
                                                class="action-btn bg-yellow-400 hover:bg-yellow-500 text-slate-800">
                                                <i class="fa-solid fa-calculator"></i>
                                            </button>
                                            <span class="tooltip-text">Input Estimasi</span>
                                        </div>
                                    @endif
                                    @if ($svc->status === 'estimated')
                                        <div class="tooltip-wrap">
                                            <button onclick='openConfirmModal(@json($svc))'
                                                class="action-btn bg-blue-500 hover:bg-blue-600 text-white">
                                                <i class="fa-solid fa-check-double"></i>
                                            </button>
                                            <span class="tooltip-text">Konfirmasi Konsumen</span>
                                        </div>
                                    @endif
                                    @if ($svc->status === 'in_progress')
                                        <div class="tooltip-wrap">
                                            <button onclick="markDone({{ $svc->id }})"
                                                class="action-btn bg-green-500 hover:bg-green-600 text-white">
                                                <i class="fa-solid fa-flag-checkered"></i>
                                            </button>
                                            <span class="tooltip-text">Tandai Selesai</span>
                                        </div>
                                    @endif
                                    @if ($svc->status === 'done' || $svc->status === 'taken')
                                        <div class="tooltip-wrap">
                                            <a href="/services/{{ $svc->id }}/print-pickup" target="_blank"
                                                class="action-btn bg-emerald-500 hover:bg-emerald-600 text-white">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </a>
                                            <span class="tooltip-text">Nota Pengambilan</span>
                                        </div>
                                    @endif
                                    @if ($svc->status === 'done')
                                        <div class="tooltip-wrap">
                                            <button onclick="markTaken({{ $svc->id }})"
                                                class="action-btn bg-teal-500 hover:bg-teal-600 text-white">
                                                <i class="fa-solid fa-handshake"></i>
                                            </button>
                                            <span class="tooltip-text">Sudah Diambil</span>
                                        </div>
                                    @endif
                                    @if (!in_array($svc->status, ['in_progress', 'done', 'taken']))
                                        <div class="tooltip-wrap">
                                            <button onclick="deleteService({{ $svc->id }})"
                                                class="action-btn bg-red-500 hover:bg-red-600 text-white">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <span class="tooltip-text">Hapus</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .tooltip-wrap {
            position: relative;
            display: inline-flex;
        }

        .tooltip-text {
            visibility: hidden;
            opacity: 0;
            background: #1e293b;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-align: center;
            border-radius: 7px;
            padding: 5px 10px;
            white-space: nowrap;
            position: absolute;
            bottom: calc(100% + 7px);
            left: 50%;
            transform: translateX(-50%);
            transition: opacity 0.18s ease, transform 0.18s ease;
            transform-origin: bottom center;
            pointer-events: none;
            z-index: 100;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
        }

        .tooltip-text::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: #1e293b;
        }

        .tooltip-wrap:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(-2px);
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            font-size: 12px;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .field-input {
            width: 100%;
            padding: 0.625rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .field-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .detail-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .detail-section-title {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 4px 0;
            gap: 8px;
        }

        .detail-label {
            font-size: 12px;
            color: #94a3b8;
            flex-shrink: 0;
        }

        .detail-value {
            font-size: 13px;
            font-weight: 500;
            color: #1e293b;
            text-align: right;
        }
    </style>

    <div id="createModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="min-h-screen px-4 py-8 flex items-start justify-center">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl">
                <div class="flex items-center gap-3 px-6 py-4 border-b">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-800">Tambah Data Service</h2>
                </div>
                <form id="createForm" action="/services" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="field-label">Nama Konsumen <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" required class="field-input mt-1">
                        </div>
                        <div class="col-span-2">
                            <label class="field-label">No. Telepon</label>
                            <input type="text" name="customer_phone" class="field-input mt-1">
                        </div>
                        <div>
                            <label class="field-label">Jenis Perangkat</label>
                            <input type="text" name="device_type" placeholder="Laptop, HP, PC..." class="field-input mt-1">
                        </div>
                        <div>
                            <label class="field-label">Brand</label>
                            <input type="text" name="device_brand" placeholder="Asus, Samsung..." class="field-input mt-1">
                        </div>
                        <div class="col-span-2">
                            <label class="field-label">Serial Number / IMEI</label>
                            <input type="text" name="device_sn" class="field-input mt-1">
                        </div>
                        <div class="col-span-2">
                            <label class="field-label">Keluhan / Kerusakan <span class="text-red-500">*</span></label>
                            <textarea name="complaint" rows="3" required class="field-input mt-1"></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="field-label">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" class="field-input mt-1"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="$('#createModal').addClass('hidden')"
                            class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-slate-50">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="estimateModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="min-h-screen px-4 py-8 flex items-start justify-center">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl">
                <div class="flex items-center gap-3 px-6 py-4 border-b">
                    <div class="w-9 h-9 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-800">Input Estimasi Biaya</h2>
                </div>
                <form id="estimateForm" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="field-label">Daftar Sparepart</label>
                            <button type="button" onclick="addSpareRow()"
                                class="text-xs px-3 py-1.5 bg-yellow-50 text-yellow-700 border border-yellow-300 rounded-lg hover:bg-yellow-100 transition font-semibold">
                                <i class="fa-solid fa-plus mr-1"></i>Tambah Item
                            </button>
                        </div>
                        <div id="spareRows" class="space-y-2"></div>
                        <div class="mt-2 flex justify-between items-center text-xs text-slate-500 border-t pt-2">
                            <span>Subtotal Sparepart</span>
                            <span id="spare_subtotal" class="font-bold text-slate-700">Rp 0</span>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Harga Jasa / Service (Rp)</label>
                        <div class="relative mt-1">
                            <input type="text" id="est_service_display" class="field-input pl-9" placeholder="0"
                                oninput="onServiceInput(this)">
                            <input type="hidden" name="service_cost" id="est_service" value="0">
                        </div>
                    </div>
                    <div
                        class="bg-yellow-50 rounded-xl px-4 py-3 flex justify-between items-center border border-yellow-200">
                        <span class="text-sm font-bold text-slate-700">Total Estimasi</span>
                        <span id="est_total" class="text-lg font-bold text-yellow-700">Rp 0</span>
                    </div>
                    <div>
                        <label class="field-label">Catatan Teknisi</label>
                        <textarea name="technician_notes" rows="2" class="field-input mt-1"></textarea>
                    </div>
                    <div>
                        <label class="field-label">Estimasi Selesai</label>
                        <input type="date" name="estimated_done" class="field-input mt-1">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="$('#estimateModal').addClass('hidden')"
                            class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-slate-50">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-yellow-500 text-white text-sm font-bold hover:bg-yellow-600">Simpan
                            Estimasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="min-h-screen px-4 py-8 flex items-start justify-center">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl">
                <div class="flex items-center gap-3 px-6 py-4 border-b">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Konfirmasi Konsumen</h2>
                        <p id="confirmSubtitle" class="text-xs text-slate-500"></p>
                    </div>
                </div>
                <form id="confirmForm" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="bg-slate-50 rounded-xl p-4 space-y-2 text-sm border border-slate-200">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Harga Sparepart</span>
                            <span id="conf_spare" class="font-semibold"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Harga Jasa</span>
                            <span id="conf_service" class="font-semibold"></span>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-1">
                            <span class="font-bold">Total</span>
                            <span id="conf_total" class="font-bold text-indigo-600 text-base"></span>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Keputusan Konsumen</label>
                        <div class="mt-2 flex gap-3">
                            <label
                                class="flex-1 flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                <input type="radio" name="decision" value="approved" class="text-green-600" checked>
                                <span class="text-sm font-semibold text-green-700"><i
                                        class="fa-solid fa-thumbs-up mr-1"></i>Setuju</span>
                            </label>
                            <label
                                class="flex-1 flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="radio" name="decision" value="rejected" class="text-red-600">
                                <span class="text-sm font-semibold text-red-700"><i
                                        class="fa-solid fa-thumbs-down mr-1"></i>Tidak Setuju</span>
                            </label>
                        </div>
                    </div>
                    <div id="technicianSection">
                        <label class="field-label">Pilih Teknisi <span class="text-red-500">*</span></label>
                        <p class="text-xs text-slate-400 mb-2">Fee jasa akan dibagi rata antar teknisi yang dipilih.</p>
                        <div class="space-y-2 max-h-48 overflow-y-auto border rounded-xl p-3">
                            @foreach (\App\Models\Employee::where('is_active', true)->get() as $emp)
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-2 rounded-lg">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="text-sm font-medium">{{ $emp->full_name }}</div>
                                        <div class="text-xs text-slate-400">{{ $emp->position }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="$('#confirmModal').addClass('hidden')"
                            class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-slate-50">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="detailModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="min-h-screen px-4 py-8 flex items-start justify-center">
            <div class="bg-white w-full max-w-xl rounded-2xl shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Detail Service</h2>
                            <p id="detail_number" class="text-xs text-indigo-500 font-mono font-bold"></p>
                        </div>
                    </div>
                    <button onclick="$('#detailModal').addClass('hidden')" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="p-6 space-y-5 text-sm" id="detailBody"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#datatable').DataTable({ order: [[0, 'desc']] });
            });

            function toRaw(str) { return parseInt(str.replace(/\D/g, '')) || 0; }
            function toRupiah(num) { return new Intl.NumberFormat('id-ID').format(num); }
            function formatRp(n) { return 'Rp ' + toRupiah(n); }

            window.openCreateModal = function () {
                $('#createModal').removeClass('hidden');
            }

            $('#createForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);

                fetch($(form).attr('action'), {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    redirect: 'follow'
                }).then(async res => {
                    const ct = res.headers.get('content-type') || '';
                    if (ct.includes('application/json')) {
                        const data = await res.json();
                        showSaveSuccess(data.id, data.service_number);
                    } else {
                        $('#createModal').addClass('hidden');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data service berhasil disimpan.',
                            confirmButtonColor: '#4f46e5'
                        }).then(() => location.reload());
                    }
                }).catch(() => {
                    Swal.fire('Gagal', 'Terjadi kesalahan, coba lagi.', 'error');
                });

                return false;
            });

            function showSaveSuccess(serviceId, serviceNumber) {
                $('#createModal').addClass('hidden');
                Swal.fire({
                    icon: 'success',
                    title: 'Service Tersimpan!',
                    html: `No. <b class="text-indigo-600 font-mono">${serviceNumber}</b> berhasil dibuat.`,
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-print mr-1"></i> Cetak Struk',
                    cancelButtonText: 'Tutup',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) window.open(`/services/${serviceId}/print-receive`, '_blank');
                    location.reload();
                });
            }

            let spareIndex = 0;

            function addSpareRow(name = '', price = 0) {
                const i = spareIndex++;
                const row = $(`
                                                                                                <div class="spare-row flex gap-2 items-center" data-index="${i}">
                                                                                                    <input type="text" name="spare_parts[${i}][name]" placeholder="Nama sparepart"
                                                                                                        value="${name}" class="field-input flex-1 text-sm py-2">
                                                                                                    <div class="relative w-40">
                                                                                                        <input type="text" placeholder="0"
                                                                                                            value="${price > 0 ? toRupiah(price) : ''}"
                                                                                                            class="field-input pl-8 pr-2 text-sm py-2 spare-price-display"
                                                                                                            oninput="onSparePriceInput(this)">
                                                                                                        <input type="hidden" name="spare_parts[${i}][price]" class="spare-price-raw" value="${price}">
                                                                                                    </div>
                                                                                                    <button type="button" onclick="removeSpareRow(this)"
                                                                                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center flex-shrink-0 transition">
                                                                                                        <i class="fa-solid fa-times text-xs"></i>
                                                                                                    </button>
                                                                                                </div>
                                                                                            `);
                $('#spareRows').append(row);
                calcEstTotal();
            }

            function removeSpareRow(btn) {
                $(btn).closest('.spare-row').remove();
                calcEstTotal();
            }

            function onSparePriceInput(el) {
                const raw = toRaw(el.value);
                el.value = raw > 0 ? toRupiah(raw) : '';
                $(el).siblings('.spare-price-raw').val(raw);
                calcEstTotal();
            }

            function onServiceInput(el) {
                const raw = toRaw(el.value);
                el.value = raw > 0 ? toRupiah(raw) : '';
                $('#est_service').val(raw);
                calcEstTotal();
            }

            function calcEstTotal() {
                let spareTotal = 0;
                $('.spare-price-raw').each(function () { spareTotal += parseInt($(this).val()) || 0; });
                const service = parseInt($('#est_service').val()) || 0;
                $('#spare_subtotal').text(formatRp(spareTotal));
                $('#est_total').text(formatRp(spareTotal + service));
            }

            window.openEstimateModal = function (data) {
                $('#estimateForm').attr('action', `/services/${data.id}/estimate`);
                $('#spareRows').empty();
                spareIndex = 0;

                let spareParts = data.spare_parts;
                if (typeof spareParts === 'string') {
                    try { spareParts = JSON.parse(spareParts); } catch { spareParts = []; }
                }
                if (!Array.isArray(spareParts)) spareParts = [];

                if (spareParts.length) {
                    spareParts.forEach(sp => addSpareRow(sp.name, sp.price));
                } else {
                    addSpareRow();
                }

                $('#est_service_display').val('');
                $('#est_service').val(0);
                calcEstTotal();
                $('#estimateModal').removeClass('hidden');
            }

            window.openConfirmModal = function (data) {
                $('#confirmForm').attr('action', `/services/${data.id}/confirm`);
                $('#confirmSubtitle').text(data.service_number + ' · ' + data.customer_name);
                $('#conf_spare').text(formatRp(data.spare_part_cost));
                $('#conf_service').text(formatRp(data.service_cost));
                $('#conf_total').text(formatRp(data.total_cost));
                $('#confirmModal').removeClass('hidden');
            }

            $('input[name="decision"]').on('change', function () {
                if ($(this).val() === 'approved') {
                    $('#technicianSection').show();
                } else {
                    $('#technicianSection').hide();
                }
            });

            window.openDetailModal = function (data) {
                if (typeof data.spare_parts === 'string') {
                    try { data.spare_parts = JSON.parse(data.spare_parts); } catch { data.spare_parts = []; }
                }
                if (!Array.isArray(data.spare_parts)) data.spare_parts = [];

                $('#detail_number').text(data.service_number);

                let spareList = '';
                if (data.spare_parts.length) {
                    spareList = data.spare_parts.map((sp, i) => `
                                                                                                    <div class="detail-row border-b border-slate-100 last:border-0 py-2">
                                                                                                        <span class="detail-label">${i + 1}. ${sp.name}</span>
                                                                                                        <span class="detail-value">${formatRp(sp.price)}</span>
                                                                                                    </div>
                                                                                                `).join('');
                } else {
                    spareList = `<p class="text-slate-400 text-xs italic">Tidak ada sparepart</p>`;
                }

                const techs = data.technicians?.length
                    ? data.technicians.map(t => `
                                                                                                    <div class="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
                                                                                                        <div class="flex items-center gap-2">
                                                                                                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                                                                                                ${t.employee.full_name.charAt(0)}
                                                                                                            </div>
                                                                                                            <span class="font-medium text-slate-700">${t.employee.full_name}</span>
                                                                                                        </div>
                                                                                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">${formatRp(t.fee_share)}</span>
                                                                                                    </div>
                                                                                                `).join('')
                    : '<p class="text-slate-400 text-xs italic">Belum ada teknisi</p>';

                const statusColors = {
                    pending: 'bg-slate-100 text-slate-600',
                    estimated: 'bg-yellow-100 text-yellow-700',
                    in_progress: 'bg-indigo-100 text-indigo-700',
                    done: 'bg-green-100 text-green-700',
                    taken: 'bg-emerald-100 text-emerald-700',
                    rejected: 'bg-red-100 text-red-600',
                };
                const statusLabels = {
                    pending: 'Menunggu Estimasi',
                    estimated: 'Estimasi Diberikan',
                    in_progress: 'Sedang Dikerjakan',
                    done: 'Selesai',
                    taken: 'Sudah Diambil',
                    rejected: 'Dibatalkan',
                };
                const statusBadge = `<span class="px-2 py-0.5 rounded-full text-xs font-bold ${statusColors[data.status] ?? 'bg-slate-100 text-slate-600'}">${statusLabels[data.status] ?? data.status}</span>`;

                $('#detailBody').html(`
                                                                                                <div class="detail-section">
                                                                                                    <div class="detail-section-title"><i class="fa-solid fa-user text-slate-400"></i> Informasi Konsumen</div>
                                                                                                    <div class="detail-row">
                                                                                                        <span class="detail-label">Nama</span>
                                                                                                        <span class="detail-value font-semibold">${data.customer_name}</span>
                                                                                                    </div>
                                                                                                    <div class="detail-row">
                                                                                                        <span class="detail-label">No. Telepon</span>
                                                                                                        <span class="detail-value">${data.customer_phone ?? '-'}</span>
                                                                                                    </div>
                                                                                                    <div class="detail-row">
                                                                                                        <span class="detail-label">Status</span>
                                                                                                        <span class="detail-value">${statusBadge}</span>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="detail-section">
                                                                                                    <div class="detail-section-title"><i class="fa-solid fa-laptop text-slate-400"></i> Perangkat</div>
                                                                                                    <div class="detail-row">
                                                                                                        <span class="detail-label">Brand / Jenis</span>
                                                                                                        <span class="detail-value">${((data.device_brand ?? '') + ' ' + (data.device_type ?? '')).trim() || '-'}</span>
                                                                                                    </div>
                                                                                                    ${data.device_sn ? `<div class="detail-row">
                                                                                                        <span class="detail-label">Serial Number</span>
                                                                                                        <span class="detail-value font-mono text-xs">${data.device_sn}</span>
                                                                                                    </div>` : ''}
                                                                                                    <div class="detail-row border-t border-slate-100 pt-2 mt-1">
                                                                                                        <span class="detail-label">Keluhan</span>
                                                                                                        <span class="detail-value text-slate-600 text-right max-w-xs">${data.complaint}</span>
                                                                                                    </div>
                                                                                                    ${data.notes ? `<div class="detail-row">
                                                                                                        <span class="detail-label">Catatan</span>
                                                                                                        <span class="detail-value text-slate-500 text-right max-w-xs">${data.notes}</span>
                                                                                                    </div>` : ''}
                                                                                                </div>

                                                                                                <div class="detail-section">
                                                                                                    <div class="detail-section-title"><i class="fa-solid fa-receipt text-slate-400"></i> Rincian Biaya</div>
                                                                                                    <div class="mb-2">
                                                                                                        <div class="text-xs text-slate-400 mb-1.5 font-semibold">Sparepart</div>
                                                                                                        ${spareList}
                                                                                                    </div>
                                                                                                    ${data.technician_notes ? `<div class="detail-row border-t border-slate-200 pt-2">
                                                                                                        <span class="detail-label">Catatan Teknisi</span>
                                                                                                        <span class="detail-value text-slate-500 text-right max-w-xs italic">${data.technician_notes}</span>
                                                                                                    </div>` : ''}
                                                                                                    <div class="detail-row border-t border-slate-200 pt-2">
                                                                                                        <span class="detail-label">Harga Jasa</span>
                                                                                                        <span class="detail-value">${formatRp(data.service_cost)}</span>
                                                                                                    </div>
                                                                                                    <div class="flex justify-between items-center mt-2 bg-indigo-600 text-white rounded-xl px-4 py-2.5">
                                                                                                        <span class="font-bold text-sm">Total Biaya</span>
                                                                                                        <span class="font-bold text-base">${formatRp(data.total_cost)}</span>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="detail-section">
                                                                                                    <div class="detail-section-title"><i class="fa-solid fa-wrench text-slate-400"></i> Teknisi</div>
                                                                                                    ${techs}
                                                                                                </div>
                                                                                            `);

                $('#detailModal').removeClass('hidden');
            }

            window.markDone = function (id) {
                Swal.fire({
                    title: 'Tandai Selesai?',
                    text: 'Service akan ditandai selesai dan siap diambil.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Selesai'
                }).then(result => {
                    if (result.isConfirmed) submitPost(`/services/${id}/done`);
                });
            }

            window.markTaken = function (id) {
                Swal.fire({
                    title: 'Barang Sudah Diambil?',
                    text: 'Status akan berubah menjadi Sudah Diambil.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d9488',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Diambil'
                }).then(result => {
                    if (result.isConfirmed) submitPost(`/services/${id}/taken`);
                });
            }

            window.deleteService = function (id) {
                Swal.fire({
                    title: 'Hapus Service?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Hapus'
                }).then(result => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/services/${id}`;
                        form.innerHTML = `
                                                                                                        <input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">
                                                                                                        <input type="hidden" name="_method" value="DELETE">
                                                                                                    `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function submitPost(url) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `<input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">`;
                document.body.appendChild(form);
                form.submit();
            }
        </script>
    @endpush
@endsection