@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Data Karyawan</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Karyawan</li>
                    </ol>
                </nav>
            </div>

            <button onclick="openCreateModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                + Tambah Karyawan
            </button>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">No Pegawai</th>
                        <th class="px-4 py-3">Nama Lengkap</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">No HP</th>
                        <th class="px-4 py-3">Gaji Pokok</th>
                        <th class="px-4 py-3">Rekening</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $i => $employee)
                        <tr>
                            <td class="px-4 py-3">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $employee->employee_number }}</td>
                            <td class="px-4 py-3 font-medium">{{ $employee->full_name }}</td>
                            <td class="px-4 py-3">{{ $employee->position }}</td>
                            <td class="px-4 py-3">{{ $employee->phone }}</td>
                            <td class="px-4 py-3 text-right font-semibold">
                                Rp {{ number_format($employee->basic_salary, 0, ',', '.') }}
                            </td>
                            <td>
                                <div class="flex flex-col leading-tight">
                                    <span class="text-sm font-semibold text-slate-700">
                                        {{ $employee->bank_name }}
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        {{ $employee->account_number }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($employee->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Aktif</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-x-2">
                                <button onclick='openEditModal(@json($employee))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500 transition">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button onclick="deleteEmployee({{ $employee->id }})"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL -->
    <div id="employeeModal" class="fixed inset-0 hidden z-50 overflow-y-auto bg-black/40 backdrop-blur-sm">

        <div class="min-h-screen px-4 py-8 flex items-start justify-center">
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl">

                <!-- HEADER -->
                <div class="flex items-center gap-3 px-6 py-4 border-b">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <h2 id="modalTitle" class="text-base font-bold text-slate-800">Tambah Karyawan</h2>
                </div>

                <!-- BODY -->
                <form id="employeeForm" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="_method" id="methodField">

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Nama Lengkap</label>
                            <input type="text" name="full_name" id="full_name" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                                Link ke Sales (Opsional)
                            </label>
                            <select name="sales_person_id" id="sales_person_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                                <option value="">-- Pilih Sales yang sudah ada --</option>
                                @foreach ($salesPersons as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                @endforeach
                            </select>

                            {{-- ✅ Info otomatis, ganti checkbox --}}
                            <p class="mt-1.5 text-xs text-slate-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-info text-indigo-400"></i>
                                Jika tidak dipilih, karyawan akan <strong class="text-indigo-500 mx-1">otomatis didaftarkan
                                    sebagai Sales baru</strong>.
                            </p>
                        </div>
                        <!-- Jabatan -->
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Jabatan</label>
                            <input type="text" name="position" id="position" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>

                        <!-- No HP -->
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">No HP</label>
                            <input type="text" name="phone" id="phone" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>

                        <!-- Tanggal Bergabung -->
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Tanggal
                                Bergabung</label>
                            <input type="date" name="join_date" id="join_date" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="birth_date" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>

                        <!-- Alamat -->
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Alamat</label>
                            <textarea name="address" id="address" rows="2" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"></textarea>
                        </div>

                        <!-- Nama Bank -->
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Nama Bank</label>
                            <input type="text" name="bank_name" id="bank_name"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>

                        <!-- Nomor Rekening -->
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Nomor Rekening</label>
                            <input type="text" name="account_number" id="account_number"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>

                        <!-- Gaji Pokok -->
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                                Gaji Pokok
                            </label>

                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">
                                    Rp
                                </span>

                                <input type="text" name="basic_salary" id="basic_salary" required autocomplete="off"
                                    class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"
                                    placeholder="0">
                            </div>
                        </div>

                        <!-- Status Aktif (Edit only) -->
                        <div class="col-span-2" id="activeStatusContainer" style="display: none;">
                            <label class="inline-flex items-center text-sm text-slate-600">
                                <input type="checkbox" name="is_active" id="is_active" checked
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 font-bold">Karyawan Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#datatable').DataTable();

                const modal = $('#employeeModal');
                const form = $('#employeeForm');
                const title = $('#modalTitle');

                const salaryInput = document.getElementById('basic_salary');

                function formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID').format(value);
                }

                salaryInput.addEventListener('input', function (e) {
                    let value = this.value.replace(/\D/g, '');

                    if (value === '') {
                        this.value = '';
                        return;
                    }

                    this.value = formatRupiah(value);
                });

                document.getElementById('employeeForm').addEventListener('submit', function () {
                    salaryInput.value = salaryInput.value.replace(/\./g, '');
                });

                window.openCreateModal = function () {
                    modal.removeClass('hidden');
                    title.text('Tambah Karyawan');
                    form.attr('action', '/employees');
                    $('#methodField').val('');
                    form[0].reset();
                    $('#activeStatusContainer').hide();
                }

                window.openEditModal = function (data) {
                    modal.removeClass('hidden');
                    title.text('Edit Karyawan');
                    form.attr('action', `/employees/${data.id}`);
                    $('#methodField').val('PUT');

                    $('#full_name').val(data.full_name);
                    $('#sales_person_id').val(data.sales_person_id);
                    $('#position').val(data.position);
                    $('#phone').val(data.phone);
                    $('#join_date').val(data.join_date);
                    $('#birth_date').val(data.birth_date);
                    $('#address').val(data.address);
                    $('#bank_name').val(data.bank_name);
                    $('#account_number').val(data.account_number);
                    $('#basic_salary').val(data.basic_salary);
                    $('#is_active').prop('checked', data.is_active);

                    $('#activeStatusContainer').show();
                    $('#create_as_sales').closest('div').hide();
                }

                window.closeModal = function () {
                    modal.addClass('hidden');
                }

                window.deleteEmployee = function (id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Karyawan akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/employees/${id}`;
                            form.innerHTML = `
                                                                                                                                        <input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">
                                                                                                                                        <input type="hidden" name="_method" value="DELETE">
                                                                                                                                    `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection