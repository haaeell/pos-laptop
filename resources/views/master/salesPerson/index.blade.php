@extends('layouts.app')

@section('title', 'Kategori')

@push('styles')
    <style>
        #datatable_wrapper .dataTables_length,
        #datatable_wrapper .dataTables_filter {
            margin-bottom: 1rem;
            color: #334155;
            font-size: 0.95rem;
        }

        #datatable_wrapper .dataTables_length select,
        #datatable_wrapper .dataTables_filter input {
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            padding: 0.55rem 0.85rem;
            background: #fff;
            color: #0f172a;
        }

        #datatable_wrapper .dataTables_filter input {
            min-width: 220px;
        }

        #datatable_wrapper .dataTables_info,
        #datatable_wrapper .dataTables_paginate {
            margin-top: 1rem;
            color: #475569;
        }

        #datatable_wrapper .paginate_button {
            border-radius: 0.75rem !important;
        }

        #datatable_wrapper .paginate_button.current {
            background: #eef2ff !important;
            border: 1px solid #c7d2fe !important;
            color: #4338ca !important;
        }

        #datatable thead th {
            white-space: nowrap;
            vertical-align: middle;
        }

        #datatable tbody td {
            vertical-align: middle;
        }

        .marketing-table td,
        .marketing-table th {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .marketing-name {
            min-width: 180px;
        }

        .marketing-phone {
            min-width: 130px;
            color: #475569;
        }

        .marketing-referral {
            max-width: 320px;
        }

        .marketing-referral-code {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            padding: 0.45rem 0.7rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 0.78rem;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .marketing-actions {
            min-width: 220px;
        }

        .marketing-actions-wrap {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: nowrap;
        }

        .marketing-action-btn {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.85rem;
            flex-shrink: 0;
        }

        .marketing-employee-badge {
            display: inline-flex;
            align-items: center;
            height: 42px;
            padding: 0 0.9rem;
            border-radius: 0.85rem;
            background: #f1f5f9;
            color: #94a3b8;
            font-size: 0.78rem;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
        }

        @media (max-width: 1024px) {
            #datatable_wrapper .dataTables_filter input {
                min-width: 160px;
            }

            .marketing-referral {
                max-width: 240px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Marketing</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Marketing</li>
                    </ol>
                </nav>
            </div>

            <button onclick="openCreateModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                + Tambah
            </button>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="marketing-table w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">No telepon</th>
                        <th class="px-4 py-3">Kode Referral</th>
                        <th class="px-4 py-3">Fee</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Jumlah Penjualan</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salesPerson as $i => $sales)
                        <tr>
                            <td class="font-semibold text-slate-600">{{ $i + 1 }}</td>
                            <td class="marketing-name font-semibold text-slate-800">{{ $sales->name }}</td>

                            <td class="marketing-phone px-4 py-3">
                                {{ $sales->phone ?: '-' }}
                            </td>
                            <td class="marketing-referral px-4 py-3">
                                <span class="marketing-referral-code font-mono uppercase" title="{{ $sales->referral_code }}">
                                    {{ $sales->referral_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                Rp {{ number_format((float) $sales->fee, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $sales->active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $sales->active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-indigo-600">
                                {{ $sales->total_penjualan }}
                            </td>
                            <td class="marketing-actions px-4 py-3">
                                <div class="marketing-actions-wrap">
                                <button onclick='openEditModal(@json($sales))'
                                    class="marketing-action-btn bg-yellow-400 rounded hover:bg-yellow-500 transition">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                @if (!$sales->employee_id)
                                    <button onclick='openPromoteModal(@json($sales))'
                                        class="marketing-action-btn bg-green-500 text-white rounded hover:bg-green-600 transition"
                                        title="Angkat jadi karyawan">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </button>
                                @else
                                    <span class="marketing-employee-badge">
                                        <i class="fa-solid fa-check"></i> Karyawan
                                    </span>
                                @endif

                                <button onclick="deleteSales({{ $sales->id }})"
                                    class="marketing-action-btn bg-red-500 text-white rounded hover:bg-red-600 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="salesModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl">

            <!-- HEADER -->
            <div class="flex items-center gap-3 px-6 py-4 border-b">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h2 id="modalTitle" class="text-base font-bold text-slate-800">Tambah Sales</h2>
            </div>

            <!-- BODY -->
            <form id="salesForm" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <!-- NAMA -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Nama
                    </label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-font absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="name" id="salesName" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm
                                                                                                        focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        No Telepon
                    </label>
                    <div class="relative mt-1">
                        <i
                            class="fa-solid fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="phone" id="salesPhone" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm
                                                                                                        focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Kode Referral
                    </label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="referral_code" id="salesReferralCode" placeholder="SALES-ANDI"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm uppercase
                                                                                                        focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Kosongkan jika ingin dibuat otomatis dari nama marketing.</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Fee
                    </label>
                    <div class="relative mt-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">
                            Rp
                        </span>
                        <input type="text" id="salesFeeText" autocomplete="off" placeholder="0"
                            class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm
                                                                                                        focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        <input type="hidden" name="fee" id="salesFee" value="0">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" id="salesActive" value="1" checked
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="salesActive" class="text-sm text-slate-700">Kode referral aktif</label>
                </div>

                <!-- FOOTER -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-slate-50">
                        Batal
                    </button>

                    <button type="submit"
                        id="submitBtn"
                        class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                        <span id="btnText">Simpan</span>
                        <span id="loader" class="hidden ml-2">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ANGKAT JADI KARYAWAN -->
    <div id="promoteModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-y-auto max-h-[90vh]">

            <!-- HEADER -->
            <div class="flex items-center gap-3 px-6 py-4 border-b">
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Angkat Jadi Karyawan</h2>
                    <p id="promoteSubtitle" class="text-xs text-slate-500"></p>
                </div>
            </div>

            <!-- BODY -->
            <form id="promoteForm" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Jabatan -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Jabatan <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="position" required
                        class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Tanggal Bergabung -->
                    <div>
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Tgl. Bergabung <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="join_date" required
                            class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Tgl. Lahir <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="birth_date" required
                            class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Alamat <span
                            class="text-red-500">*</span></label>
                    <textarea name="address" rows="2" required
                        class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Nama Bank -->
                    <div>
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Nama Bank</label>
                        <input type="text" name="bank_name"
                            class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>

                    <!-- No. Rekening -->
                    <div>
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">No. Rekening</label>
                        <input type="text" name="account_number"
                            class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                </div>

                <!-- Gaji Pokok -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Gaji Pokok <span class="text-red-500">*</span>
                    </label>

                    <div class="relative mt-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">
                            Rp
                        </span>

                        <input type="text" name="basic_salary" id="basic_salary" required autocomplete="off" placeholder="0"
                            class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closePromoteModal()"
                        class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-green-600 text-white text-sm font-bold hover:bg-green-700">
                        <i class="fa-solid fa-user-tie mr-1"></i> Angkat Jadi Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>



    @push('scripts')
        <script>
            $(document).ready(function () {

                $('#datatable').DataTable()

                const salaryInput = document.getElementById('basic_salary')

                function formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID').format(value)
                }

                salaryInput.addEventListener('input', function () {
                    let value = this.value.replace(/\D/g, '')

                    if (value === '') {
                        this.value = ''
                        return
                    }

                    this.value = formatRupiah(value)
                })

                $('#promoteForm').on('submit', function () {
                    salaryInput.value = salaryInput.value.replace(/\./g, '')
                })

                const modal = $('#salesModal')
                const form = $('#salesForm')
                const title = $('#modalTitle')
                const phone = $('#salesPhone')
                const referralCode = $('#salesReferralCode')
                const feeText = $('#salesFeeText')
                const fee = $('#salesFee')
                const active = $('#salesActive')

                const name = $('#salesName')
                const method = $('#methodField')

                const btn = $('#submitBtn')
                const btnText = $('#btnText')
                const loader = $('#loader')

                feeText.on('input', function () {
                    const raw = this.value.replace(/\D/g, '')
                    fee.val(raw || 0)
                    this.value = raw ? formatRupiah(raw) : ''
                })

                referralCode.on('input', function () {
                    this.value = this.value.toUpperCase()
                })

                window.openCreateModal = function () {
                    modal.removeClass('hidden')
                    title.text('Tambah Sales')

                    form.attr('action', '/penjuals')
                    method.val('')
                    name.val('')
                    phone.val('')
                    referralCode.val('')
                    fee.val(0)
                    feeText.val('')
                    active.prop('checked', true)
                    btn.prop('disabled', false).removeClass('opacity-70')
                    btnText.text('Simpan')
                    loader.addClass('hidden')
                }

                window.openEditModal = function (data) {
                    modal.removeClass('hidden')
                    title.text('Edit Sales')

                    form.attr('action', `/penjuals/${data.id}`)
                    method.val('PUT')
                    name.val(data.name)
                    phone.val(data.phone)
                    referralCode.val(data.referral_code ?? '')
                    fee.val(data.fee ?? 0)
                    feeText.val((data.fee && Number(data.fee) > 0) ? formatRupiah(data.fee) : '')
                    active.prop('checked', Boolean(Number(data.active)))
                    btn.prop('disabled', false).removeClass('opacity-70')
                    btnText.text('Simpan')
                    loader.addClass('hidden')
                }

                window.closeModal = function () {
                    modal.addClass('hidden')
                }

                form.on('submit', function () {
                    btn.prop('disabled', true).addClass('opacity-70')
                    btnText.text('Menyimpan...')
                    loader.removeClass('hidden')
                })

                window.deleteSales = function (id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Sales akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form')
                            form.method = 'POST'
                            form.action = `/penjuals/${id}`
                            form.innerHTML =
                                `
                                                                                                                                                                                                                            <input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">
                                                                                                                                                                                                                            <input type="hidden" name="_method" value="DELETE">
                                                                                                                                                                                                                        `
                            document.body.appendChild(form)
                            form.submit()
                        }
                    })
                }

                window.openPromoteModal = function (data) {
                    $('#promoteModal').removeClass('hidden')
                    $('#promoteSubtitle').text('Sales: ' + data.name + (data.phone ? ' · ' + data.phone : ''))
                    $('#promoteForm').attr('action', `/penjuals/${data.id}/promote`)
                    $('#promoteForm')[0].reset()
                }

                window.closePromoteModal = function () {
                    $('#promoteModal').addClass('hidden')
                }
            })
        </script>
    @endpush

@endsection
