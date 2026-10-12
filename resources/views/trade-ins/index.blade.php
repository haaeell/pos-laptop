@extends('layouts.app')

@section('title', 'Tukar Tambah')

@section('content')
    @php
        $activeReceivables = $sales->filter(fn ($sale) => $sale->remaining_amount > 0);
    @endphp

    <div class="mx-auto bg-white rounded-xl">
        <div class="flex flex-wrap justify-between items-start gap-3 mb-5">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Tukar Tambah</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Tukar Tambah</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('trade-ins.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <i class="fa-solid fa-right-left text-sm"></i>
                Tukar Tambah Baru
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if (session('error') || $errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">{{ session('error') ?? $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
            @foreach ([
                ['Total Transaksi', $sales->count(), 'bg-slate-100 text-slate-700', 'fa-receipt'],
                ['Lunas', $sales->where('payment_status', 'paid')->count(), 'bg-emerald-100 text-emerald-700', 'fa-circle-check'],
                ['Masih Piutang', $activeReceivables->count(), 'bg-amber-100 text-amber-700', 'fa-file-invoice-dollar'],
                ['Total Piutang', 'Rp ' . number_format($activeReceivables->sum('remaining_amount'), 0, ',', '.'), 'bg-rose-100 text-rose-700', 'fa-hand-holding-dollar'],
            ] as [$label, $value, $color, $icon])
                <div class="rounded-xl p-4 {{ $color }}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold uppercase">{{ $label }}</span>
                        <i class="fa-solid {{ $icon }} text-sm opacity-70"></i>
                    </div>
                    <div class="text-2xl font-bold mt-1 truncate">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                <p class="text-xs text-slate-500">Barang pelanggan langsung tercatat sebagai stok produk bekas.</p>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold">
                    <i class="fa-solid fa-box-open"></i> {{ $sales->count() }} transaksi
                </span>
            </div>
            <div class="overflow-x-auto">
                <table id="tradeInTable" class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Invoice</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Pelanggan</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Produk</th>
                            <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Penjualan</th>
                            <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Nilai Tukar</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Pembayaran</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sales as $sale)
                            @php
                                $status = match ($sale->payment_status) {
                                    'paid' => ['Lunas', 'bg-emerald-100 text-emerald-700'],
                                    'partial' => ['Sebagian', 'bg-amber-100 text-amber-700'],
                                    default => ['Hutang', 'bg-rose-100 text-rose-700'],
                                };
                            @endphp
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap" data-order="{{ $sale->id }}">
                                    <span class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded text-xs font-bold">#{{ $sale->invoice_number }}</span>
                                    <span class="block mt-1 text-[10px] text-slate-400">{{ $sale->created_at->format('d M Y, H:i') }} WIB</span>
                                </td>
                                <td class="px-4 py-4" data-search="{{ $sale->customer_name }} {{ $sale->customer_phone }}">
                                    <div class="font-semibold text-slate-700">{{ $sale->customer_name ?: 'Pelanggan Umum' }}</div>
                                    @if ($sale->customer_phone)<span class="text-[10px] text-slate-400">{{ $sale->customer_phone }}</span>@endif
                                </td>
                                <td class="px-4 py-4 min-w-60" data-search="{{ $sale->items->first()?->product?->name }} {{ $sale->tradeInProduct?->name }}">
                                    <div class="font-semibold text-slate-700">{{ $sale->items->first()?->product?->name ?? 'Produk dihapus' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1">Diterima: {{ $sale->tradeInProduct?->name ?? 'Produk dihapus' }}</div>
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap font-bold text-slate-800" data-order="{{ $sale->grand_total }}">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right whitespace-nowrap text-amber-700 font-semibold" data-order="{{ $sale->trade_in_value }}">
                                    Rp {{ number_format($sale->trade_in_value, 0, ',', '.') }}
                                    @if ($sale->paid_amount < 0)<span class="block mt-1 text-[10px] text-rose-600">Toko bayar: Rp {{ number_format(abs($sale->paid_amount), 0, ',', '.') }}</span>@endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $status[1] }}">{{ $status[0] }}</span>
                                        @if ($sale->remaining_amount > 0)<span class="text-[10px] text-rose-600">Sisa: Rp {{ number_format($sale->remaining_amount, 0, ',', '.') }}</span>@endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex justify-center gap-2">
                                        @if ($sale->remaining_amount > 0)
                                            <button type="button" data-payment-action="{{ route('sales.pay', $sale->id) }}" data-invoice="{{ $sale->invoice_number }}" data-remaining="{{ $sale->remaining_amount }}"
                                                class="inline-flex items-center justify-center w-9 h-9 text-amber-600 bg-amber-50 rounded-xl hover:bg-amber-600 hover:text-white transition" title="Catat Cicilan">
                                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('sales.invoice.pdf', $sale->id) }}" target="_blank" rel="noopener"
                                            class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-800 hover:text-white transition" title="Cetak Nota">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                        <button type="button" data-delete-action="{{ route('sales.destroy', $sale->id) }}" data-invoice="{{ $sale->invoice_number }}"
                                            class="inline-flex items-center justify-center w-9 h-9 text-rose-600 bg-rose-50 rounded-xl hover:bg-rose-600 hover:text-white transition" title="Hapus Tukar Tambah">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="fixed inset-0 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm z-50 p-4">
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div><h2 class="text-lg font-bold text-slate-800">Catat Cicilan</h2><p id="paymentInvoice" class="text-xs text-slate-500 mt-1"></p></div>
                <button type="button" data-close-payment class="w-8 h-8 rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="paymentForm" method="POST" class="space-y-4">
                @csrf
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah bayar</label><input id="paymentAmountText" type="text" inputmode="numeric" required placeholder="Rp 0" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm"><input id="paymentAmount" type="hidden" name="amount"></div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal pembayaran</label><input type="date" name="paid_at" value="{{ now()->toDateString() }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm"></div>
                <button class="w-full rounded-xl bg-amber-600 hover:bg-amber-700 px-4 py-2.5 text-white text-sm font-semibold">Simpan Cicilan</button>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm z-50 p-4">
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div><h2 class="text-lg font-bold text-slate-800">Hapus Tukar Tambah?</h2><p id="deleteInvoice" class="text-xs text-slate-500 mt-1"></p></div>
                <button type="button" data-close-delete class="w-8 h-8 rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">Produk yang dijual dikembalikan ke stok. Produk barang customer akan dihapus bila belum terjual atau dipakai.</div>
            <form id="deleteForm" method="POST" class="space-y-4">
                @csrf @method('DELETE')
                <input type="hidden" name="from_trade_in" value="1">
                <div class="flex gap-3"><button type="button" data-close-delete class="flex-1 rounded-xl border px-4 py-2.5 text-sm font-semibold text-slate-600">Batal</button><button class="flex-1 rounded-xl bg-rose-600 hover:bg-rose-700 px-4 py-2.5 text-sm font-semibold text-white">Hapus</button></div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#tradeInTable').DataTable({
                order: [[0, 'desc']], pageLength: 10, columnDefs: [{ targets: 6, orderable: false }],
                language: { search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data', info: 'Menampilkan _START_–_END_ dari _TOTAL_ transaksi', infoEmpty: 'Belum ada transaksi', emptyTable: 'Belum ada transaksi tukar tambah.', zeroRecords: 'Tidak ada transaksi yang cocok.', infoFiltered: '(dari _MAX_ transaksi)', paginate: { previous: 'Sebelumnya', next: 'Berikutnya' } }
            });

            const modal = $('#paymentModal');
            const $amountText = $('#paymentAmountText');
            const $amount = $('#paymentAmount');
            $(document).on('click', '[data-payment-action]', function () {
                const button = $(this);
                $('#paymentForm').attr('action', button.data('payment-action'));
                $('#paymentInvoice').text('#' + button.data('invoice') + ' · Maks. Rp ' + Number(button.data('remaining')).toLocaleString('id-ID'));
                $amountText.val(''); $amount.val('').attr('max', button.data('remaining'));
                modal.removeClass('hidden').addClass('flex');
                $amountText.trigger('focus');
            });
            $('[data-close-payment]').on('click', () => modal.addClass('hidden').removeClass('flex'));
            modal.on('click', event => { if (event.target === modal[0]) modal.addClass('hidden').removeClass('flex'); });
            $amountText.on('input', function () {
                const value = Number(String(this.value).replace(/[^\d]/g, '')) || 0;
                const max = Number($amount.attr('max')) || 0;
                const amount = max ? Math.min(value, max) : value;
                this.value = amount ? 'Rp ' + amount.toLocaleString('id-ID') : '';
                $amount.val(amount);
            });

            const deleteModal = $('#deleteModal');
            $(document).on('click', '[data-delete-action]', function () {
                const button = $(this);
                $('#deleteForm').attr('action', button.data('delete-action'));
                $('#deleteInvoice').text('#' + button.data('invoice'));
                deleteModal.removeClass('hidden').addClass('flex');
            });
            $('[data-close-delete]').on('click', () => deleteModal.addClass('hidden').removeClass('flex'));
            deleteModal.on('click', event => { if (event.target === deleteModal[0]) deleteModal.addClass('hidden').removeClass('flex'); });
        });
    </script>
@endpush
