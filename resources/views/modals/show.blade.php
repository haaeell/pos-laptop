{{-- resources/views/modals/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 px-4  md:px-6">

        {{-- ── HEADER ── --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('modals.index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg px-3 py-1.5 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <div>
                <h4 class="text-xl font-bold text-[#1a3a5c] leading-tight">Detail Pinjaman</h4>
                <p class="text-xs text-gray-400 mt-0.5">{{ $modal->kode_pinjaman }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

            {{-- ── LEFT COLUMN ── --}}
            <div class="lg:col-span-2 flex flex-col gap-5">

                {{-- HERO CARD --}}
                <div class="relative overflow-hidden rounded-2xl p-6 text-white"
                    style="background: linear-gradient(135deg, #1a3a5c 0%, #2c5282 100%)">
                    <div class="absolute -top-14 -right-14 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>

                    <p class="text-xs uppercase tracking-widest opacity-70 mb-1">{{ ucfirst($modal->jenis_pinjaman) }}</p>
                    <h3 class="text-2xl font-bold">{{ $modal->nama_pemberi_pinjaman }}</h3>

                    <div class="mt-4 flex flex-wrap gap-6">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider opacity-60">Total Kewajiban</p>
                            <p class="text-2xl font-bold mt-0.5">
                                Rp {{ number_format($modal->total_kewajiban, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider opacity-60">Sisa Kewajiban</p>
                            <p class="text-2xl font-bold mt-0.5 text-red-300">
                                Rp {{ number_format($modal->sisa_kewajiban, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="mt-4">
                        <div class="flex justify-between text-xs opacity-80 mb-1.5">
                            <span>Cicilan {{ $modal->cicilan_ke }} / {{ $modal->tenor }}</span>
                            <span>{{ $modal->getProgressPersen() }}%</span>
                        </div>
                        <div class="h-4 rounded-full bg-white/20 overflow-hidden">
                            <div class="h-4 rounded-full flex items-center justify-end pr-2 text-[10px] font-bold text-white transition-all duration-700"
                                style="width:{{ $modal->getProgressPersen() }}%; background: linear-gradient(90deg, #27ae60, #2ecc71)">
                                {{ $modal->getProgressPersen() }}%
                            </div>
                        </div>
                    </div>

                   
                </div>

                {{-- INFO CARD --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z" />
                        </svg>
                        <span class="font-semibold text-gray-700 text-sm">Informasi Pinjaman</span>
                    </div>
                    <div class="px-5 py-3 divide-y divide-dashed divide-gray-200">
                        @php
                            $rows = [
                                ['Jumlah Pinjaman', 'Rp ' . number_format($modal->jumlah_pinjaman, 0, ',', '.')],
                                ['Nominal Pencairan', 'Rp ' . number_format($modal->nominal_pencairan, 0, ',', '.')],
                                ['Total Bunga', 'Rp ' . number_format($modal->total_bunga, 0, ',', '.')],
                                ['Tenor', $modal->tenor . ' ' . $modal->satuan_tenor],
                                ['Cicilan / Periode', 'Rp ' . number_format($modal->cicilan_per_periode, 0, ',', '.')],
                                ['Tgl Pinjaman', $modal->tanggal_pinjaman->format('d M Y')],
                                ['Tgl Pencairan', $modal->tanggal_pencairan->format('d M Y')],
                                ['Jatuh Tempo', $modal->tanggal_jatuh_tempo->format('d M Y')],
                                ['Total Terbayar', 'Rp ' . number_format($modal->total_terbayar, 0, ',', '.')],
                                ['Status', ucfirst($modal->status)],
                            ];
                        @endphp
                        @foreach($rows as [$lbl, $val])
                            <div class="flex items-center justify-between py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">{{ $lbl }}</span>
                                <span class="text-sm font-semibold text-gray-800">{{ $val }}</span>
                            </div>
                        @endforeach
                        @if($modal->keterangan)
                            <div class="pt-2">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Keterangan</p>
                                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $modal->keterangan }}</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ── RIGHT COLUMN – JADWAL CICILAN ── --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h10" />
                            </svg>
                            <span class="font-semibold text-gray-700 text-sm">Jadwal Cicilan</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $modal->cicilans->count() }} cicilan</span>
                    </div>

                    <div class="overflow-auto" style="max-height:560px">
                        <table class="w-full text-sm border-collapse">
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-[#1a3a5c] text-white text-[11px] uppercase tracking-wider">
                                    <th class="px-3 py-2.5 text-center font-semibold">#</th>
                                    <th class="px-3 py-2.5 text-left font-semibold">Jatuh Tempo</th>
                                    <th class="px-3 py-2.5 text-right font-semibold">Cicilan</th>
                                    <th class="px-3 py-2.5 text-right font-semibold">Denda</th>
                                    <th class="px-3 py-2.5 text-left font-semibold">Tgl Bayar</th>
                                    <th class="px-3 py-2.5 text-left font-semibold">Status</th>
                                    <th class="px-3 py-2.5 text-center font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($modal->cicilans as $c)
                                    @php
                                        $rowBg = match ($c->status) {
                                            'sudah_bayar' => 'bg-green-50',
                                            'terlambat' => 'bg-red-50',
                                            default => ($c->cicilan_ke == $modal->cicilan_ke + 1 ? 'bg-yellow-50 font-semibold' : 'bg-white'),
                                        };
                                        $badge = match ($c->status) {
                                            'sudah_bayar' => ['bg-green-100 text-green-800', 'Lunas'],
                                            'terlambat' => ['bg-yellow-100 text-yellow-800', 'Terlambat'],
                                            'sebagian' => ['bg-blue-100 text-blue-800', 'Sebagian'],
                                            default => ['bg-red-100 text-red-800', 'Belum'],
                                        };
                                    @endphp
                                    <tr class="{{ $rowBg }} hover:brightness-95 transition-all">
                                        <td class="px-3 py-2 text-center font-bold text-gray-700">{{ $c->cicilan_ke }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">
                                            {{ $c->tanggal_jatuh_tempo->format('d M Y') }}</td>
                                        <td class="px-3 py-2 text-right font-semibold text-gray-800">
                                            {{ number_format($c->nominal_cicilan, 0, ',', '.') }}</td>
                                        <td
                                            class="px-3 py-2 text-right {{ $c->denda > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                            {{ $c->denda > 0 ? number_format($c->denda, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">
                                            {{ $c->tanggal_bayar ? $c->tanggal_bayar->format('d M Y') : '-' }}</td>
                                        <td class="px-3 py-2">
                                            <span
                                                class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badge[0] }}">
                                                {{ $badge[1] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            @if($c->status !== 'sudah_bayar')
                                                <button
                                                    onclick="bayarCicilan({{ $modal->id }}, {{ $c->id }}, {{ $c->cicilan_ke }}, {{ $c->nominal_cicilan }})"
                                                    class="inline-flex items-center gap-1 text-xs font-semibold bg-green-500 hover:bg-green-600 text-white px-2.5 py-1 rounded-lg transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Bayar
                                                </button>
                                            @else
                                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 font-bold border-t-2 border-gray-200 text-gray-700 text-sm">
                                    <td colspan="2" class="px-3 py-2.5 text-right">Total</td>
                                    <td class="px-3 py-2.5 text-right">
                                        {{ number_format($modal->cicilans->sum('nominal_cicilan'), 0, ',', '.') }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        {{ number_format($modal->cicilans->sum('denda'), 0, ',', '.') }}</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── MODAL BAYAR CICILAN ── --}}
    <div id="modalBayar" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 bg-green-600 text-white">
                <div class="flex items-center gap-2 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a5 5 0 00-10 0v2M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Bayar Cicilan
                </div>
                <button onclick="closeModal()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-5 py-5 space-y-4">
                <input type="hidden" id="bayarCicilanId">
                <div id="infoBayar"
                    class="bg-blue-50 border border-blue-200 text-blue-700 rounded-lg px-4 py-2.5 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Tanggal
                        Pembayaran</label>
                    <input type="date" id="tglBayar" value="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Denda
                        (Rp)</label>
                    <input type="number" id="inputDenda" min="0" value="0" placeholder="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Keterangan</label>
                    <input type="text" id="ketBayar" placeholder="Transfer BRI, …"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>
            <div class="flex justify-end gap-2 px-5 pb-5">
                <button onclick="closeModal()"
                    class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button onclick="konfirmasiBayar()"
                    class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Konfirmasi Bayar
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID');
        let activeCicilanId = null;
        const modalId = {{ $modal->id }};

        function openModal() { const m = document.getElementById('modalBayar'); m.classList.remove('hidden'); m.classList.add('flex'); }
        function closeModal() { const m = document.getElementById('modalBayar'); m.classList.add('hidden'); m.classList.remove('flex'); }

        document.getElementById('modalBayar').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        function bayarCicilan(mid, cid, ke, nominal) {
            activeCicilanId = cid;
            document.getElementById('bayarCicilanId').value = cid;
            document.getElementById('infoBayar').textContent = `Cicilan ke-${ke} · Nominal: ${fmt(nominal)}`;
            openModal();
        }

        async function konfirmasiBayar() {
            const payload = {
                cicilan_id: activeCicilanId,
                tanggal_bayar: document.getElementById('tglBayar').value,
                denda: document.getElementById('inputDenda').value || 0,
                keterangan: document.getElementById('ketBayar').value,
            };

            const res = await fetch(`/modals/${modalId}/bayar-cicilan`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload),
            });
            const json = await res.json();

            if (json.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: json.message,
                    confirmButtonColor: '#4f46e5',
                    timer: 2000,
                    timerProgressBar: true,
                });
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: json.message,
                    confirmButtonColor: '#dc2626',
                });
            }
        }
    </script>
@endpush