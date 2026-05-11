@extends('layouts.app')

@section('title', 'Penggajian')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Penggajian</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Penggajian</li>
                    </ol>
                </nav>
            </div>

            <a href="/payrolls/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                + Buat Penggajian
            </a>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">No. Penggajian</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Tgl Rilis</th>
                        <th class="px-4 py-3">Jml Karyawan</th>
                        <th class="px-4 py-3 text-right">Total Penggajian</th>
                        <th class="px-4 py-3">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $months = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember'
                        ];
                    @endphp

                    @foreach ($payrolls as $i => $payroll)
                        <tr class="hover:bg-slate-50 border-b border-slate-100">
                            <td class="px-4 py-3 text-center">{{ $i + 1 }}</td>

                            <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">
                                {{ $payroll->payroll_number }}
                            </td>

                            <td class="px-4 py-3 font-medium">
                                {{ $months[$payroll->period_month] }} {{ $payroll->period_year }}
                            </td>

                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($payroll->release_date)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">
                                    {{ $payroll->details->count() }} orang
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right font-bold text-slate-800">
                                Rp {{ number_format($payroll->total_amount, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $payroll->releasedBy?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($payroll->status === 'released')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                        <i class="fa-solid fa-check mr-1"></i>Dirilis
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                                        <i class="fa-solid fa-clock mr-1"></i>Draft
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">

                                    <!-- DETAIL -->
                                    <div class="relative group">
                                        <button onclick="openDetailModal({{ $payroll->id }})"
                                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition shadow-sm">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>

                                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 whitespace-nowrap
                                                                        bg-slate-900 text-white text-xs px-2.5 py-1.5 rounded-lg
                                                                        opacity-0 invisible group-hover:opacity-100 group-hover:visible
                                                                        transition duration-200 shadow-lg z-10">
                                            Lihat Detail
                                        </div>
                                    </div>

                                    @if ($payroll->status === 'draft')

                                        <!-- RILIS -->
                                        <div class="relative group">
                                            <button onclick="releasePayroll({{ $payroll->id }})"
                                                class="w-9 h-9 flex items-center justify-center rounded-lg bg-green-500 text-white hover:bg-green-600 transition shadow-sm">
                                                <i class="fa-solid fa-paper-plane text-sm"></i>
                                            </button>

                                            <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 whitespace-nowrap
                                                                                        bg-slate-900 text-white text-xs px-2.5 py-1.5 rounded-lg
                                                                                        opacity-0 invisible group-hover:opacity-100 group-hover:visible
                                                                                        transition duration-200 shadow-lg z-10">
                                                Rilis Gaji
                                            </div>
                                        </div>

                                        <!-- HAPUS -->
                                        <div class="relative group">
                                            <button onclick="deletePayroll({{ $payroll->id }})"
                                                class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-500 text-white hover:bg-red-600 transition shadow-sm">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>

                                            <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 whitespace-nowrap
                                                                                        bg-slate-900 text-white text-xs px-2.5 py-1.5 rounded-lg
                                                                                        opacity-0 invisible group-hover:opacity-100 group-hover:visible
                                                                                        transition duration-200 shadow-lg z-10">
                                                Hapus Penggajian
                                            </div>
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

    <!-- MODAL DETAIL -->
    <div id="detailModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="min-h-screen px-4 py-8 flex items-start justify-center">
            <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl">

                <!-- HEADER MODAL -->
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Detail Penggajian</h2>
                            <p id="detailSubtitle" class="text-xs text-slate-500"></p>
                        </div>
                    </div>
                    <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- BODY MODAL -->
                <div class="p-6">

                    <!-- SUMMARY -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-indigo-50 rounded-xl p-4">
                            <div class="text-xs text-indigo-500 font-bold uppercase">Total Penggajian</div>
                            <div id="detailTotal" class="text-xl font-bold text-indigo-700 mt-1"></div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <div class="text-xs text-slate-500 font-bold uppercase">Jumlah Karyawan</div>
                            <div id="detailCount" class="text-xl font-bold text-slate-700 mt-1"></div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <div class="text-xs text-slate-500 font-bold uppercase">Status</div>
                            <div id="detailStatus" class="mt-1"></div>
                        </div>
                    </div>

                    <!-- TABLE DETAIL -->
                    <div class="overflow-x-auto rounded-xl border">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100 text-slate-700">
                                <tr>
                                    <th class="px-4 py-3">Karyawan</th>
                                    <th class="px-4 py-3 text-right">Gaji Pokok</th>
                                    <th class="px-4 py-3 text-right">Bonus Sales</th>
                                    <th class="px-4 py-3 text-right">Jasa Teknisi</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-center">Slip</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#datatable').DataTable({
                    order: [[0, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [8] }
                    ],
                    language: {
                        emptyTable: `
                                <div class="py-10 text-slate-400">
                                    <i class="fa-solid fa-inbox text-3xl mb-3 block"></i>
                                    Belum ada data penggajian
                                </div>
                            `
                    }
                });
            });
            // Data payrolls dari blade
            const payrolls = @json($payrolls->load('details.employee'));

            const months = [
                '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            window.openDetailModal = function (id) {
                const payroll = payrolls.find(p => p.id === id);
                if (!payroll) return;

                $('#detailSubtitle').text(
                    `${payroll.payroll_number} · ${months[payroll.period_month]} ${payroll.period_year}`
                );
                $('#detailTotal').text(formatRupiah(payroll.total_amount));
                $('#detailCount').text(payroll.details.length + ' orang');
                $('#detailStatus').html(
                    payroll.status === 'released'
                        ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fa-solid fa-check mr-1"></i>Dirilis</span>'
                        : '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><i class="fa-solid fa-clock mr-1"></i>Draft</span>'
                );

                let rows = '';
                payroll.details.forEach(detail => {
                    rows += `
                                                        <tr class="border-t hover:bg-slate-50">
                                                            <td class="px-4 py-3">
                                                                <div class="font-medium">${detail.employee.full_name}</div>
                                                                <div class="text-xs text-slate-400">${detail.employee.position}</div>
                                                            </td>
                                                            <td class="px-4 py-3 text-right">${formatRupiah(detail.basic_salary)}</td>
                                                            <td class="px-4 py-3 text-right text-green-600">${formatRupiah(detail.sales_bonus)}</td>
                                                            <td class="px-4 py-3 text-right text-blue-600">${formatRupiah(detail.technician_fee)}</td>
                                                            <td class="px-4 py-3 text-right font-bold text-indigo-600">${formatRupiah(detail.net_salary)}</td>
                                                            <td class="px-4 py-3 text-center">
                                                                <a href="/payrolls/${payroll.id}/slip/${detail.employee_id}"
                                                                    target="_blank"
                                                                    class="px-2 py-1 bg-slate-100 text-slate-600 rounded hover:bg-slate-200 transition text-xs">
                                                                    <i class="fa-solid fa-print mr-1"></i>Cetak
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    `;
                });

                $('#detailTableBody').html(rows);
                $('#detailModal').removeClass('hidden');
            }

            window.closeDetailModal = function () {
                $('#detailModal').addClass('hidden');
            }

            window.releasePayroll = function (id) {
                Swal.fire({
                    title: 'Rilis Penggajian?',
                    text: 'Penggajian akan dirilis dan tidak bisa diubah lagi.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Rilis'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/payrolls/${id}/release`;
                        form.innerHTML = `
                                                            <input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">
                                                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            window.deletePayroll = function (id) {
                Swal.fire({
                    title: 'Hapus Penggajian?',
                    text: 'Data penggajian akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/payrolls/${id}`;
                        form.innerHTML = `
                                                            <input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection