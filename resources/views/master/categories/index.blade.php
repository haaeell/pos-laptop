@extends('layouts.app')

@section('title', 'Kategori')

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

        .icon-picker-grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 6px; }
        .icon-picker-item {
            display: flex; align-items: center; justify-content: center;
            aspect-ratio: 1; border-radius: 0.65rem; border: 1px solid #e2e8f0;
            cursor: pointer; font-size: 16px; color: #475569; transition: .15s;
        }
        .icon-picker-item:hover { background: #eef2ff; border-color: #6366f1; color: #4f46e5; }
        .icon-picker-item.selected { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    </style>
@endpush

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Kategori</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Kategori</li>
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
                        <th class="px-4 py-3">Icon</th>
                        <th class="px-4 py-3">Nama Kategori</th>
                        <th class="px-4 py-3">Produk Tersedia</th>
                        <th class="px-4 py-3 text-center">Tampil di Customer</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $i => $category)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div class="w-9 h-9 rounded-lg border flex items-center justify-center bg-slate-50 text-indigo-600">
                                            <i class="{{ $category->icon ?: 'fa-solid fa-layer-group' }}"></i>
                                        </div>
                                    </td>
                                    <td class="font-medium">{{ $category->name }}</td>
                                    <td class="px-4 py-3">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                                                                                {{ $category->available_products_count > 0
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-slate-200 text-slate-500' }}">
                                                {{ $category->available_products_count }}
                                            </span>

                                            <a href="/products?category={{ $category->id }}"
                                                class="text-xs font-medium text-indigo-600 hover:text-indigo-800
                                                                                                                      transition inline-flex items-center gap-1"
                                                title="Lihat produk">
                                                <i class="fa-solid fa-eye text-[11px]"></i>
                                                <span class="hidden sm:inline">Lihat</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('categories.toggle-visibility', $category->id) }}" method="POST">
                                            @csrf
                                            <label class="switch">
                                                <input type="checkbox" onchange="this.form.submit()" {{ $category->show_on_customer_site ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td class=" space-x-2">
                                        <button onclick='openEditModal(@json($category))'
                                            class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500 transition">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button onclick="deleteCategory({{ $category->id }})"
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

    <div id="categoryModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto">

            <!-- HEADER -->
            <div class="flex items-center gap-3 px-6 py-4 border-b">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h2 id="modalTitle" class="text-base font-bold text-slate-800">Tambah Kategori</h2>
            </div>

            <!-- BODY -->
            <form id="categoryForm" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="methodField">
                <input type="hidden" name="icon" id="categoryIcon">

                <!-- NAMA -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Nama Kategori
                    </label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-font absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="name" id="categoryName" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm
                                                                focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    </div>
                </div>

                <!-- ICON PICKER -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Icon Kategori
                    </label>
                    <div class="flex items-center gap-3 mt-1">
                        <div id="iconPreview" class="w-12 h-12 rounded-xl border flex items-center justify-center bg-slate-50 text-indigo-600 text-lg shrink-0">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div class="relative flex-1">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" id="iconSearch" placeholder="Cari icon... (mis. laptop, kamera)"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm
                                    focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>
                    </div>
                    <div id="iconPickerGrid" class="icon-picker-grid mt-3 max-h-40 overflow-y-auto p-1"></div>
                </div>

                <!-- TAMPIL DI CUSTOMER -->
                <div class="flex items-center justify-between rounded-xl border px-4 py-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-700">Tampilkan di Customer</div>
                        <div class="text-xs text-slate-500">Kategori akan muncul di landing page &amp; katalog</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="show_on_customer_site" id="categoryShowOnCustomer" value="1" checked>
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
            $(document).ready(function () {

                $('#datatable').DataTable()

                const modal = $('#categoryModal')
                const form = $('#categoryForm')
                const title = $('#modalTitle')

                const name = $('#categoryName')
                const method = $('#methodField')
                const iconInput = $('#categoryIcon')
                const iconPreview = $('#iconPreview')
                const iconSearch = $('#iconSearch')
                const iconGrid = $('#iconPickerGrid')
                const showOnCustomer = $('#categoryShowOnCustomer')

                let allIcons = []

                function renderIcon(iconClass) {
                    iconPreview.html(`<i class="${iconClass}"></i>`)
                }

                function renderIconGrid(filter) {
                    const q = (filter || '').toLowerCase().trim()
                    const filtered = q
                        ? allIcons.filter(i => i.toLowerCase().includes(q))
                        : allIcons

                    iconGrid.html(filtered.slice(0, 60).map(iconClass => `
                        <div class="icon-picker-item ${iconClass === iconInput.val() ? 'selected' : ''}" data-icon="${iconClass}" title="${iconClass}">
                            <i class="${iconClass}"></i>
                        </div>
                    `).join(''))
                }

                fetch('/vendor/fontawesome-icons.json')
                    .then(res => res.json())
                    .then(icons => {
                        allIcons = icons
                        renderIconGrid('')
                    })

                iconSearch.on('input', function () {
                    renderIconGrid($(this).val())
                })

                iconGrid.on('click', '.icon-picker-item', function () {
                    const iconClass = $(this).data('icon')
                    iconInput.val(iconClass)
                    renderIcon(iconClass)
                    iconGrid.find('.icon-picker-item').removeClass('selected')
                    $(this).addClass('selected')
                })

                window.openCreateModal = function () {
                    modal.removeClass('hidden')
                    title.text('Tambah Kategori')

                    form.attr('action', '/categories')
                    method.val('')
                    name.val('')
                    iconInput.val('fa-solid fa-layer-group')
                    renderIcon('fa-solid fa-layer-group')
                    showOnCustomer.prop('checked', true)
                    iconSearch.val('')
                    renderIconGrid('')
                }

                window.openEditModal = function (data) {
                    modal.removeClass('hidden')
                    title.text('Edit Kategori')

                    form.attr('action', `/categories/${data.id}`)
                    method.val('PUT')
                    name.val(data.name)
                    iconInput.val(data.icon || 'fa-solid fa-layer-group')
                    renderIcon(data.icon || 'fa-solid fa-layer-group')
                    showOnCustomer.prop('checked', !!data.show_on_customer_site)
                    iconSearch.val('')
                    renderIconGrid('')
                }

                window.closeModal = function () {
                    modal.addClass('hidden')
                }

                window.deleteCategory = function (id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Kategori akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form')
                            form.method = 'POST'
                            form.action = `/categories/${id}`
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
