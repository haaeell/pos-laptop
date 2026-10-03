@extends('layouts.app')
@section('title', 'Pengembalian Sewa')
@section('content')
    @php
        $money = fn($value) => 'Rp ' .
            number_format((float) $value, (float) $value == floor((float) $value) ? 0 : 2, ',', '.');
    @endphp
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-wrap justify-between items-start gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Pengembalian Sewa</h1>
                <p class="text-sm text-indigo-600 mt-1">{{ $rental->rental_number }} · {{ $rental->renter_name }}</p>
            </div><a href="{{ route('rentals.index') }}"
                class="px-4 py-2 border border-slate-200 rounded-lg text-sm">Kembali</a>
        </div>
        @include('rentals.partials.alerts')
        <div class="rounded-xl bg-indigo-50 border border-indigo-100 p-4 mb-5 text-sm text-indigo-900"><strong>Rencana
                kembali: {{ $rental->planned_return_date->format('d M Y') }}</strong>
            <p class="mt-1">{{ $rental->total_qty }} unit · {{ count($rental->rental_dates) }} hari pemakaian ·
                {{ $rental->person_in_charge }} ({{ $rental->phone }})</p>
        </div>
        <form method="POST" action="{{ route('rentals.return', $rental) }}" class="space-y-5">
            @csrf
            @foreach ($rental->items as $item)
                <section class="p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <h2 class="font-semibold text-slate-800">{{ $item->product_name }} <span class="text-indigo-600">×
                            {{ $item->qty }} unit</span></h2>
                    <p class="text-xs text-slate-400 mt-1">{{ $item->product_code }}</p>
                    <div class="mt-3 p-3 rounded-lg bg-slate-50 text-xs text-slate-600">Kelengkapan awal:
                        {{ $item->accessories }}<br>Kondisi awal: {{ $item->condition_out }}</div>
                    <label class="block mt-4 text-sm font-medium">Kondisi Saat Dikembalikan<input required maxlength="255"
                            name="items[{{ $item->id }}][condition_in]"
                            value="{{ old('items.' . $item->id . '.condition_in', 'Normal') }}"
                            class="mt-1.5 w-full p-2.5 border border-slate-200 rounded-xl"></label>
                    <label class="block mt-3 text-sm font-medium">Kendala / Kerusakan / Kelengkapan Kurang
                        <textarea name="items[{{ $item->id }}][return_issue]" rows="2"
                            class="mt-1.5 w-full p-2.5 border border-slate-200 rounded-xl" placeholder="Kosongkan jika tidak ada kendala">{{ old('items.' . $item->id . '.return_issue') }}</textarea>
                    </label>
                </section>
            @endforeach
            <section class="p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-4">Rincian Biaya Akhir</h2>
                <div class="flex justify-between text-sm gap-3"><span>Biaya sewa ({{ $rental->total_qty }} unit ×
                        {{ count($rental->rental_dates) }} hari × {{ $money($rental->daily_rate) }})</span><strong
                        class="whitespace-nowrap">{{ $money($rental->rental_total) }}</strong></div>
                <label class="block mt-4 text-sm font-medium">Denda (Rp)<input type="text" inputmode="decimal"
                        data-rupiah placeholder="Rp 0" autocomplete="off" id="fineAmount" name="fine_amount"
                        value="{{ old('fine_amount', 0) }}"
                        class="mt-1.5 w-full p-2.5 border border-slate-200 rounded-xl"></label>
                <p class="text-xs text-slate-500 mt-2">Isi sesuai hasil pemeriksaan dan konfirmasi dengan penyewa. Denda
                    tidak dihitung otomatis.</p>
                <label class="block mt-4 text-sm font-medium">Catatan Pengembalian / Keterangan Denda
                    <textarea name="return_notes" rows="3" class="mt-1.5 w-full p-2.5 border border-slate-200 rounded-xl"
                        placeholder="Jelaskan alasan dan rincian denda jika ada. Catatan ini dicetak pada invoice.">{{ old('return_notes') }}</textarea>
                </label>
                <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between text-indigo-700" aria-live="polite">
                    <strong>Total Biaya Akhir</strong><strong
                        id="returnTotal">{{ $money($rental->rental_total + (float) old('fine_amount', 0)) }}</strong>
                </div>
            </section>
            <p class="text-xs text-slate-500">Setelah disimpan, status sewa menjadi dikembalikan dan stok laptop tersedia
                kembali.</p>
            <button class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold">Simpan
                Pengembalian</button>
        </form>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/rental-money.js') }}"></script>
    <script>
        document.getElementById('fineAmount').addEventListener('rupiah:change', function() {
            document.getElementById('returnTotal').textContent = 'Rp ' + ({{ (float) $rental->rental_total }} +
                Math.max(0, Number(RentalMoney.value(this)) || 0)).toLocaleString('id-ID', {
                maximumFractionDigits: 2
            });
        });
    </script>
@endpush
