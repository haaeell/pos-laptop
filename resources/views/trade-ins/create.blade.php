@extends('layouts.app')

@section('title', 'Tukar Tambah Baru')

@push('styles')
    <style>
        .trade-in-form input:not([type="hidden"]), .trade-in-form select, .trade-in-form textarea {
            border-color: #e2e8f0;
            transition: border-color .2s, box-shadow .2s;
        }
        .trade-in-form input:not([type="hidden"]):focus, .trade-in-form select:focus, .trade-in-form textarea:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 2px rgb(199 210 254 / .7);
            outline: none;
        }
        .trade-in-form .select2-container .select2-selection--single { height: 42px; border-color: #e2e8f0; border-radius: .75rem; }
        .trade-in-form .select2-selection__rendered { line-height: 28px !important; }
        .trade-in-form .select2-selection__arrow { height: 40px !important; }
    </style>
@endpush

@section('content')
    <div class="trade-in-form max-w-6xl mx-auto space-y-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-right-left text-indigo-600"></i>Tukar Tambah Baru</h1>
                <nav class="text-sm text-slate-500 mt-1"><ol class="flex items-center gap-2"><li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li><li>/</li><li><a href="{{ route('trade-ins.index') }}" class="hover:text-indigo-600">Tukar Tambah</a></li><li>/</li><li class="text-slate-700 font-medium">Baru</li></ol></nav>
            </div>
            <a href="{{ route('trade-ins.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50"><i class="fa-solid fa-clock-rotate-left"></i>Riwayat Tukar Tambah</a>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <p class="font-semibold mb-1">Periksa kembali data transaksi:</p>
                <ul class="list-disc pl-5 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('trade-ins.store') }}" method="POST" enctype="multipart/form-data" class="grid lg:grid-cols-3 gap-5">
            @csrf
            <div class="lg:col-span-2 space-y-5">
                <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="font-semibold text-slate-800"><i class="fa-solid fa-cart-shopping text-indigo-500 mr-2"></i>1. Produk yang dijual</h2>
                        <p class="text-xs text-slate-500 mt-1">Pilih satu produk tersedia dan tentukan harga jual finalnya.</p>
                    </div>
                    <div class="p-5 grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="product_id" class="block text-xs font-semibold text-slate-600 mb-1">Produk *</label>
                            <select id="product_id" name="product_id" required class="w-full product-select text-sm">
                                <option value="">Pilih produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" @selected(old('product_id') == $product->id)>
                                        {{ $product->product_code }} — {{ $product->name }} (stok {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="sale_price" class="block text-xs font-semibold text-slate-600 mb-1">Harga jual final *</label>
                            <input id="sale_price_text" type="text" inputmode="numeric" value="{{ old('sale_price') ? 'Rp ' . number_format(old('sale_price'), 0, ',', '.') : '' }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Rp 6.000.000">
                            <input id="sale_price" name="sale_price" type="hidden" value="{{ old('sale_price') }}">
                        </div>
                        <div>
                            <label for="sales_person_id" class="block text-xs font-semibold text-slate-600 mb-1">Marketing</label>
                            <select id="sales_person_id" name="sales_person_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <option value="">Tanpa marketing</option>
                                @foreach ($salesPeople as $person)
                                    <option value="{{ $person->id }}" @selected(old('sales_person_id') == $person->id)>{{ $person->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="fee_sales" class="block text-xs font-semibold text-slate-600 mb-1">Fee marketing</label>
                            <input id="fee_sales_text" type="text" inputmode="numeric" value="{{ old('fee_sales') ? 'Rp ' . number_format(old('fee_sales'), 0, ',', '.') : '' }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Rp 0">
                            <input id="fee_sales" name="fee_sales" type="hidden" value="{{ old('fee_sales', 0) }}">
                        </div>
                    </div>
                </section>

                <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="font-semibold text-slate-800"><i class="fa-solid fa-box-open text-amber-500 mr-2"></i>2. Produk pelanggan yang diterima</h2>
                        <p class="text-xs text-slate-500 mt-1">Produk langsung masuk Master Produk sebagai barang bekas, stok 1, dan belum tampil di katalog online.</p>
                    </div>
                    <div class="p-5 grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="trade_in_name" class="block text-xs font-semibold text-slate-600 mb-1">Nama produk *</label>
                            <input id="trade_in_name" name="trade_in_name" value="{{ old('trade_in_name') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Laptop pelanggan">
                        </div>
                        <div>
                            <label for="trade_in_category_id" class="block text-xs font-semibold text-slate-600 mb-1">Kategori *</label>
                            <select id="trade_in_category_id" name="trade_in_category_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('trade_in_category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="trade_in_brand_id" class="block text-xs font-semibold text-slate-600 mb-1">Merek</label>
                            <select id="trade_in_brand_id" name="trade_in_brand_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                <option value="">Tanpa merek</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected(old('trade_in_brand_id') == $brand->id)>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="trade_in_value" class="block text-xs font-semibold text-slate-600 mb-1">Nilai beli / tukar tambah *</label>
                            <input id="trade_in_value_text" type="text" inputmode="numeric" value="{{ old('trade_in_value') ? 'Rp ' . number_format(old('trade_in_value'), 0, ',', '.') : '' }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Rp 2.000.000">
                            <input id="trade_in_value" name="trade_in_value" type="hidden" value="{{ old('trade_in_value') }}">
                        </div>
                        <div>
                            <label for="trade_in_selling_price" class="block text-xs font-semibold text-slate-600 mb-1">Harga jual ulang</label>
                            <input id="trade_in_selling_price_text" type="text" inputmode="numeric" value="{{ old('trade_in_selling_price') ? 'Rp ' . number_format(old('trade_in_selling_price'), 0, ',', '.') : '' }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Rp 0">
                            <input id="trade_in_selling_price" name="trade_in_selling_price" type="hidden" value="{{ old('trade_in_selling_price') }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="trade_in_notes" class="block text-xs font-semibold text-slate-600 mb-1">Catatan kondisi / kelengkapan</label>
                            <textarea id="trade_in_notes" name="trade_in_notes" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Contoh: charger lengkap, gores pada cover">{{ old('trade_in_notes') }}</textarea>
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-5 lg:sticky lg:top-20 self-start">
                <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/70"><h2 class="font-semibold text-slate-800"><i class="fa-solid fa-wallet text-emerald-500 mr-2"></i>3. Pelanggan & pembayaran</h2></div>
                    <div class="p-5 space-y-4">
                    <div>
                        <label for="customer_name" class="block text-xs font-semibold text-slate-600 mb-1">Nama pelanggan *</label>
                        <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-xs font-semibold text-slate-600 mb-1">Nomor HP</label>
                        <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    </div>

                    <div class="rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white p-4 space-y-2 text-sm shadow-md shadow-indigo-100">
                        <div class="flex justify-between"><span>Harga jual</span><strong id="summary-sale">Rp 0</strong></div>
                        <div class="flex justify-between"><span>Nilai barang pelanggan</span><strong id="summary-trade">− Rp 0</strong></div>
                        <div class="border-t border-white/30 pt-2 flex justify-between font-semibold"><span id="summary-due-label">Customer membayar</span><strong id="summary-due">Rp 0</strong></div>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-semibold text-slate-600 mb-1">Metode pembayaran uang *</label>
                        <div class="relative"><i class="fa-solid fa-wallet absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i><select id="payment_method" name="payment_method" class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm">
                            @foreach (['cash' => 'Cash', 'transfer' => 'Transfer', 'qris' => 'QRIS'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('payment_method', 'cash') === $key)>{{ $label }}</option>
                            @endforeach
                        </select></div>
                        <p id="payoutNotice" class="hidden mt-2 text-[11px] text-amber-700">Nilai barang lebih tinggi. Toko membayar selisih kepada customer.</p>
                    </div>
                    <div>
                        <label for="payment_status" class="block text-xs font-semibold text-slate-600 mb-1">Status pembayaran *</label>
                        <div class="relative"><i class="fa-solid fa-circle-check absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i><select id="payment_status" name="payment_status" class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 text-sm">
                            <option value="paid" @selected(old('payment_status', 'paid') === 'paid')>Lunas</option>
                            <option value="partial" @selected(old('payment_status') === 'partial')>Bayar sebagian</option>
                            <option value="unpaid" @selected(old('payment_status') === 'unpaid')>Hutang seluruh selisih</option>
                        </select></div>
                    </div>
                    <div id="credit-fields" class="space-y-4 hidden rounded-xl bg-amber-50 border border-amber-200 p-3">
                        <div id="dp-field">
                            <label for="paid_amount" class="block text-xs font-semibold text-slate-600 mb-1">Uang dibayar sekarang</label>
                            <input id="paid_amount_text" type="text" inputmode="numeric" value="{{ old('paid_amount') ? 'Rp ' . number_format(old('paid_amount'), 0, ',', '.') : '' }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Rp 0">
                            <input id="paid_amount" name="paid_amount" type="hidden" value="{{ old('paid_amount') }}">
                        </div>
                        <div>
                            <label for="due_date" class="block text-xs font-semibold text-slate-600 mb-1">Jatuh tempo *</label>
                            <input id="due_date" name="due_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('due_date') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label for="collateral" class="block text-xs font-semibold text-slate-600 mb-1">Jaminan / KTP *</label>
                            <input id="collateral" name="collateral" type="file" accept="image/jpeg,image/png,application/pdf" class="w-full text-xs">
                        </div>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-sm flex justify-between"><span class="text-slate-500">Sisa piutang</span><strong class="text-slate-800" id="summary-remaining">Rp 0</strong></div>
                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 px-4 py-3 text-white text-sm font-semibold shadow-sm shadow-indigo-200 transition flex justify-center items-center gap-2"><i class="fa-solid fa-floppy-disk"></i>Simpan Tukar Tambah</button>
                    </div>
                </section>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const $product = $('#product_id').select2({
            placeholder: 'Cari kode atau nama produk',
            width: '100%',
            language: { noResults: () => 'Produk tidak ditemukan' },
        });
        const product = $product[0];
        const price = document.getElementById('sale_price');
        const tradeValue = document.getElementById('trade_in_value');
        const status = document.getElementById('payment_status');
        const dp = document.getElementById('paid_amount');
        const rupiah = value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
        const cleanNumber = value => Number(String(value).replace(/[^\d]/g, '')) || 0;
        const moneyFields = ['sale_price', 'fee_sales', 'trade_in_value', 'trade_in_selling_price', 'paid_amount'];

        moneyFields.forEach(id => {
            const input = document.getElementById(`${id}_text`);
            const value = document.getElementById(id);
            input.addEventListener('input', () => {
                value.value = cleanNumber(input.value);
                input.value = value.value ? rupiah(value.value) : '';
                refreshTradeIn();
            });
        });

        function refreshTradeIn() {
            const sale = Number(price.value) || 0;
            const received = Number(tradeValue.value) || 0;
            const difference = sale - received;
            const due = Math.max(0, difference);
            const payout = Math.max(0, -difference);
            document.getElementById('summary-sale').textContent = rupiah(sale);
            document.getElementById('summary-trade').textContent = '− ' + rupiah(received);
            document.getElementById('summary-due-label').textContent = payout ? 'Toko membayar' : 'Customer membayar';
            document.getElementById('summary-due').textContent = rupiah(payout || due);
            document.getElementById('payoutNotice').classList.toggle('hidden', !payout);
            [...status.options].forEach(option => option.disabled = payout && option.value !== 'paid');
            if (payout) status.value = 'paid';
            document.getElementById('credit-fields').classList.toggle('hidden', status.value === 'paid');
            document.getElementById('dp-field').classList.toggle('hidden', status.value !== 'partial');
            const paid = status.value === 'paid' ? due : (status.value === 'partial' ? (Number(dp.value) || 0) : 0);
            document.getElementById('summary-remaining').textContent = rupiah(Math.max(0, due - paid));
        }

        $product.on('change', () => {
            const selectedPrice = product.selectedOptions[0]?.dataset.price;
            if (selectedPrice) {
                price.value = selectedPrice;
                document.getElementById('sale_price_text').value = rupiah(selectedPrice);
            }
            refreshTradeIn();
        });
        status.addEventListener('change', refreshTradeIn);
        refreshTradeIn();
    </script>
@endpush
