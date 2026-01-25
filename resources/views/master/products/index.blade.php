@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl">

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
                        <th>#</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Brand</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $i => $product)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $product->product_code }}</td>
                                    <td class="font-medium">{{ $product->name }}</td>
                                    <td>{{ $product->category?->name }}</td>
                                    <td>{{ $product->brand?->name }}</td>
                                    <td>Rp {{ number_format($product->selling_price) }}</td>
                                    <td>
                                        <span class="px-2 py-1 text-xs rounded-full
                                                                                                {{ $product->status === 'available'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center space-x-2">
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
    <div id="productModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">

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
            <form id="productForm" method="POST" class="px-6 py-5 space-y-5">
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
                            <input type="text" name="product_code" id="productCode" class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
                                    focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="PRD-001" required>
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
                            <input type="text" name="name" id="productName" class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
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
                            <select name="category_id" id="categoryId" class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
                                    bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                @foreach($categories as $category)
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
                            <i class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <select name="brand_id" id="brandId" class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
                                    bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                @foreach($brands as $brand)
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
                            <input type="number" name="purchase_price" id="purchasePrice" class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm
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
                            <input type="number" name="selling_price" id="sellingPrice" class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm
                                    focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                placeholder="12000000">
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
                            <select name="status" id="status" class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm
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
            $(function () {

                $('#datatable').DataTable()

                const modal = $('#productModal')
                const form = $('#productForm')

                window.openCreateModal = function () {
                    modal.removeClass('hidden')
                    form.attr('action', '/products')
                    $('#methodField').val('')
                    form.trigger('reset')
                }

                window.openEditModal = function (data) {
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