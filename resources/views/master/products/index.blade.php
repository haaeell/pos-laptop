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
                <a href="{{ route('products.export-pdf', request()->query()) }}" target="_blank"
                    class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                </a>
                <button onclick="printAllBarcodes()"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-800 transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Cetak Semua
                </button>
                @if (Auth::user()->isSuperAdmin())
                    <button onclick="openImportModal()"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
                        <i class="fa-solid fa-file-import"></i> Import
                    </button>
                @endif
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
                                        <input type="file" name="file" id="importFile"
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
                                <button type="submit" id="importBtn"
                                    class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold
                                                                                                                                                                                                                                                                                                                                                                                       rounded-xl flex items-center justify-center gap-2
                                                                                                                                                                                                                                                                                                                                                                                       disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span class="btn-text">Proses Import</span>
                                    <svg class="btn-loading hidden animate-spin h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </button>

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
                class="grid grid-cols-1 md:grid-cols-8 gap-5 items-end">

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
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                        <input type="text" id="minPriceDisplay"
                            value="{{ request('min_price') ? number_format(request('min_price'), 0, ',', '.') : '' }}"
                            oninput="formatRupiahInput(this)"
                            class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                            placeholder="0">
                        <input type="hidden" name="min_price" id="minPrice" value="{{ request('min_price') }}">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-500 uppercase ml-1 tracking-wider">Harga Maksimal</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                        <input type="text" id="maxPriceDisplay"
                            value="{{ request('max_price') ? number_format(request('max_price'), 0, ',', '.') : '' }}"
                            oninput="formatRupiahInput(this)"
                            class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                            placeholder="0">
                        <input type="hidden" name="max_price" id="maxPrice" value="{{ request('max_price') }}">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-500 uppercase ml-1">Status Produk</label>
                    <select name="status" class="select2-filter w-full">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Terjual</option>
                        <option value="bonus" {{ request('status') == 'bonus' ? 'selected' : '' }}>Bonus</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-500 uppercase ml-1">Tampil Katalog</label>
                    <select name="catalog_status" class="select2-filter w-full">
                        <option value="">Semua</option>
                        <option value="active" {{ request('catalog_status') == 'active' ? 'selected' : '' }}>Tampil</option>
                        <option value="inactive" {{ request('catalog_status') == 'inactive' ? 'selected' : '' }}>Sembunyikan</option>
                    </select>
                </div>

                <div class="space-y-1 md:col-span-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase ml-1">Scan Barcode</label>
                    <div class="relative">
                        <i class="fa-solid fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-indigo-500"></i>
                        <input type="text" id="barcodeScanner" name="barcode_search" value="{{ request('barcode_search') }}"
                            placeholder="Scan barcode atau ketik kode produk..." autocomplete="off"
                            class="w-full pl-10 pr-3 py-2.5 border border-indigo-300 rounded-xl text-sm
                                                                                                                   focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                                                                                                                   bg-indigo-50 placeholder:text-indigo-300" />
                    </div>
                </div>

                <div class="flex gap-2 md:col-span-7">
                    <button type="submit"
                        class="px-6 bg-indigo-600 text-white h-[42px] rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100 flex items-center justify-center gap-2 text-sm font-bold">
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
                        <th>Foto</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Brand</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        <th>Kondisi</th>
                        <th>Katalog</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $i => $product)
                        <tr>
                            <td class="text-nowrap">{{ $i + 1 }}</td>
                            <td>
                                <div
                                    class="w-16 h-12 rounded-lg border border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-image text-slate-300"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="text-nowrap">{{ $product->product_code }}</td>
                            <td class="text-nowrap font-medium">
                                {{ $product->name }}
                                @if($product->status === 'available' && $product->stock > 1)
                                    <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">
                                        Stok: {{ $product->stock }}
                                    </span>
                                @elseif($product->status === 'bonus')
                                    <span class="text-xs text-slate-500"> (Stok: {{ $product->stock }})</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $product->category?->name }}</td>
                            <td class="text-nowrap">{{ $product->brand?->name }}</td>
                            <td class="text-nowrap">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td class="text-nowrap">
                                <span
                                    class="px-2 py-1 text-xs rounded-full
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $product->status === 'sold' ? 'bg-red-100 text-red-700' : ($product->status === 'bonus' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $product->status === 'sold' ? 'Terjual' : ($product->status === 'bonus' ? 'Bonus' : 'Tersedia') }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $product->condition === 'used' ? 'bg-orange-100 text-orange-700' : 'bg-sky-100 text-sky-700' }}">
                                    {{ $product->condition === 'used' ? 'Bekas' : 'Baru' }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $product->is_active ? 'Tampil' : 'Disembunyikan' }}
                                </span>
                            </td>

                            <td class="text-center text-nowrap space-x-2">
                                <button onclick="printBarcode({{ $product->id }})"
                                    class="px-3 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600"
                                    title="Cetak Barcode">
                                    <i class="fa-solid fa-barcode"></i>
                                </button>
                                <button onclick='openEditModal(@json($product->load("images")->makeHidden(Auth::user()->isSuperAdmin() ? [] : ["purchase_price"])))'
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
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">

        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl border border-slate-200 overflow-y-auto max-h-[90vh]">

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
            <form id="productForm" method="POST" class="px-4 sm:px-6 py-5 space-y-5" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="methodField">
                <input type="hidden" name="deleted_images" id="deletedImages">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- KODE -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Kode Produk</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="product_code" id="productCode"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="PRD-001" required>
                        </div>
                    </div>

                    <!-- FOTO PRODUK -->
                    <div class="col-span-1 sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Foto Produk</label>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div id="imagePreviewContainer"
                                class="w-36 sm:w-28 aspect-[4/3] rounded-xl border-2 border-dashed border-slate-300 overflow-hidden flex items-center justify-center bg-slate-50">
                                <img id="imagePreview" src="" class="hidden w-full h-full object-cover">
                                <i id="imageIcon" class="fa-solid fa-image text-slate-300 text-2xl sm:text-xl"></i>
                            </div>
                            <div class="flex-1 w-full">
                                <input type="file" name="image" id="productImage" accept="image/*"
                                    onchange="previewProductImage(this)"
                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-[11px] text-slate-400 mt-1">JPG/PNG maksimal 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- FOTO TAMBAHAN -->
                    <div class="col-span-1 sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">
                            Foto Tambahan (Opsional)
                        </label>

                        <!-- DROPZONE -->
                        <div id="galleryDropzone"
                            class="relative border-2 border-dashed border-slate-300 rounded-2xl p-6
                                                                                                                                                                                                                                                                   text-center cursor-pointer
                                                                                                                                                                                                                                                                   hover:border-emerald-400 hover:bg-emerald-50/40 transition">

                            <input type="file" id="galleryInput" name="images[]" multiple accept="image/*"
                                class="absolute inset-0 opacity-0 cursor-pointer">

                            <i class="fa-solid fa-images text-3xl text-slate-300 mb-2"></i>
                            <p class="text-sm font-medium text-slate-500">
                                Klik atau seret foto ke sini
                            </p>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Bisa upload banyak foto (JPG, PNG, maksimal 2MB per foto)
                            </p>
                        </div>

                        <!-- PREVIEW -->
                        <div id="galleryPreview" class="grid grid-cols-3 sm:grid-cols-5 gap-3 mt-4"></div>
                    </div>

                    <!-- NAMA PRODUK -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Nama Produk</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-laptop absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="name" id="productName"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="Macbook Pro M1" required>
                        </div>
                    </div>

                    <!-- KATEGORI -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Kategori</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <select name="category_id" id="categoryId"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- BRAND -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Brand</label>
                        <div class="relative">
                            <i class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <select name="brand_id" id="brandId"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- HARGA BELI (SUPER ADMIN ONLY) -->
                    @if (Auth::user()->isSuperAdmin())
                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1 block">Harga Beli</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                                <input type="text" id="purchasePriceDisplay" oninput="formatRupiahInput(this)"
                                    class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                    placeholder="0">
                                <input type="hidden" name="purchase_price" id="purchasePrice">
                            </div>
                        </div>
                    @endif

                    <!-- HARGA JUAL -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Harga Jual</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                            <input type="text" id="sellingPriceDisplay" oninput="formatRupiahInput(this)"
                                class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="0">
                            <input type="hidden" name="selling_price" id="sellingPrice">
                        </div>
                    </div>

                    <!-- HARGA CORET -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Harga Coret (opsional)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">Rp</span>
                            <input type="text" id="strikePriceDisplay" oninput="formatRupiahInput(this)"
                                class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="Harga sebelum diskon">
                            <input type="hidden" name="strike_price" id="strikePrice">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Harus lebih besar dari Harga Jual. Kosongkan jika tidak ada diskon.</p>
                    </div>

                    <!-- DESKRIPSI -->
                    <div class="col-span-1 sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Deskripsi Produk</label>
                        <textarea name="description" id="productDescription"
                            class="w-full rounded-xl border border-slate-300 p-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"
                            rows="4"></textarea>
                    </div>

                    <!-- STATUS -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Status Produk</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-circle-info absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <select name="status" id="status" onchange="toggleStockInput()"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">

                                <option value="available">Tersedia</option>
                                <option value="sold">Terjual</option>
                                <option value="bonus">Bonus</option>
                            </select>
                        </div>
                    </div>

                    <!-- KONDISI -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Kondisi Produk</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <select name="condition" id="productCondition"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                <option value="new">Baru</option>
                                <option value="used">Bekas</option>
                            </select>
                        </div>
                    </div>

                    <div id="stockContainer" class="col-span-1 sm:col-span-1 hidden">
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Stok Bonus</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-cubes absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="number" name="stock" id="productStock"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="col-span-1 sm:col-span-1">
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Berat (gram)</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-weight-hanging absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="number" name="weight" id="productWeight" min="1"
                                class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="1000" value="1000">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Dipakai untuk hitung ongkir pengiriman.</p>
                    </div>

                    <div class="col-span-1 sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-2 block">Tampilan Katalog</label>
                        <label
                            class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/50 transition">
                            <input type="checkbox" name="is_active" id="productIsActive" value="1" checked
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="block">
                                <span class="block text-sm font-semibold text-slate-700">Tampilkan produk di katalog</span>
                                <span class="block text-xs text-slate-500">Nonaktifkan jika produk ini tidak ingin muncul
                                    di katalog publik.</span>
                            </span>
                        </label>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4 border-t">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 hover:bg-slate-100 transition">
                        Batal
                    </button>

                    <button type="submit" id="saveProductBtn"
                        class="px-5 py-2 text-sm font-semibold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed mt-2 sm:mt-0">
                        <span class="btn-text">Simpan Produk</span>
                        <svg class="btn-loading hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div id="barcodeModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl border w-full max-w-sm">

            {{-- Header --}}
            <div class="bg-indigo-600 px-5 py-3 rounded-t-2xl flex justify-between items-center text-white">
                <h3 class="font-bold flex items-center gap-2">
                    <i class="fa-solid fa-barcode"></i> Cetak Label Barcode
                </h3>
                <button onclick="closeBarcodeModal()" class="hover:rotate-90 transition-transform">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Preview label POS 58mm --}}
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <label class="text-xs font-semibold text-slate-600">Jumlah Cetak:</label>
                    <input type="number" id="printQty" value="1" min="1" max="50"
                        class="w-20 border border-slate-300 rounded-lg px-3 py-1.5 text-sm" />
                </div>

                {{-- Preview area --}}
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 bg-slate-50 flex justify-center">
                    <div id="barcodePreview" class="text-center"></div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button onclick="closeBarcodeModal()"
                        class="flex-1 py-2 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-100">
                        Batal
                    </button>
                    <button onclick="doPrint()"
                        class="flex-1 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')
        <script>
            var productData = {
                @foreach ($products as $product)
                    {{ $product->id }}: @json($product),
                @endforeach
                                                                                                                                                                                                                                                                        };
        </script>
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
        <script>
            $('#productDescription').summernote({
                height: 200,
                placeholder: 'Tulis deskripsi produk...',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']],
                    ['view', ['codeview']],
                ],
                callbacks: {
                    onImageUpload: function (files) {
                        const formData = new FormData();
                        formData.append('image', files[0]);
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                        fetch('{{ route('products.upload-description-image') }}', {
                            method: 'POST',
                            body: formData,
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.url) {
                                    $('#productDescription').summernote('insertImage', data.url);
                                }
                            })
                            .catch(() => {
                                alert('Gagal mengunggah gambar.');
                            });
                    },
                },
            });

            let deletedImageIds = [];
            const MAX_PRODUCT_IMAGE_SIZE = 2 * 1024 * 1024;

            function isOverProductImageLimit(file) {
                return file.size > MAX_PRODUCT_IMAGE_SIZE;
            }

            function showProductImageSizeError(fileName = 'Gambar') {
                Swal.fire({
                    icon: 'error',
                    title: 'File terlalu besar',
                    text: `${fileName} melebihi batas maksimal 2MB.`,
                });
            }

            function renderExistingGallery(images) {
                galleryPreview.innerHTML = '';

                images.forEach(img => {
                    const div = document.createElement('div');
                    div.className = 'relative group';

                    div.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                    <img src="/storage/${img.image}"
                                                                                                                                                                                                                                                                                                                                                                                                         class="w-full h-24 object-cover rounded-xl border">
                                                                                                                                                                                                                                                                                                                                                                                                    <button type="button"
                                                                                                                                                                                                                                                                                                                                                                                                        class="absolute top-1 right-1 bg-red-600 text-white text-xs
                                                                                                                                                                                                                                                                                                                                                                                                               w-6 h-6 rounded-full flex items-center justify-center
                                                                                                                                                                                                                                                                                                                                                                                                               opacity-0 group-hover:opacity-100 transition"
                                                                                                                                                                                                                                                                                                                                                                                                        onclick="removeExistingImage(${img.id}, this)">
                                                                                                                                                                                                                                                                                                                                                                                                        ✕
                                                                                                                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                                                                                                                `;

                    galleryPreview.appendChild(div);
                });
            }

            function removeExistingImage(id, btn) {
                deletedImageIds.push(id);
                document.getElementById('deletedImages').value = deletedImageIds.join(',');
                btn.parentElement.remove();
            }

            let galleryFiles = [];

            const galleryInput = document.getElementById('galleryInput');
            const galleryPreview = document.getElementById('galleryPreview');

            galleryInput.addEventListener('change', function () {
                handleGalleryFiles(this.files);
            });

            function handleGalleryFiles(files) {
                [...files].forEach(file => {
                    if (!file.type.startsWith('image/')) return;
                    if (isOverProductImageLimit(file)) {
                        showProductImageSizeError(file.name);
                        return;
                    }

                    galleryFiles.push(file);

                    const reader = new FileReader();
                    reader.onload = e => {
                        const div = document.createElement('div');
                        div.className = 'relative group';

                        div.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <img src="${e.target.result}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    class="w-full h-24 object-cover rounded-xl border">

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <button type="button"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    class="absolute top-1 right-1 bg-red-500 text-white text-xs
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           w-6 h-6 rounded-full flex items-center justify-center
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           opacity-0 group-hover:opacity-100 transition"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    onclick="removeGalleryImage(${galleryFiles.length - 1}, this)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ✕
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `;

                        galleryPreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });

                syncGalleryInput();
            }

            function removeGalleryImage(index, btn) {
                galleryFiles.splice(index, 1);
                btn.parentElement.remove();
                syncGalleryInput();
            }

            function syncGalleryInput() {
                const dataTransfer = new DataTransfer();
                galleryFiles.forEach(file => dataTransfer.items.add(file));
                galleryInput.files = dataTransfer.files;
            }

            function resetGallery() {
                galleryFiles = [];
                galleryPreview.innerHTML = '';
                galleryInput.value = '';
            }

            function handleLoadingButton(formSelector, buttonSelector) {
                $(formSelector).on('submit', function () {
                    const btn = $(buttonSelector);

                    btn.prop('disabled', true);
                    btn.find('.btn-text').text('Memproses...');
                    btn.find('.btn-loading').removeClass('hidden');

                    btn.addClass('pointer-events-none');
                });
            }

            function toggleStockInput() {
                const status = document.getElementById('status').value;
                const stockContainer = document.getElementById('stockContainer');
                const stockInput = document.getElementById('productStock');
                const stockLabel = stockContainer.querySelector('label');

                if (status === 'bonus' || status === 'available') {
                    stockContainer.classList.remove('hidden');

                    if (status === 'bonus') {
                        stockLabel.textContent = 'Stok Bonus';
                        stockInput.setAttribute('required', 'required');
                    } else {
                        stockLabel.textContent = 'Stok Produk (kosongkan jika produk satuan)';
                        stockInput.removeAttribute('required');
                    }
                } else {
                    stockContainer.classList.add('hidden');
                    stockInput.removeAttribute('required');
                    stockInput.value = '';
                }
            }

            $(function () {
                handleLoadingButton('#productForm', '#saveProductBtn');
                handleLoadingButton('#importModal form', '#importBtn');
                window.showPreview = function (input) {
                    const fileNameEl = document.getElementById('fileName');

                    if (input.files && input.files.length > 0) {
                        const file = input.files[0];

                        fileNameEl.textContent = file.name;
                        fileNameEl.classList.remove('text-slate-400');
                        fileNameEl.classList.add('text-indigo-600', 'font-semibold');
                    } else {
                        fileNameEl.textContent = 'Seret file atau klik disini';
                        fileNameEl.classList.remove('text-indigo-600', 'font-semibold');
                        fileNameEl.classList.add('text-slate-400');
                    }
                };

                $('.select2-filter').select2({
                    placeholder: "Pilih...",
                    allowClear: true,
                });
                $('#datatable').DataTable()

                const modal = $('#productModal')
                const form = $('#productForm')

                $('#productForm').on('submit', function () {
                    $('#productDescription').val($('#productDescription').summernote('code'));
                });

                window.openCreateModal = function () {
                    deletedImageIds = [];
                    document.getElementById('deletedImages').value = '';
                    document.getElementById('productForm').reset();
                    document.getElementById('status').value = 'available';
                    document.getElementById('productCondition').value = 'new';
                    document.getElementById('productIsActive').checked = true;
                    $('#productDescription').summernote('code', '');
                    toggleStockInput();
                    modal.removeClass('hidden')
                    form.attr('action', '/products')
                    $('#methodField').val('')
                    form.trigger('reset')

                    resetGallery();

                    $('#imagePreview').attr('src', '').addClass('hidden');
                    $('#imageIcon').removeClass('hidden');
                    $('#productImage').val('');
                }


                window.openEditModal = function (data) {
                    deletedImageIds = [];
                    document.getElementById('deletedImages').value = '';
                    modal.removeClass('hidden')
                    form.attr('action', `/products/${data.id}`)
                    $('#methodField').val('PUT')

                    resetGallery();

                    $('#productCode').val(data.product_code)
                    $('#productName').val(data.name)
                    $('#categoryId').val(data.category_id)
                    $('#brandId').val(data.brand_id)
                    if (data.purchase_price !== undefined) {
                        $('#purchasePrice').val(data.purchase_price)
                        $('#purchasePriceDisplay').val(new Intl.NumberFormat('id-ID').format(data.purchase_price));
                    }
                    $('#sellingPrice').val(data.selling_price)
                    $('#strikePrice').val(data.strike_price ?? '')
                    $('#productWeight').val(data.weight || 1000)
                    $('#status').val(data.status)
                    $('#productCondition').val(data.condition || 'new')
                    $('#productIsActive').prop('checked', !!data.is_active)
                    if (data.image) {
                        $('#imagePreview').attr('src', `/storage/${data.image}`).removeClass('hidden');
                        $('#imageIcon').addClass('hidden');
                    } else {
                        $('#imagePreview').addClass('hidden');
                        $('#imageIcon').removeClass('hidden');
                    }

                    $('#sellingPriceDisplay').val(new Intl.NumberFormat('id-ID').format(data.selling_price));
                    $('#strikePriceDisplay').val(data.strike_price ? new Intl.NumberFormat('id-ID').format(data.strike_price) : '');

                    $('#productDescription').summernote('code', data.description ?? '');

                    if (data.images && data.images.length) {
                        renderExistingGallery(data.images);
                    } else {
                        galleryPreview.innerHTML = '';
                    }

                    document.getElementById('status').value = data.status;
                    document.getElementById('productStock').value = data.stock || '';
                    toggleStockInput();

                }

                window.closeModal = function () {
                    modal.addClass('hidden')
                }

                window.deleteProduct = function (id) {
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

                const importModal = $('#importModal');

                window.openImportModal = function () {
                    importModal.removeClass('hidden');
                }

                window.closeImportModal = function () {
                    importModal.addClass('hidden');

                    $('#importFile').val('');
                    $('#fileName')
                        .text('Seret file atau klik disini')
                        .removeClass('text-indigo-600 font-semibold')
                        .addClass('text-slate-400');
                }


                window.previewProductImage = function (input) {
                    const preview = document.getElementById('imagePreview');
                    const icon = document.getElementById('imageIcon');

                    if (input.files && input.files[0]) {
                        if (isOverProductImageLimit(input.files[0])) {
                            showProductImageSizeError(input.files[0].name);
                            input.value = '';
                            return;
                        }

                        const reader = new FileReader();

                        reader.onload = function (e) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            icon.classList.add('hidden');
                        };

                        reader.readAsDataURL(input.files[0]);
                    }
                };

                window.formatRupiahInput = function (el) {
                    let value = el.value.replace(/[^0-9]/g, '');
                    let hiddenInputId = el.id.replace('Display', '');
                    document.getElementById(hiddenInputId).value = value;

                    if (value) {
                        el.value = new Intl.NumberFormat('id-ID').format(value);
                    } else {
                        el.value = '';
                    }
                };

            })
        </script>

        {{-- CDN JsBarcode --}}
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

        <script>
            let currentBarcodeProduct = null;

            window.printBarcode = function (id) {
                const product = productData[id];
                currentBarcodeProduct = product;
                const preview = document.getElementById('barcodePreview');

                // Tampilkan modal dulu
                document.getElementById('barcodeModal').classList.remove('hidden');

                // Kosongkan preview
                preview.innerHTML = '';

                // Buat SVG dan append
                const svg = document.createElement('svg');
                svg.id = 'svgPreview';
                svg.style.width = '100%';
                svg.style.height = 'auto';
                svg.style.display = 'block';
                svg.style.margin = '0 auto';
                preview.appendChild(svg);

                const desc = product.description
                    ? product.description
                        .replace(/<\/p>/gi, ' | ')
                        .replace(/<[^>]+>/g, '')
                        .replace(/\s*\|\s*$/, '')
                        .trim()
                    : '-';

                preview.insertAdjacentHTML('beforeend',
                    '<p style="font-size:11px;font-weight:bold;margin-top:4px;">' + (product.product_code || '') + '</p>' +
                    '<p style="font-size:11px;margin-top:2px;">' + (product.name || '') + '</p>' +
                    '<p style="font-size:10px;color:#666;margin-top:2px;">' + desc + '</p>' +
                    '<p style="font-size:13px;font-weight:bold;color:#4f46e5;margin-top:4px;">Barokah Computer</p>' +
                    '</p>'
                );

                // Render barcode setelah modal & SVG sudah ada di DOM
                setTimeout(function () {
                    const svgEl = document.getElementById('svgPreview');
                    JsBarcode(svgEl, product.product_code, {
                        format: "CODE128",
                        width: 1,       // ← lebih kecil
                        height: 40,
                        displayValue: false,
                        margin: 2
                    });
                }, 100);
            };

            window.closeBarcodeModal = function () {
                document.getElementById('barcodeModal').classList.add('hidden');
                currentBarcodeProduct = null;
            };

            window.doPrint = function () {
                const qty = parseInt(document.getElementById('printQty').value) || 1;
                const p = currentBarcodeProduct;
                const desc = p.description
                    ? p.description
                        .replace(/<\/p>/gi, ' | ')   // ganti </p> jadi separator
                        .replace(/<[^>]+>/g, '')     // hapus semua tag HTML sisanya
                        .replace(/\s*\|\s*$/, '')    // buang separator di akhir
                        .trim()
                    : '-';
                const price = 'Barokah Computer';

                // Buat SVG barcode sebagai string
                const tmpSvg = document.createElement('svg');
                JsBarcode(tmpSvg, p.product_code, {
                    format: "CODE128", width: 1, height: 45,  // ← 1 sudah cukup untuk 58mm
                    displayValue: false, margin: 2
                });
                const svgStr = tmpSvg.outerHTML;

                // Buat satu label HTML
                const labelHtml =
                    '<div class="label">' +
                    svgStr +
                    '<div class="code">' + p.product_code + '</div>' +
                    '<div class="name">' + p.name + '</div>' +
                    '<div class="desc">' + desc + '</div>' +
                    '<div class="price">' + price + '</div>' +
                    '</div>';

                const repeatedLabels = labelHtml.repeat(qty);

                const win = window.open('', '_blank');
                const html =
                    '<!DOCTYPE html>' +
                    '<html><head>' +
                    '<meta charset="UTF-8">' +
                    '<title>Label Barcode</title>' +
                    '<style>' +
                    '* { margin:0; padding:0; box-sizing:border-box; }' +
                    '@page { size: 58mm auto; margin: 2mm; }' +
                    'body { font-family: Arial, sans-serif; width: 54mm; }' +
                    '.label { width:54mm; padding:1mm; text-align:center; page-break-after:always; overflow:hidden; }' +
                    '.label:last-child { border-bottom: none; }' +
                    'svg { width:50mm; max-width:50mm; height:auto; display:block; margin:4px auto 0; overflow:hidden; }' +
                    '.code  { font-size:8pt; font-weight:bold; margin-top:1mm; letter-spacing:1px; }' +
                    '.name  { font-size:8pt; font-weight:bold; margin-top:1mm; }' +
                    '.desc  { font-size:8pt; margin-top:0.5mm; font-weight:bold; white-space: pre-line; }' +
                    '.price { font-size:10pt; font-weight:bold; margin-top:1mm; }' +
                    '</style>' +
                    '</head><body>' +
                    repeatedLabels +
                    '<script>window.onload=function(){ window.print(); window.close(); }</' + 'script>' +
                    '</body></html>';

                win.document.write(html);
                win.document.close();
            };

            // === SCAN BARCODE (auto-submit saat scan) ===
            const barcodeInput = document.getElementById('barcodeScanner');
            if (barcodeInput) {
                let scanBuffer = '', scanTimer = null;

                barcodeInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        // Enter dari alat scan → langsung submit form
                        e.preventDefault();
                        barcodeInput.closest('form').submit();
                    }
                });

                barcodeInput.addEventListener('input', function () {
                    clearTimeout(scanTimer);
                    // Auto-submit 300ms setelah scan selesai (alat scan cepat)
                    scanTimer = setTimeout(function () {
                        if (barcodeInput.value.length > 3) {
                            barcodeInput.closest('form').submit();
                        }
                    }, 300);
                });
            }
            window.printAllBarcodes = async function () {
                const ids = Object.keys(productData).filter(id => productData[id].status === 'available');

                if (ids.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Produk',
                        text: 'Tidak ada produk dengan status Tersedia.',
                        confirmButtonColor: '#4f46e5',
                    });
                    return;
                }

                const konfirmasi = await Swal.fire({
                    icon: 'question',
                    title: 'Cetak Semua Barcode?',
                    html: `Akan mencetak <strong>${ids.length} label</strong> produk berstatus <span class="text-green-600 font-bold">Tersedia</span>.`,
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Cetak',
                    cancelButtonText: 'Batal',
                });

                if (!konfirmasi.isConfirmed) return;

                let allLabels = '';

                ids.forEach(function (id) {
                    const p = productData[id];

                    const tmpSvg = document.createElement('svg');
                    JsBarcode(tmpSvg, p.product_code, {
                        format: "CODE128", width: 1, height: 45,  // ← 1 sudah cukup untuk 58mm
                        displayValue: false, margin: 2
                    });
                    const svgStr = tmpSvg.outerHTML;

                    const desc = p.description
                        ? p.description
                            .replace(/<\/p>/gi, ' | ')
                            .replace(/<[^>]+>/g, '')
                            .replace(/\s*\|\s*$/, '')
                            .trim()
                        : '-';

                    const price = 'Rp ' + new Intl.NumberFormat('id-ID').format(p.selling_price);

                    allLabels +=
                        '<div class="label">' +
                        svgStr +
                        '<div class="code">' + p.product_code + '</div>' +
                        '<div class="name">' + p.name + '</div>' +
                        '<div class="desc">' + desc + '</div>' +
                        '<div class="price">' + price + '</div>' +
                        '</div>';
                });

                const win = window.open('', '_blank');
                const html =
                    '<!DOCTYPE html>' +
                    '<html><head>' +
                    '<meta charset="UTF-8">' +
                    '<title>Cetak Semua Barcode</title>' +
                    '<style>' +
                    '* { margin:0; padding:0; box-sizing:border-box; }' +
                    '@page { size: 58mm auto; margin: 0; }' +
                    'body { font-family: Arial, sans-serif; width: 58mm; padding: 2mm; }' +
                    '.label { width:54mm; padding:1mm; text-align:center; page-break-after:always; overflow:hidden; }' +
                    '.label:last-child { border-bottom: none; }' +
                    'svg { width:50mm; max-width:50mm; height:auto; display:block; margin:4px auto 0; overflow:hidden; }' +
                    '.code  { font-size:8pt; font-weight:bold; margin-top:1mm; letter-spacing:1px; }' +
                    '.name  { font-size:8pt; font-weight:bold; margin-top:1mm; }' +
                    '.desc  { font-size:8pt; margin-top:0.5mm; font-weight:bold; white-space:pre-line; }' +
                    '.price { font-size:10pt; font-weight:bold; margin-top:1mm; }' +
                    '</style>' +
                    '</head><body>' +
                    allLabels +
                    '<script>window.onload=function(){ window.print(); window.close(); }</' + 'script>' +
                    '</body></html>';

                win.document.write(html);
                win.document.close();
            };
        </script>
    @endpush
@endsection
