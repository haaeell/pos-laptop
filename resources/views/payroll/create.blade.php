@extends('layouts.app')

@section('title', 'Buat Penggajian')

@section('content')
    <div class="mx-auto bg-white rounded-xl p-6">

        <!-- HEADER -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-800">Buat Penggajian Bulanan</h1>
            <nav class="text-sm text-slate-500 mt-1">
                <ol class="flex items-center gap-2">
                    <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                    <li>/</li>
                    <li><a href="/payrolls" class="hover:text-indigo-600">Penggajian</a></li>
                    <li>/</li>
                    <li class="text-slate-700 font-medium">Buat</li>
                </ol>
            </nav>
        </div>

        <!-- FORM PERIODE -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Periode Penggajian</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Dari Tanggal -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase">Dari Tanggal</label>
                    <input type="date" id="dateFrom" value="{{ date('Y-m-01') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm mt-1">
                </div>

                <!-- Sampai Tanggal -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase">Sampai Tanggal</label>
                    <input type="date" id="dateTo" value="{{ date('Y-m-t') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm mt-1">
                </div>

                <!-- Tanggal Rilis -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase">Tanggal Rilis</label>
                    <input type="date" id="releaseDate" value="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm mt-1">
                </div>
            </div>

            <!-- Info periode aktif -->
            <div id="periodInfo" class="mt-3 text-xs text-indigo-600 font-medium flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-days"></i>
                <span id="periodInfoText"></span>
            </div>

            <button onclick="calculatePayroll()"
                class="mt-4 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">
                <i class="fa-solid fa-calculator mr-2"></i> Hitung Gaji
            </button>
        </div>

        <!-- HASIL KALKULASI -->
        <div id="calculationResult" class="hidden">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Detail Penggajian</h2>
                    {{-- ✅ label rentang tanggal --}}
                    <p class="text-xs text-slate-500 mt-0.5">
                        <i class="fa-solid fa-calendar-range mr-1"></i>
                        <span id="periodRangeLabel"></span>
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-slate-500">Total Penggajian</div>
                    <div class="text-2xl font-bold text-indigo-600" id="totalPayroll">Rp 0</div>
                </div>
            </div>

            <div id="shareMethodSummary" class="hidden mb-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-semibold text-slate-500">Bagi Hasil Penjualan</div>
                        <div id="salesMethodText" class="mt-1 text-sm font-semibold text-slate-800"></div>
                        <div id="salesMethodCount" class="mt-0.5 text-xs text-slate-500"></div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-semibold text-slate-500">Bagi Hasil Sewa</div>
                        <div id="rentalMethodText" class="mt-1 text-sm font-semibold text-slate-800"></div>
                        <div id="rentalMethodCount" class="mt-0.5 text-xs text-slate-500"></div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="bg-white rounded-xl shadow border overflow-x-auto mb-6">
                <table class="w-full min-w-[1050px] text-sm">
                    <thead class="bg-slate-100 text-slate-700">
                        <tr>
                            <th class="px-4 py-3">No Pegawai</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3 text-right">Gaji Pokok</th>
                            <th class="px-4 py-3 text-right">Bonus Marketing</th>
                            <th class="px-4 py-3 text-right">Bagi Hasil Penjualan</th>
                            <th class="px-4 py-3 text-right">Bagi Hasil Sewa</th>
                            <th class="px-4 py-3 text-right">Jasa Teknisi</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody id="payrollTableBody">
                        <!-- Akan diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- CATATAN -->
            <div class="mb-6">
                <label class="text-xs font-bold text-slate-600 uppercase">Catatan (Opsional)</label>
                <textarea id="notes" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm mt-1"></textarea>
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-end gap-3">
                <a href="/payrolls" class="px-6 py-3 border rounded-xl font-bold hover:bg-slate-50">Batal</a>
                <button onclick="submitPayroll()"
                    class="px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition">
                    <i class="fa-solid fa-check mr-2"></i> Simpan Penggajian
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let payrollData = [];
            // Tampilkan info periode saat halaman load
            syncPeriod();

            function syncPeriod() {
                const from = $('#dateFrom').val();
                if (!from) return;

                const d = new Date(from);
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                $('#periodInfoText').text(
                    `Transaksi yang dihitung: ${formatDate($('#dateFrom').val())} s/d ${formatDate($('#dateTo').val())}`
                );
            }

            function formatDate(str) {
                if (!str) return '-';
                const d = new Date(str);
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                    'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
            }

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            function shareMethodDescription(mode, value, basis) {
                if (mode === 'fixed') {
                    return `${formatRupiah(value)} per transaksi`;
                }

                const percentage = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(value);
                return `${percentage}% dari ${basis} per transaksi`;
            }

            // Update info saat tanggal diubah
            $('#dateFrom, #dateTo').on('change', function () {
                syncPeriod();
                payrollData = [];
                $('#calculationResult').addClass('hidden');
            });

            async function calculatePayroll() {
                const dateFrom = $('#dateFrom').val();
                const dateTo = $('#dateTo').val();

                if (!dateFrom || !dateTo) {
                    Swal.fire('Perhatian', 'Harap isi rentang tanggal terlebih dahulu', 'warning');
                    return;
                }

                if (dateFrom > dateTo) {
                    Swal.fire('Perhatian', 'Tanggal awal tidak boleh lebih dari tanggal akhir', 'warning');
                    return;
                }

                // Reset state sebelum hit ulang
                payrollData = [];
                $('#shareMethodSummary').addClass('hidden');
                $('#payrollTableBody').html(`
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-slate-400">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Menghitung...
                    </td>
                </tr>
            `);
                $('#calculationResult').removeClass('hidden');
                $('#totalPayroll').text('Menghitung...');

                try {
                    const response = await $.ajax({
                        url: '/payrolls/calculate',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            date_from: dateFrom,
                            date_to: dateTo,
                        }
                    });

                    if (response.success) {
                        payrollData = response.data;
                        displayPayrollData(response.data, response.total_payroll, response.period_label);
                    }
                } catch (error) {
                    $('#payrollTableBody').html(`
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-red-400">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i> Gagal menghitung gaji. Coba lagi.
                        </td>
                    </tr>
                `);
                    $('#totalPayroll').text('Rp 0');
                }
            }

            function displayPayrollData(data, total, label) {
                let html = '';

                if (data.length) {
                    const method = data[0];
                    $('#salesMethodText').text(shareMethodDescription(method.sales_share_mode, method.sales_share_value, 'profit'));
                    $('#salesMethodCount').text(`${method.sales_transaction_count} transaksi lunas`);
                    $('#rentalMethodText').text(shareMethodDescription(method.rental_share_mode, method.rental_share_value, 'nominal sewa'));
                    $('#rentalMethodCount').text(`${method.rental_transaction_count} transaksi sewa`);
                    $('#shareMethodSummary').removeClass('hidden');
                }

                data.forEach(emp => {
                    html += `
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-3 font-mono text-xs">${emp.employee_number}</td>
                                                <td class="px-4 py-3 font-medium">${emp.full_name}</td>
                                                <td class="px-4 py-3">${emp.position}</td>
                                                <td class="px-4 py-3 text-right">${formatRupiah(emp.basic_salary)}</td>
                                                <td class="px-4 py-3 text-right text-green-600">${formatRupiah(emp.sales_bonus)}<div class="text-xs text-slate-500">${emp.total_transactions} transaksi</div></td>
                                                <td class="px-4 py-3 text-right text-green-600">${formatRupiah(emp.sales_profit_share)}<div class="text-xs text-slate-500">${emp.sales_transaction_count} transaksi</div></td>
                                                <td class="px-4 py-3 text-right text-green-600">${formatRupiah(emp.rental_share)}<div class="text-xs text-slate-500">${emp.rental_transaction_count} transaksi</div></td>
                                                <td class="px-4 py-3 text-right text-blue-600">${formatRupiah(emp.technician_fee)}</td>
                                                <td class="px-4 py-3 text-right font-bold text-indigo-600">${formatRupiah(emp.net_salary)}</td>
                                            </tr>
                                        `;
                });

                $('#payrollTableBody').html(html);
                $('#totalPayroll').text(formatRupiah(total));

                // Update judul section dengan label periode
                $('#periodRangeLabel').text(label);
            }

            async function submitPayroll() {
                const dateFrom = $('#dateFrom').val();
                const dateTo = $('#dateTo').val();
                const releaseDate = $('#releaseDate').val();
                const notes = $('#notes').val();

                if (payrollData.length === 0) {
                    Swal.fire('Error', 'Silakan hitung gaji terlebih dahulu', 'error');
                    return;
                }

                try {
                    await $.ajax({
                        url: '/payrolls',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            date_from: dateFrom,
                            date_to: dateTo,
                            release_date: releaseDate,
                            notes: notes,
                        }
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Penggajian berhasil dibuat'
                    }).then(() => {
                        window.location.href = '/payrolls';
                    });
                } catch (error) {
                    Swal.fire('Error', 'Gagal menyimpan penggajian', 'error');
                }
            }
        </script>
    @endpush
@endsection
