@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Sales</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Sales</li>
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
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Sales</th>
                        <th class="px-4 py-3">No telepon</th>
                        <th class="px-4 py-3">Jumlah Penjualan</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salesPerson as $i => $sales)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-medium">{{ $sales->name }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-indigo-600">
                                {{ $sales->total_penjualan }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $sales->phone }}
                            </td>
                            <td class=" space-x-2">
                                <button onclick='openEditModal(@json($sales))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500 transition">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button onclick="deleteSales({{ $sales->id }})"
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

    <div id="salesModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl">

            <!-- HEADER -->
            <div class="flex items-center gap-3 px-6 py-4 border-b">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800">Tambah Sales</h2>
            </div>

            <!-- BODY -->
            <form id="salesForm" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <!-- NAMA -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Nama Sales
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



    @push('scripts')
        <script>
            $(document).ready(function() {

                $('#datatable').DataTable()

                const modal = $('#salesModal')
                const form = $('#salesForm')
                const title = $('#modalTitle')

                const name = $('#salesName')
                const method = $('#methodField')

                const btn = $('#submitBtn')
                const btnText = $('#btnText')
                const loader = $('#loader')

                window.openCreateModal = function() {
                    modal.removeClass('hidden')
                    title.text('Tambah Sales')

                    form.attr('action', '/penjuals')
                    method.val('')
                    name.val('')
                }

                window.openEditModal = function(data) {
                    modal.removeClass('hidden')
                    title.text('Edit Sales')

                    form.attr('action', `/penjuals/${data.id}`)
                    method.val('PUT')
                    name.val(data.name)
                    $('#salesPhone').val(data.phone)
                }

                window.closeModal = function() {
                    modal.addClass('hidden')
                }

                form.on('submit', function() {
                    btn.prop('disabled', true).addClass('opacity-70')
                    btnText.text('Menyimpan...')
                    loader.removeClass('hidden')
                })

                window.deleteSales = function(id) {
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
            })
        </script>
    @endpush

@endsection
