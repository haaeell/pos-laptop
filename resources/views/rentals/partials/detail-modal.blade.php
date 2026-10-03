<dialog id="rentalDetailModal" aria-labelledby="rentalDetailTitle" class="rounded-2xl shadow-2xl bg-white text-slate-700">
    <div class="flex flex-col max-h-[calc(100dvh-2rem)]">
        <div
            class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4 bg-slate-50 border-b border-slate-100 shrink-0">
            <div>
                <h2 id="rentalDetailTitle" class="text-xl font-bold text-slate-800">Detail Sewa</h2>
                <p class="text-xs text-slate-500 mt-1">Informasi penyewa, perangkat, jadwal, dan rincian biaya.</p>
            </div>
            <form method="dialog">
                <button type="submit" autofocus aria-label="Tutup detail sewa"
                    class="w-9 h-9 rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </form>
        </div>
        <div id="rentalDetailContent" class="overflow-y-auto overscroll-contain p-5 sm:p-6 space-y-5 min-h-0"></div>
        <div class="px-5 sm:px-6 py-3 border-t border-slate-100 bg-slate-50 flex justify-end shrink-0">
            <form method="dialog"><button
                    class="px-4 py-2 text-sm font-medium border border-slate-200 rounded-lg hover:bg-slate-100">Tutup</button>
            </form>
        </div>
    </div>
</dialog>

@foreach ($rentals as $rental)
    <template id="rentalDetail-{{ $rental->id }}">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="font-bold text-indigo-600">{{ $rental->rental_number }}</p>
                <p class="text-xs text-slate-400 mt-1">Dibuat {{ $rental->created_at->format('d M Y, H:i') }}</p>
            </div>
            <span
                class="px-3 py-1 rounded-full text-xs font-semibold {{ $rental->status === 'returned' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $rental->status === 'returned' ? 'Dikembalikan' : 'Disewa' }}</span>
        </div>
        <section class="rounded-xl border border-slate-200 p-4">
            <h3 class="font-semibold text-slate-800 mb-3">Informasi Penyewa</h3>
            <dl class="grid sm:grid-cols-2 gap-4 text-sm break-words">
                <div>
                    <dt class="text-xs text-slate-400 mb-1">Penyewa / Instansi</dt>
                    <dd class="font-medium">{{ $rental->renter_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-1">Penanggung Jawab</dt>
                    <dd>{{ $rental->person_in_charge }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-1">No. HP / WhatsApp</dt>
                    <dd>{{ $rental->phone }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-1">Keperluan Sewa</dt>
                    <dd>{{ $rental->purpose }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-slate-400 mb-1">Alamat</dt>
                    <dd class="whitespace-pre-line">{{ $rental->address }}</dd>
                </div>
            </dl>
        </section>
        <section class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
            <div class="flex flex-wrap justify-between gap-2 mb-3">
                <h3 class="font-semibold text-slate-800">Jadwal Sewa</h3><span
                    class="text-xs font-medium text-indigo-600">{{ $rental->total_qty }} unit ·
                    {{ count($rental->rental_dates) }} hari pemakaian</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach (collect($rental->rental_dates)->sort() as $date)
                    <span
                        class="px-2.5 py-1 bg-white border border-indigo-100 rounded-lg text-xs text-indigo-700">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
                @endforeach
            </div>
            <dl class="grid sm:grid-cols-2 gap-3 mt-4 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 mb-1">Rencana Pengembalian</dt>
                    <dd class="font-medium">{{ $rental->planned_return_date->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-1">Waktu Pengembalian</dt>
                    <dd>{{ $rental->returned_at?->format('d M Y, H:i') ?? 'Belum dikembalikan' }}</dd>
                </div>
            </dl>
        </section>
        <section>
            <h3 class="font-semibold text-slate-800 mb-3">Laptop & Kelengkapan</h3>
            <div class="space-y-3">
                @foreach ($rental->items as $item)
                    <div class="p-4 rounded-xl border border-slate-200 text-sm break-words">
                        <div class="flex flex-wrap justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800">{{ $item->product_name }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ $item->product_code }}</p>
                            </div>
                            <span class="text-xs font-semibold text-indigo-600">{{ $item->qty }} unit</span>
                        </div>
                        <dl class="grid sm:grid-cols-2 gap-3 mt-3">
                            <div>
                                <dt class="text-xs text-slate-400 mb-1">Kelengkapan</dt>
                                <dd>{{ $item->accessories ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-400 mb-1">Kondisi Saat Diserahkan</dt>
                                <dd>{{ $item->condition_out ?: '-' }}</dd>
                            </div>
                            @if ($rental->status === 'returned')
                                <div>
                                    <dt class="text-xs text-slate-400 mb-1">Kondisi Saat Dikembalikan</dt>
                                    <dd>{{ $item->condition_in ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-400 mb-1">Kendala / Kerusakan</dt>
                                    <dd class="whitespace-pre-line">
                                        {{ $item->return_issue ?: 'Tidak ada kendala tercatat.' }}</dd>
                                </div>
                            @endif
                        </dl>
                        <div class="flex flex-wrap justify-between gap-2 mt-3 pt-3 border-t border-slate-100 text-xs">
                            <span class="text-slate-500">{{ $item->qty }} unit × {{ count($rental->rental_dates) }}
                                hari × {{ $money($rental->daily_rate) }}</span>
                            <strong
                                class="text-slate-800">{{ $money($item->qty * count($rental->rental_dates) * $rental->daily_rate) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        <section class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <h3 class="font-semibold text-slate-800 mb-3">Rincian Biaya</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex flex-wrap justify-between gap-2">
                    <dt class="text-slate-500">Tarif / Unit / Hari</dt>
                    <dd class="font-medium">{{ $money($rental->daily_rate) }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2">
                    <dt class="text-slate-500">Biaya Sewa ({{ $rental->total_qty }} unit ×
                        {{ count($rental->rental_dates) }} hari)</dt>
                    <dd class="font-medium">{{ $money($rental->rental_total) }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2">
                    <dt class="text-slate-500">Denda</dt>
                    <dd class="font-medium">{{ $money($rental->fine_amount) }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 pt-3 border-t border-slate-200 text-indigo-700">
                    <dt class="font-semibold">Total Biaya</dt>
                    <dd class="text-lg font-bold">{{ $money($rental->grand_total) }}</dd>
                </div>
            </dl>
            @if ($rental->status === 'active')
                <p class="text-xs text-slate-400 mt-2">Denda, bila ada, dicatat saat pengembalian.</p>
            @endif
        </section>
        @if ($rental->return_notes)
            <section class="p-4 rounded-xl border border-amber-100 bg-amber-50 text-sm break-words">
                <h3 class="font-semibold text-slate-800 mb-2">Catatan Pengembalian / Keterangan Denda</h3>
                <p class="whitespace-pre-line text-slate-600">{{ $rental->return_notes }}</p>
            </section>
        @endif
        <div class="flex flex-wrap justify-end gap-2">
            <a href="{{ route('rentals.invoice', $rental) }}" target="_blank" rel="noopener"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm rounded-lg"><i
                    class="fa-solid fa-print mr-2"></i>Cetak Invoice</a>
            @if ($rental->status === 'active')
                <a href="{{ route('rentals.return.form', $rental) }}"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg"><i
                        class="fa-solid fa-arrow-rotate-left mr-2"></i>Pengembalian</a>
            @endif
        </div>
    </template>
@endforeach

@push('styles')
    <style>
        #rentalDetailModal {
            width: calc(100% - 2rem);
            max-width: 56rem;
            max-height: calc(100dvh - 2rem);
            padding: 0;
            border: 0;
        }

        #rentalDetailModal::backdrop {
            background: rgb(15 23 42 / 60%);
            backdrop-filter: blur(4px);
        }
    </style>
@endpush
