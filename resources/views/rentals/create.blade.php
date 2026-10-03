@extends('layouts.app')
@section('title', 'Sewa Laptop Baru')
@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-wrap justify-between items-start gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Sewa Laptop Baru</h1>
                <nav class="text-sm text-slate-500 mt-1"><a href="{{ route('rentals.index') }}"
                        class="hover:text-indigo-600">Sewa Laptop</a> <span class="mx-2">/</span> Tambah Sewa</nav>
            </div>
            <a href="{{ route('rentals.index') }}"
                class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50"><i
                    class="fa-solid fa-arrow-left mr-2"></i>Kembali</a>
        </div>
        @include('rentals.partials.alerts')
        @if ($tiers->isEmpty() || $products->isEmpty())
            <div class="p-4 mb-5 rounded-xl bg-amber-50 text-amber-700 text-sm">
                {{ $tiers->isEmpty() ? 'Atur tarif sewa terlebih dahulu pada halaman daftar sewa.' : 'Belum ada laptop dengan stok tersedia untuk disewakan.' }}
            </div>
        @endif
        <form id="rentalForm" method="POST" action="{{ route('rentals.store') }}" class="space-y-5">
            @csrf
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800"><i class="fa-solid fa-user text-indigo-500 mr-2"></i>Informasi
                        Penyewa</h2>
                    <p class="text-xs text-slate-500 mt-1">Data penyewa dan penanggung jawab akan tercantum pada invoice.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-4 p-5">
                    @foreach (['renter_name' => 'Nama Penyewa / Instansi', 'person_in_charge' => 'Nama Penanggung Jawab', 'phone' => 'No. HP / WhatsApp', 'purpose' => 'Keperluan Sewa'] as $name => $label)
                        <label class="text-sm font-medium">{{ $label }} <span class="text-rose-500">*</span>
                            <input required type="{{ $name === 'phone' ? 'tel' : 'text' }}"
                                maxlength="{{ $name === 'phone' ? 30 : 255 }}" name="{{ $name }}"
                                value="{{ old($name) }}"
                                class="mt-1.5 w-full p-2.5 border border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        </label>
                    @endforeach
                    <label class="text-sm font-medium md:col-span-2">Alamat <span class="text-rose-500">*</span>
                        <textarea required name="address" rows="2" class="mt-1.5 w-full p-2.5 border border-slate-200 rounded-xl">{{ old('address') }}</textarea>
                    </label>
                </div>
            </section>
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h2 class="font-semibold text-slate-800 mb-4"><i
                        class="fa-solid fa-calendar-days text-indigo-500 mr-2"></i>Jadwal Sewa</h2>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm font-medium">Tanggal Pemakaian <span class="text-rose-500">*</span></p>
                        <p class="text-xs text-slate-500 mt-1 mb-3">Pilih setiap tanggal pemakaian. Tanggal boleh tidak
                            berurutan; setiap tanggal dihitung satu hari.</p>
                        <div id="dateList" class="space-y-2"></div><button type="button" id="addDate"
                            class="mt-3 text-sm font-semibold text-indigo-600">+ Tambah Tanggal</button>
                    </div>
                    <label class="text-sm font-medium">Rencana Pengembalian <span class="text-rose-500">*</span><input
                            required type="date" id="plannedReturn" name="planned_return_date"
                            value="{{ old('planned_return_date') }}"
                            class="mt-1.5 w-full p-2.5 border border-slate-200 rounded-xl"><span
                            class="block text-xs font-normal text-slate-500 mt-2">Tidak boleh sebelum tanggal pemakaian
                            terakhir. Stok tersedia kembali setelah pengembalian dicatat.</span></label>
                </div>
            </section>
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                    <div>
                        <h2 class="font-semibold text-slate-800"><i
                                class="fa-solid fa-laptop text-indigo-500 mr-2"></i>Laptop & Kelengkapan</h2>
                        <p class="text-xs text-slate-500 mt-1">Cari nama atau kode laptop, lalu isi jumlah dan kondisi saat
                            diserahkan.</p>
                    </div><button type="button" id="addItem"
                        class="px-3 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-semibold hover:bg-indigo-100">+
                        Tambah Laptop</button>
                </div>
                <div id="items" class="space-y-3"></div>
            </section>
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" aria-live="polite">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Rincian Biaya Sewa</h2>
                    <p id="tierInfo" class="text-xs text-slate-500 mt-1">Pilih laptop untuk melihat tarif yang berlaku.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs text-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-left">Laptop</th>
                                <th class="px-4 py-3 text-right">Unit</th>
                                <th class="px-4 py-3 text-right">Hari</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Tarif / Unit / Hari</th>
                                <th class="px-5 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="costRows" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
                <div class="p-5 bg-indigo-50 border-t border-indigo-100 flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Total Biaya Sewa</p>
                        <p id="summary" class="text-xs text-indigo-600 mt-1">0 unit × 0 hari</p>
                    </div><strong id="total" class="text-2xl text-indigo-700">Rp 0</strong>
                </div>
                <p class="px-5 py-3 text-xs text-slate-500">Total ini adalah biaya sewa awal. Denda, bila ada, dicatat
                    terpisah saat pengembalian.</p>
            </section>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-xs text-slate-600">
                <h2 class="font-semibold text-slate-800 mb-2">Keterangan Penting & Aturan Sewa</h2>
                @include('rentals.partials.terms', ['listClass' => 'list-disc pl-4 space-y-1.5'])
            </div>
            <p id="formIssue" role="alert" class="text-sm text-rose-600"></p>
            <div class="flex justify-end"><button id="saveRental" type="submit" disabled
                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white rounded-xl font-semibold text-sm"><i
                        class="fa-solid fa-check mr-2"></i>Simpan Sewa</button></div>
        </form>
    </div>
    <template id="rentalItemTemplate">
        <div class="rental-item rounded-xl border border-slate-200 bg-slate-50/50 p-4">
            <div class="grid grid-cols-12 gap-3 items-end">
                <label class="col-span-8 md:col-span-9 text-xs font-medium min-w-0">Laptop<select required
                        class="product-select w-full mt-1"></select></label>
                <label class="col-span-3 md:col-span-2 text-xs font-medium">Jumlah Unit<input required min="1"
                        step="1" type="number" value="1"
                        class="qty mt-1 w-full p-2.5 border border-slate-200 rounded-xl"></label>
                <button type="button" class="remove-item col-span-1 h-10 text-rose-500 hover:text-rose-700"
                    aria-label="Hapus laptop" title="Hapus laptop"><i class="fa-solid fa-trash-can"></i></button>
            </div>
            <div class="grid md:grid-cols-2 gap-3 mt-3">
                <label class="text-xs font-medium">Kelengkapan<input required maxlength="255"
                        class="accessories mt-1 w-full p-2.5 border border-slate-200 rounded-xl"
                        value="Laptop dan Charger"></label>
                <label class="text-xs font-medium">Kondisi Saat Diserahkan<input required maxlength="255"
                        class="condition-out mt-1 w-full p-2.5 border border-slate-200 rounded-xl" value="Normal"></label>
            </div>
        </div>
    </template>
@endsection
@push('scripts')
    <script>
        window.rentalFormData =
            {{ Illuminate\Support\Js::from(['products' => $products, 'tiers' => $tiers, 'items' => array_values(old('items', [['qty' => 1]])), 'dates' => array_values(old('rental_dates', ['']))]) }};
    </script>
    <script src="{{ asset('js/rental-create.js') }}"></script>
@endpush
