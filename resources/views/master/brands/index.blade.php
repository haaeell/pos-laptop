@extends('layouts.app')

@section('title', 'Brand')

@push('styles')
    <style>
        .switch { position: relative; display: inline-block; width: 42px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .switch-slider {
            position: absolute; inset: 0; cursor: pointer;
            background-color: #cbd5e1; transition: .2s; border-radius: 999px;
        }
        .switch-slider::before {
            content: ""; position: absolute; height: 18px; width: 18px;
            left: 3px; bottom: 3px; background-color: #fff; transition: .2s; border-radius: 50%;
        }
        .switch input:checked + .switch-slider { background-color: #10b981; }
        .switch input:checked + .switch-slider::before { transform: translateX(18px); }
    </style>
@endpush

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Brand</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Brand</li>
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
                        <th width="5%">#</th>
                        <th width="10%">Logo</th>
                        <th>Nama Brand</th>
                        <th>Produk Tersedia</th>
                        <th width="14%" class="text-center">Partner di Landing</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $i => $brand)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div class="w-12 h-9 rounded-lg border flex items-center justify-center bg-slate-50 overflow-hidden">
                                            @if($brand->logo)
                                                <img src="{{ asset('storage/' . $brand->logo) }}" class="w-full h-full object-contain">
                                            @else
                                                <i class="fa-solid fa-tags text-slate-300"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="font-medium">{{ $brand->name }}</td>
                                    <td class="px-4 py-3">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                                                                                {{ $brand->available_products_count > 0
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-slate-200 text-slate-500' }}">
                                                {{ $brand->available_products_count }}
                                            </span>

                                            <a href="/products?brand={{ $brand->id }}"
                                                class="text-xs font-medium text-indigo-600 hover:text-indigo-800
                                                                                                                      transition inline-flex items-center gap-1"
                                                title="Lihat produk">
                                                <i class="fa-solid fa-eye text-[11px]"></i>
                                                <span class="hidden sm:inline">Lihat</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('brands.toggle-partner', $brand->id) }}" method="POST">
                                            @csrf
                                            <label class="switch">
                                                <input type="checkbox" onchange="this.form.submit()" {{ $brand->show_as_partner ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                        </form>
                                    </td>

                                    <td class="text-center space-x-2">
                                        <button onclick='openEditModal(@json($brand))'
                                            class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500 transition">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button onclick="deleteBrand({{ $brand->id }})"
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

    <div id="brandModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl">

            <!-- HEADER -->
            <div class="flex items-center gap-3 px-6 py-4 border-b">
                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <h2 id="modalTitle" class="text-base font-bold text-slate-800">Tambah Brand</h2>
            </div>

            <!-- BODY -->
            <form id="brandForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <!-- NAMA -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Nama Brand
                    </label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="name" id="brandName" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm
                                                                            focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    </div>
                </div>

                <!-- LOGO -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Logo Brand
                    </label>
                    <div class="flex items-center gap-4 mt-1">
                        <div id="logoPreview" class="w-16 h-12 rounded-lg border flex items-center justify-center bg-slate-50 overflow-hidden">
                            <i class="fa-solid fa-tags text-slate-300"></i>
                        </div>
                        <input type="file" name="logo" accept="image/*" class="block text-sm text-slate-600
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-xl file:border-0
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Logo ini akan tampil di landing page &amp; halaman produk.</p>
                </div>

                <!-- PARTNER -->
                <div class="flex items-center justify-between rounded-xl border px-4 py-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-700">Tampilkan sebagai Brand Partner</div>
                        <div class="text-xs text-slate-500">Muncul di section brand partner landing page</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="show_as_partner" id="brandShowAsPartner" value="1" checked>
                        <span class="switch-slider"></span>
                    </label>
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
            $(function () {

                $('#datatable').DataTable()

                const modal = $('#brandModal')
                const form = $('#brandForm')
                const title = $('#modalTitle')

                const name = $('#brandName')
                const method = $('#methodField')
                const logoPreview = $('#logoPreview')
                const showAsPartner = $('#brandShowAsPartner')

                window.openCreateModal = function () {
                    modal.removeClass('hidden')
                    title.text('Tambah Brand')

                    form.attr('action', '/brands')
                    method.val('')
                    name.val('')
                    showAsPartner.prop('checked', true)
                    logoPreview.html('<i class="fa-solid fa-tags text-slate-300"></i>')
                }

                window.openEditModal = function (data) {
                    modal.removeClass('hidden')
                    title.text('Edit Brand')

                    form.attr('action', `/brands/${data.id}`)
                    method.val('PUT')
                    name.val(data.name)
                    showAsPartner.prop('checked', !!data.show_as_partner)

                    if (data.logo) {
                        logoPreview.html(`<img src="/storage/${data.logo}" class="w-full h-full object-contain">`)
                    } else {
                        logoPreview.html('<i class="fa-solid fa-tags text-slate-300"></i>')
                    }
                }

                window.closeModal = function () {
                    modal.addClass('hidden')
                }

                form.on('submit', function () {
                    $('#submitBtn').prop('disabled', true).addClass('opacity-70')
                })

                window.deleteBrand = function (id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Brand akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form')
                            form.method = 'POST'
                            form.action = `/brands/${id}`
                            form.innerHTML = `
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
