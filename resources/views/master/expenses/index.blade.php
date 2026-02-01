@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Daftar Pengeluaran</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Pengeluaran</li>
                    </ol>
                </nav>
            </div>

            <button onclick="openCreateModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Catat Pengeluaran</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-red-50 border border-red-100 rounded-xl">
                <p class="text-xs font-bold text-red-600 uppercase">Total Pengeluaran</p>
                <h3 class="text-xl font-bold text-red-700">Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</h3>
            </div>
            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl">
                <p class="text-xs font-bold text-slate-600 uppercase">Jumlah Transaksi</p>
                <h3 class="text-xl font-bold text-slate-700">{{ $expenses->count() }} Transaksi</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Nominal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $i => $expense)
                        <tr class="border-b hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($expense->entry_date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">
                                {{ $expense->title }}
                                @if($expense->description)
                                    <p class="text-[10px] text-slate-400 font-normal">{{ Str::limit($expense->description, 40) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-slate-100 rounded-md text-[11px] font-semibold text-slate-600">
                                    {{ $expense->category ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-red-600">
                                Rp {{ number_format($expense->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center space-x-1">
                                <button onclick='openEditModal(@json($expense))'
                                    class="p-2 bg-yellow-400 rounded-lg hover:bg-yellow-500 transition text-white" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button onclick="deleteExpense({{ $expense->id }})"
                                    class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="expenseModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl transform transition-all">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-receipt text-lg"></i>
                    </div>
                    <h2 id="modalTitle" class="text-lg font-bold text-slate-800">Catat Pengeluaran</h2>
                </div>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="expenseForm" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div class="grid grid-cols-2 gap-5">
                    <div class="col-span-2">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Judul Pengeluaran</label>
                        <div class="relative mt-1.5">
                            <i class="fa-solid fa-font absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="title" id="expTitle" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition"
                                placeholder="Contoh: Bayar Listrik Toko">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Nominal (Rp)</label>
                        <div class="relative mt-1.5">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">Rp</span>
                            <input type="text" id="expAmountDisplay" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition font-bold text-slate-700"
                                placeholder="0" onkeyup="handleRupiahInput(this)">
                            <input type="hidden" name="amount" id="expAmountReal">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Tanggal</label>
                        <div class="relative mt-1.5">
                            <i class="fa-solid fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="date" name="entry_date" id="expDate" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition">
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Kategori</label>
                        <select name="category" id="expCategory"
                            class="w-full mt-1.5 px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition bg-white cursor-pointer">
                            <option value="Operasional">Operasional</option>
                            <option value="Gaji Karyawan">Gaji Karyawan</option>
                            <option value="Listrik & Air">Listrik & Air</option>
                            <option value="Sewa Tempat">Sewa Tempat</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Deskripsi (Opsional)</label>
                        <textarea name="description" id="expDesc" rows="3"
                            class="w-full mt-1.5 px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition"
                            placeholder="Tambahkan catatan singkat..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal()"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#datatable').DataTable({
                    language: {
                        searchPlaceholder: "Cari pengeluaran...",
                        search: ""
                    }
                });

                const modal = $('#expenseModal');
                const form = $('#expenseForm');
                const title = $('#modalTitle');
                const method = $('#methodField');

                // --- FUNGSI RUPIAH ---
                window.handleRupiahInput = function(el) {
                    let val = el.value.replace(/[^,\d]/g, "").toString();
                    let split = val.split(",");
                    let sisa = split[0].length % 3;
                    let rupiah = split[0].substr(0, sisa);
                    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        let separator = sisa ? "." : "";
                        rupiah += separator + ribuan.join(".");
                    }
                    el.value = rupiah;

                    // Simpan angka bersih ke hidden input
                    $('#expAmountReal').val(val.replace(/\./g, ''));
                }

                // --- MODAL ACTIONS ---
                window.openCreateModal = function () {
                    modal.removeClass('hidden').addClass('flex');
                    title.text('Catat Pengeluaran Baru');
                    form.attr('action', '/expenses');
                    method.val('POST');
                    form[0].reset();
                    $('#expAmountReal').val('');
                    $('#expDate').val(new Date().toISOString().split('T')[0]);
                }

                window.openEditModal = function (data) {
                    modal.removeClass('hidden').addClass('flex');
                    title.text('Edit Data Pengeluaran');
                    form.attr('action', `/expenses/${data.id}`);
                    method.val('PUT');

                    $('#expTitle').val(data.title);
                    $('#expDate').val(data.entry_date);
                    $('#expCategory').val(data.category);
                    $('#expDesc').val(data.description);

                    // Set Nominal
                    let amountClean = Math.floor(data.amount);
                    $('#expAmountReal').val(amountClean);

                    // Format displaynya
                    let displayEl = document.getElementById('expAmountDisplay');
                    displayEl.value = amountClean.toString();
                    handleRupiahInput(displayEl);
                }

                window.closeModal = function () {
                    modal.addClass('hidden').removeClass('flex');
                }

                // --- DELETE ACTION ---
                window.deleteExpense = function (id) {
                    Swal.fire({
                        title: 'Hapus data pengeluaran?',
                        text: "Data yang dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const delForm = document.createElement('form');
                            delForm.method = 'POST';
                            delForm.action = `/expenses/${id}`;
                            delForm.innerHTML = `@csrf @method('DELETE')`;
                            document.body.appendChild(delForm);
                            delForm.submit();
                        }
                    })
                }
            });
        </script>
    @endpush
@endsection
