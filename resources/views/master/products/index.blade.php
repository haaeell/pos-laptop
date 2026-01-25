@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Produk</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Produk</li>
                    </ol>
                </nav>
            </div>

            <div class="flex gap-2">
                <button onclick="openImportModal()"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-import"></i> Import
                </button>
                <button onclick="openCreateModal()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    + Tambah
                </button>
            </div>

            <div id="importModal"
                class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
                <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-slate-200">

                    <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                        <h3 class="font-bold flex items-center gap-2">
                            <i class="fa-solid fa-file-excel"></i> Import Data Laptop
                        </h3>
                        <button onclick="closeImportModal()" class="hover:rotate-90 transition-transform">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="p-8">
                        <div class="flex items-start gap-4 mb-8">
                            <div
                                class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center shrink-0 font-bold">
                                1</div>
                            <div>
                                <h4 class="font-bold text-slate-800">Unduh Template</h4>
                                <p class="text-xs text-slate-500 mb-3">Gunakan format kami agar Nama Kategori & Brand
                                    terbaca otomatis.</p>
                                <a href="{{ route('products.template') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-indigo-700 text-xs font-bold rounded-lg transition">
                                    <i class="fa-solid fa-download"></i> Download Template.xlsx
                                </a>
                            </div>
                        </div>

                        <div class="border-l-2 border-dashed border-slate-200 ml-4 h-8 mb-4"></div>

                        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-start gap-4">
                                <div
                                    class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center shrink-0 font-bold">
                                    2</div>
                                <div class="w-full">
                                    <h4 class="font-bold text-slate-800">Upload File</h4>
                                    <p class="text-xs text-slate-500 mb-4">Pastikan kolom Nama Kategori sesuai dengan yang
                                        ada di sistem.</p>

                                    <div
                                        class="group relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-300 rounded-2xl hover:border-indigo-400 hover:bg-indigo-50/30 transition-all cursor-pointer">
                                        <input type="file" name="file"
                                            class="absolute inset-0 opacity-0 cursor-pointer" onchange="showPreview(this)"
                                            required>
                                        <i
                                            class="fa-solid fa-cloud-arrow-up text-3xl text-slate-300 group-hover:text-indigo-400 mb-2"></i>
                                        <span id="fileName" class="text-xs text-slate-400 font-medium">Seret file atau klik
                                            disini</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex gap-3">
                                <button type="button" onclick="closeImportModal()"
                                    class="flex-1 py-3 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">Batal</button>
                                <button type="submit"
                                    class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition">Proses
                                    Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mb-6">
            <div class="flex items-center gap-2 mb-4 text-slate-700">
                <i class="fa-solid fa-sliders text-indigo-600"></i>
                <h3 class="font-bold text-sm uppercase tracking-wider">Filter Pencarian</h3>
            </div>

            <form action="{{ route('products.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-5 gap-5 items-end">
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-500 uppercase ml-1">Kategori</label>
                    <select name="category" class="select2-filter w-full">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-500 uppercase ml-1">Brand</label>
                    <select name="brand" class="select2-filter w-full">
                        <option value="">Semua Brand</option>
                        @foreach ($brands as $br)
                            <option value="{{ $br->id }}" {{ request('brand') == $br->id ? 'selected' : '' }}>
                                {{ $br->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-500 uppercase ml-1 tracking-wider">Harga Minimal</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span
                                class="text-[10px] font-bold text-slate-400 group-focus-within:text-indigo-600 transition-colors">Rp</span>
                        </div>
                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                            class="block w-full pl-10 pr-3 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                            placeholder="0">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-500 uppercase ml-1 tracking-wider">Harga Maksimal</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span
                                class="text-[10px] font-bold text-slate-400 group-focus-within:text-indigo-600 transition-colors">Rp</span>
                        </div>
                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                            class="block w-full pl-10 pr-3 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                            placeholder="0">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white h-[42px] rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100 flex items-center justify-center gap-2 text-sm font-bold">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> Cari
                    </button>
                    <a href="{{ route('products.index') }}"
                        class="w-[42px] h-[42px] bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition flex items-center justify-center"
                        title="Reset Filter">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Brand</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $i => $product)
                        <tr>
                            <td class="text-nowrap">{{ $i + 1 }}</td>
                            <td>
                                <div
                                    class="w-12 h-12 rounded-lg border border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-image text-slate-300"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="text-nowrap">{{ $product->product_code }}</td>
                            <td class="text-nowrap" class="font-medium">{{ $product->name }}</td>
                            <td class="text-nowrap">{{ $product->category?->name }}</td>
                            <td class="text-nowrap">{{ $product->brand?->name }}</td>
                            <td class="text-nowrap">Rp {{ number_format($product->selling_price) }}</td>
                            <td class="text-nowrap">
                                <span
                                    class="px-2 py-1 text-xs rounded-full
                                                                                                {{ $product->status === 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($product->status == 'sold' ? 'Terjual' : 'Tersedia') }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap space-x-2">
                                <button onclick='openEditModal(@json($product))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button onclick="deleteProduct({{ $product->id }})"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
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
    <div id="productModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">

        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl border border-slate-200">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 id="modalTitle" class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-box text-indigo-600"></i>
                    <span>Tambah Produk</span>
                </h2>

                <button onclick="closeModal()"
                    class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-slate-100">
                    <i class="fa-solid fa-xmark text-slate-500"></i>
                </button>
            </div>

            <!-- BODY -->
            <form id="productForm" method="POST" class="px-6 py-5 space-y-5" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div class="grid grid-cols-2 gap-4">

                    <!-- KODE -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">
                            Kode Produk
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="product_code" id="productCode"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
                                    focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="PRD-001" required>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Foto Produk</label>
                        <div class="flex items-center gap-4">
                            <div id="imagePreviewContainer"
                                class="w-20 h-20 rounded-xl border-2 border-dashed border-slate-300 overflow-hidden flex items-center justify-center bg-slate-50">
                                <img id="imagePreview" src="" class="hidden w-full h-full object-cover">
                                <i id="imageIcon" class="fa-solid fa-image text-slate-300 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="image" id="productImage" accept="image/*"
                                    onchange="previewProductImage(this)"
                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>
                    </div>

                    <!-- NAMA -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">
                            Nama Produk
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-laptop absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="name" id="productName"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
                                    focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="Macbook Pro M1" required>
                        </div>
                    </div>

                    <!-- KATEGORI -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">
                            Kategori
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <select name="category_id" id="categoryId"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
                                    bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- BRAND -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">
                            Brand
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <select name="brand_id" id="brandId"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
                                    bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- HARGA BELI -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">
                            Harga Beli
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                            <input type="number" name="purchase_price" id="purchasePrice"
                                class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm
                                    focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="10000000">
                        </div>
                    </div>

                    <!-- HARGA JUAL -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">
                            Harga Jual
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                            <input type="text" id="sellingPriceDisplay" oninput="formatRupiahInput(this)"
                                class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm focus:border-indigo-500 transition"
                                placeholder="0">
                            <input type="hidden" name="selling_price" id="sellingPrice">
                        </div>
                    </div>

                    <!-- STATUS -->
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">
                            Status Produk
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-circle-info absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <select name="status" id="status"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
                                    bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                <option value="available">Available</option>
                                <option value="sold">Sold</option>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 hover:bg-slate-100 transition">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-5 py-2 text-sm font-semibold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">
                        Simpan Produk
                    </button>
                </div>

            </form>
        </div>
    </div>


    @push('scripts')
        <script>
            $(function() {
                $('.select2-filter').select2({
                    placeholder: "Pilih...",
                    allowClear: true,
                });
                $('#datatable').DataTable()

                const modal = $('#productModal')
                const form = $('#productForm')

                window.openCreateModal = function() {
                    modal.removeClass('hidden')
                    form.attr('action', '/products')
                    $('#methodField').val('')
                    form.trigger('reset')
                }

                window.openEditModal = function(data) {
                    modal.removeClass('hidden')
                    form.attr('action', `/products/${data.id}`)
                    $('#methodField').val('PUT')

                    $('#productCode').val(data.product_code)
                    $('#productName').val(data.name)
                    $('#categoryId').val(data.category_id)
                    $('#brandId').val(data.brand_id)
                    $('#purchasePrice').val(data.purchase_price)
                    $('#sellingPrice').val(data.selling_price)
                    $('#status').val(data.status)
                    if (data.image) {
                        $('#imagePreview').attr('src', `/storage/${data.image}`).removeClass('hidden');
                        $('#imageIcon').addClass('hidden');
                    } else {
                        $('#imagePreview').addClass('hidden');
                        $('#imageIcon').removeClass('hidden');
                    }

                    $('#sellingPriceDisplay').val(new Intl.NumberFormat('id-ID').format(data.selling_price));
                    $('#purchasePriceDisplay').val(new Intl.NumberFormat('id-ID').format(data.purchase_price));
                }

                window.closeModal = function() {
                    modal.addClass('hidden')
                }

                window.deleteProduct = function(id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Produk akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form')
                            form.method = 'POST'
                            form.action = `/products/${id}`
                            form.innerHTML = `
                                                    <input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                `
                            document.body.appendChild(form)
                            form.submit()
                        }
                    })
                }

                const importModal = $('#importModal');

                window.openImportModal = function() {
                    importModal.removeClass('hidden');
                }

                window.closeImportModal = function() {
                    importModal.addClass('hidden');
                    // Reset input file saat ditutup
                    $('#importFile').val('');
                    $('#fileName').text('Seret file atau klik disini').removeClass('text-indigo-600');
                }

                function previewProductImage(input) {
                    const preview = document.getElementById('imagePreview');
                    const icon = document.getElementById('imageIcon');
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            icon.classList.add('hidden');
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                // Format Rupiah OnInput
                function formatRupiahInput(el) {
                    let value = el.value.replace(/[^0-9]/g, '');
                    let hiddenInputId = el.id.replace('Display', '');
                    document.getElementById(hiddenInputId).value = value;

                    if (value) {
                        el.value = new Intl.NumberFormat('id-ID').format(value);
                    } else {
                        el.value = '';
                    }
                }
            })
        </script>
    @endpush
@endsection
