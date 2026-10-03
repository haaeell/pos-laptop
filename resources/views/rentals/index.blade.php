@extends('layouts.app')
@section('title', 'Sewa Laptop')
@section('content')
    @php
        $money = fn($value) => 'Rp ' .
            number_format((float) $value, (float) $value == floor((float) $value) ? 0 : 2, ',', '.');
    @endphp
    <style>
        .rental-tooltip { position: relative; }
        .rental-tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 50%; bottom: calc(100% + 8px);
            transform: translateX(-50%);
            width: max-content; max-width: 180px;
            padding: 5px 8px; border-radius: 6px;
            background: #1e293b; color: #fff;
            font-size: 11px; line-height: 1.2; white-space: nowrap;
            opacity: 0; pointer-events: none;
            transition: opacity .15s; z-index: 20;
        }
        .rental-tooltip:hover::after, .rental-tooltip:focus-visible::after { opacity: 1; }
    </style>
    <div class="mx-auto bg-white rounded-xl">
        <div class="flex flex-wrap justify-between items-start gap-3 mb-5">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Sewa Laptop</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Sewa Laptop</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('rentals.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">+ Sewa Baru</a>
        </div>
        @include('rentals.partials.alerts')
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
            @foreach ([['Total Transaksi', $rentals->count(), 'bg-slate-100 text-slate-700'], ['Sedang Disewa', $rentals->where('status', 'active')->count(), 'bg-amber-100 text-amber-700'], ['Sudah Dikembalikan', $rentals->where('status', 'returned')->count(), 'bg-emerald-100 text-emerald-700'], ['Unit Sedang Disewa', $rentals->where('status', 'active')->sum('total_qty'), 'bg-indigo-100 text-indigo-700']] as [$label, $value, $color])
                <div class="rounded-xl p-4 {{ $color }}">
                    <div class="text-xs font-bold uppercase">{{ $label }}</div>
                    <div class="text-2xl font-bold mt-1">{{ $value }}</div>
                </div>
            @endforeach
        </div>
        <details class="bg-white rounded-xl border border-slate-200 mb-5 group"
            @if ($errors->has('tiers*')) open @endif>
            <summary class="cursor-pointer font-semibold p-4 text-sm"><i
                    class="fa-solid fa-sliders text-indigo-500 mr-2"></i>Pengaturan Tarif Sewa <span
                    class="font-normal text-xs text-slate-400 ml-2">per unit / hari</span></summary>
            <form action="{{ route('rentals.rates') }}" method="POST" id="rates" class="p-4 pt-0">
                @csrf
                <p class="text-xs text-slate-500 mb-4">Tarif mengikuti batas minimum unit tertinggi yang terpenuhi, berlaku
                    untuk seluruh unit. Perubahan tarif hanya berlaku untuk sewa baru.</p>
                <div id="rateRows" class="space-y-3">
                    @foreach (old('tiers', $tiers->toArray()) as $i => $tier)
                        <div class="rate-row flex flex-wrap gap-3 items-end">
                            <label class="text-xs font-medium">Minimal Unit<input required type="number" min="1"
                                    step="1" name="tiers[{{ $i }}][min_qty]" value="{{ $tier['min_qty'] }}"
                                    class="block mt-1 p-2 border border-slate-200 rounded-lg w-28"></label>
                            <label class="text-xs font-medium">Tarif / Unit / Hari (Rp)<input required type="text"
                                    inputmode="decimal" data-rupiah placeholder="Rp 0" autocomplete="off"
                                    name="tiers[{{ $i }}][daily_rate]" value="{{ $tier['daily_rate'] }}"
                                    class="block mt-1 p-2 border border-slate-200 rounded-lg w-44"></label>
                            <button type="button" class="remove-rate h-9 px-3 text-rose-600 text-sm"
                                aria-label="Hapus tarif"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-3 mt-4"><button id="addRate" type="button"
                        class="px-3 py-2 bg-indigo-50 rounded-lg text-sm font-medium text-indigo-600">+ Tambah
                        Tarif</button><button
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">Simpan
                        Tarif</button></div>
            </form>
        </details>
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                <p class="text-xs text-slate-500">Stok laptop tersedia kembali setelah pengembalian dicatat.</p><label
                    class="text-sm">Status <select id="statusFilter"
                        class="ml-2 px-3 py-2 border border-slate-200 rounded-lg">
                        <option value="">Semua Status</option>
                        <option value="Disewa">Disewa</option>
                        <option value="Dikembalikan">Dikembalikan</option>
                    </select></label>
            </div>
            <div class="overflow-x-auto">
                <table id="rentalTable" class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            @foreach (['No. Sewa', 'Penyewa', 'Rencana Kembali', 'Total Biaya', 'Status', 'Aksi'] as $heading)
                                <th class="px-4 py-4 font-semibold whitespace-nowrap">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rentals as $rental)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap" data-order="{{ $rental->id }}">
                                    <span
                                        class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded text-xs font-bold">{{ $rental->rental_number }}</span>
                                </td>
                                <td class="px-4 py-4"
                                    data-search="{{ $rental->renter_name }} {{ $rental->phone }} {{ $rental->person_in_charge }} {{ $rental->items->pluck('product_name')->join(' ') }} {{ $rental->items->pluck('product_code')->join(' ') }}">
                                    <div class="font-semibold text-slate-700">{{ $rental->renter_name }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-slate-600"
                                    data-order="{{ $rental->planned_return_date->format('Y-m-d') }}">
                                    {{ $rental->planned_return_date->format('d M Y') }}@if ($rental->status === 'active' && $rental->planned_return_date->lt(today()))
                                        <span class="block text-xs text-rose-600 mt-1">Lewat rencana kembali</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap" data-order="{{ $rental->grand_total }}">
                                    <div class="font-bold text-slate-800">{{ $money($rental->grand_total) }}</div>
                                </td>
                                <td class="px-4 py-4"
                                    data-search="{{ $rental->status === 'returned' ? 'Dikembalikan' : 'Disewa' }}"><span
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $rental->status === 'returned' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $rental->status === 'returned' ? 'Dikembalikan' : 'Disewa' }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex gap-2">
                                        <button type="button" data-rental-detail="{{ $rental->id }}"
                                            class="rental-tooltip inline-flex items-center justify-center w-9 h-9 text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-600 hover:text-white transition"
                                            data-tooltip="Detail sewa"
                                            title="Detail Sewa" aria-label="Detail sewa {{ $rental->rental_number }}"
                                            aria-haspopup="dialog" aria-controls="rentalDetailModal">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <a href="{{ route('rentals.invoice', $rental) }}" target="_blank" rel="noopener"
                                            class="rental-tooltip inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-800 hover:text-white transition"
                                            data-tooltip="Cetak invoice"
                                            title="Cetak Invoice"
                                            aria-label="Cetak invoice {{ $rental->rental_number }}"><i
                                                class="fa-solid fa-print"></i></a>
                                        @if ($rental->status === 'active')
                                            <a href="{{ route('rentals.return.form', $rental) }}"
                                                class="rental-tooltip inline-flex items-center justify-center w-9 h-9 text-emerald-600 bg-emerald-50 rounded-xl hover:bg-emerald-600 hover:text-white transition"
                                                data-tooltip="Pengembalian laptop"
                                                title="Pengembalian Laptop"
                                                aria-label="Pengembalian {{ $rental->rental_number }}"><i
                                                     class="fa-solid fa-arrow-rotate-left"></i></a>
                                        @endif
                                        <form action="{{ route('rentals.destroy', $rental) }}" method="POST"
                                            class="delete-rental-form"
                                            data-rental-number="{{ $rental->rental_number }}"
                                            data-restores-stock="{{ $rental->status === 'active' ? '1' : '0' }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rental-tooltip inline-flex items-center justify-center w-9 h-9 text-rose-600 bg-rose-50 rounded-xl hover:bg-rose-600 hover:text-white transition"
                                                data-tooltip="Hapus sewa" title="Hapus Sewa"
                                                aria-label="Hapus sewa {{ $rental->rental_number }}"><i
                                                    class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('rentals.partials.detail-modal')
@endsection
@push('scripts')
    <script src="{{ asset('js/rental-money.js') }}"></script>
    <script>
        $(function() {
            const table = $('#rentalTable').DataTable({
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                columnDefs: [{
                    targets: [5],
                    orderable: false
                }],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ sewa',
                    infoEmpty: 'Belum ada sewa',
                    emptyTable: 'Belum ada transaksi sewa. Klik + Sewa Baru untuk menambahkan.',
                    zeroRecords: 'Tidak ada sewa yang cocok.',
                    infoFiltered: '(dari _MAX_ sewa)',
                    paginate: {
                        previous: 'Sebelumnya',
                        next: 'Berikutnya'
                    }
                }
            });
            $('#statusFilter').on('change', function() {
                table.column(4).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
            });
            const modal = document.getElementById('rentalDetailModal');
            const content = document.getElementById('rentalDetailContent');
            let previousOverflow = '';
            document.getElementById('rentalTable').addEventListener('click', event => {
                const button = event.target.closest('[data-rental-detail]');
                if (!button) return;
                const template = document.getElementById('rentalDetail-' + button.dataset.rentalDetail);
                if (!template) return;
                content.replaceChildren(template.content.cloneNode(true));
                previousOverflow = document.body.style.overflow;
                document.body.style.overflow = 'hidden';
                modal.showModal();
            });
            modal.addEventListener('click', event => {
                if (event.target !== modal) return;
                const bounds = modal.getBoundingClientRect();
                if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds
                    .top || event.clientY > bounds.bottom) modal.close();
            });
            modal.addEventListener('close', () => {
                document.body.style.overflow = previousOverflow;
                content.replaceChildren();
            });
            const rows = document.getElementById('rateRows');
            let nextRate = Math.max(-1, ...Array.from(rows.querySelectorAll('input[name$="[min_qty]"]'), input =>
                Number(input.name.match(/\[(\d+)\]/)[1]))) + 1;
            document.getElementById('addRate').addEventListener('click', () => {
                const i = nextRate++;
                rows.insertAdjacentHTML('beforeend',
                    `<div class="rate-row flex flex-wrap gap-3 items-end"><label class="text-xs font-medium">Minimal Unit<input required type="number" min="1" step="1" name="tiers[${i}][min_qty]" class="block mt-1 p-2 border border-slate-200 rounded-lg w-28"></label><label class="text-xs font-medium">Tarif / Unit / Hari (Rp)<input required type="text" inputmode="decimal" data-rupiah placeholder="Rp 0" autocomplete="off" name="tiers[${i}][daily_rate]" class="block mt-1 p-2 border border-slate-200 rounded-lg w-44"></label><button type="button" class="remove-rate h-9 px-3 text-rose-600 text-sm" aria-label="Hapus tarif"><i class="fa-solid fa-trash-can"></i></button></div>`
                );
                RentalMoney.init(rows);
            });
            rows.addEventListener('click', event => {
                const button = event.target.closest('.remove-rate');
                if (button) button.closest('.rate-row').remove();
            });

            document.querySelectorAll('.delete-rental-form').forEach(form => {
                form.addEventListener('submit', async event => {
                    event.preventDefault();
                    const restoresStock = form.dataset.restoresStock === '1';
                    const result = await Swal.fire({
                        icon: 'warning',
                        title: 'Hapus sewa?',
                        html: `Sewa <strong>${form.dataset.rentalNumber}</strong> akan dihapus.${restoresStock ? '<br>Stok laptop akan dikembalikan.' : ''}`,
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#e11d48',
                        reverseButtons: true,
                    });
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
