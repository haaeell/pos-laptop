{{-- resources/views/modals/index.blade.php --}}
@extends('layouts.app')

@section('content')

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar text-indigo-600"></i>
                Modal & Hutang
            </h2>
            <p class="text-sm text-slate-400 mt-0.5">Pantau pinjaman, bunga, dan progress cicilan</p>
        </div>
        <button onclick="openTambah()"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow transition">
            <i class="fa-solid fa-plus"></i> Tambah Pinjaman
        </button>
    </div>

    {{-- SUMMARY CARDS --}}
    @php
        $cards = [
            ['label' => 'Total Pinjaman', 'value' => 'Rp ' . number_format($summary['total_pinjaman'], 0, ',', '.'), 'icon' => 'fa-sack-dollar', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-400', 'ic' => 'text-indigo-500'],
            ['label' => 'Total Pencairan', 'value' => 'Rp ' . number_format($summary['total_pencairan'], 0, ',', '.'), 'icon' => 'fa-money-bill-transfer', 'bg' => 'bg-blue-50', 'border' => 'border-blue-400', 'ic' => 'text-blue-500'],
            ['label' => 'Sisa Kewajiban', 'value' => 'Rp ' . number_format($summary['total_kewajiban'], 0, ',', '.'), 'icon' => 'fa-triangle-exclamation', 'bg' => 'bg-red-50', 'border' => 'border-red-400', 'ic' => 'text-red-500'],
            ['label' => 'Aktif', 'value' => $summary['total_aktif'], 'icon' => 'fa-circle-play', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-400', 'ic' => 'text-emerald-500'],
        ];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @foreach($cards as $card)
            <div
                class="rounded-2xl {{ $card['bg'] }} border-l-4 {{ $card['border'] }} p-4 shadow-sm hover:-translate-y-1 transition-transform duration-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $card['label'] }}</span>
                    <i class="fa-solid {{ $card['icon'] }} {{ $card['ic'] }} text-lg"></i>
                </div>
                <div class="text-sm font-bold text-slate-700 leading-tight">{{ $card['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- FILTER & SEARCH --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <select name="status" onchange="this.form.submit()"
            class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Semua Status</option>
            @foreach(['aktif', 'lunas'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <div class="flex flex-1 min-w-52 rounded-xl overflow-hidden border border-slate-200 bg-white">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / pemberi pinjaman…"
                class="flex-1 px-4 py-2 text-sm text-slate-600 focus:outline-none">
            <button type="submit" class="px-4 text-slate-400 hover:text-indigo-600 transition">
                <i class="fa-solid fa-search"></i>
            </button>
        </div>
        @if(request()->hasAny(['status', 'search']))
            <a href="{{ route('modals.index') }}"
                class="flex items-center gap-1 text-sm px-4 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 transition">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
        @endif
    </form>

    {{-- TABLE --}}
    <div class="overflow-x-auto rounded-2xl shadow-sm border border-slate-100">
        <table class="w-full text-sm text-slate-700">
            <thead>
                <tr class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                    <th class="px-4 py-3 text-left font-semibold">Kode</th>
                    <th class="px-4 py-3 text-left font-semibold">Pemberi Pinjaman</th>
                    <th class="px-4 py-3 text-right font-semibold">Pinjaman</th>
                    <th class="px-4 py-3 text-right font-semibold">Pencairan</th>
                    <th class="px-4 py-3 text-right font-semibold">Total Bunga</th>
                    <th class="px-4 py-3 text-right font-semibold">Total Kewajiban</th>
                    <th class="px-4 py-3 text-right font-semibold">Sisa</th>
                    <th class="px-4 py-3 font-semibold" style="min-width:160px">Progress</th>
                    <th class="px-4 py-3 text-center font-semibold">Status</th>
                    <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($modals as $m)
                    <tr class="hover:bg-indigo-50/40 transition">
                        <td class="px-4 py-3">
                            <code
                                class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-lg">{{ $m->kode_pinjaman }}</code>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800">{{ $m->nama_pemberi_pinjaman }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ ucfirst($m->jenis_pinjaman) }} · {{ $m->tenor }}
                                {{ $m->satuan_tenor }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">{{ number_format($m->jumlah_pinjaman, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($m->nominal_pencairan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-amber-600 font-medium">
                            {{ number_format($m->total_bunga, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($m->total_kewajiban, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-red-500">
                            {{ number_format($m->sisa_kewajiban, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-between text-xs text-slate-400 mb-1">
                                <span>{{ $m->cicilan_ke }}/{{ $m->tenor }}</span>
                                <span>{{ $m->getProgressPersen() }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-gradient-to-r from-emerald-500 to-green-400 transition-all duration-500"
                                    style="width:{{ $m->getProgressPersen() }}%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $st = match ($m->status) {
                                    'aktif' => 'bg-emerald-100 text-emerald-700',
                                    'lunas' => 'bg-sky-100 text-sky-700',
                                    'restrukturisasi' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $st }}">
                                {{ ucfirst($m->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('modals.show', $m->id) }}"
                                    class="p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-100 transition" title="Detail">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <button onclick="editModal({{ $m->toJson() }})"
                                    class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-100 transition" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <button
                                    onclick="hapusModal({{ $m->id }}, '{{ $m->kode_pinjaman }}', {{ $m->sudah_dicairkan ? 'true' : 'false' }})"
                                    class="p-1.5 rounded-lg text-red-500 hover:bg-red-100 transition" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-16 text-slate-400">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                            <span class="text-sm">Belum ada data modal / hutang</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($modals->hasPages())
        <div class="mt-4">{{ $modals->links() }}</div>
    @endif

    {{-- ══════════════════════════════
    DRAWER FORM (Tambah / Edit)
    ══════════════════════════════ --}}
    <div id="drawerOverlay" onclick="closeDrawer()" class="fixed inset-0 bg-black/40 z-40 hidden"></div>

    <div id="drawer" class="fixed top-0 right-0 h-full w-full max-w-2xl bg-white z-50 shadow-2xl
                                                       translate-x-full transition-transform duration-300 flex flex-col">

        <div class="flex items-center justify-between px-6 py-4 bg-slate-800 text-white shrink-0">
            <div class="flex items-center gap-2 font-semibold">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <span id="drawerTitle">Tambah Pinjaman</span>
            </div>
            <button onclick="closeDrawer()" class="hover:text-slate-300 transition text-xl leading-none">&times;</button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">
            <form id="formModal">
                @csrf
                <input type="hidden" id="modalId">

                {{-- Info Pinjaman --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Informasi Pinjaman</p>
                    <div class="space-y-4">

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                Nama Pemberi Pinjaman <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_pemberi_pinjaman" required
                                placeholder="Bank BRI, Koperasi Sejahtera, …"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Pinjaman</label>
                                <select name="jenis_pinjaman"
                                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                    <option value="bank">Bank</option>
                                    <option value="koperasi">Koperasi</option>
                                    <option value="perorangan">Perorangan</option>
                                    <option value="lembaga_keuangan">Lembaga Keuangan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                                <select name="status"
                                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                    <option value="aktif">Aktif</option>
                                    <option value="lunas">Lunas</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">
                                    Jumlah Pinjaman (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="jumlah_pinjaman" id="inputJumlahPinjaman" required
                                    placeholder="100.000.000" oninput="hitungOtomatis()"
                                    class="rupiah-input w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">
                                    Nominal Pencairan (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nominal_pencairan" required placeholder="98.500.000"
                                    class="rupiah-input w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <p class="text-xs text-slate-400 mt-1">Aktual diterima (setelah potongan admin)</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tgl Pinjaman</label>
                                <input type="date" name="tanggal_pinjaman" required
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tgl Pencairan</label>
                                <input type="date" name="tanggal_pencairan" required
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Jatuh Tempo</label>
                                <input type="date" name="tanggal_jatuh_tempo" required
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="2" placeholder="Tujuan pinjaman, nomor kontrak, …"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Bunga & Cicilan --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Bunga & Cicilan</p>
                    <div class="space-y-4">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">
                                    Total Bunga (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="total_bunga" id="inputTotalBunga" required placeholder="10.000.000"
                                    oninput="hitungOtomatis()"
                                    class="rupiah-input w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <p class="text-xs text-slate-400 mt-1">Total bunga selama tenor</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">
                                    Tenor <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="number" name="tenor" id="inputTenor" required min="1" placeholder="12"
                                        oninput="hitungOtomatis()"
                                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                    <select name="satuan_tenor"
                                        class="border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                                        <option value="bulan" selected>Bln</option>
                                        <option value="hari">Hari</option>
                                        <option value="minggu">Mgg</option>
                                        <option value="tahun">Thn</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Preview kalkulasi otomatis --}}
                        <div id="kalkulasiResult"
                            class="hidden bg-indigo-50 border border-indigo-200 rounded-xl p-4 space-y-2">
                            <p class="text-xs font-bold uppercase tracking-wider text-indigo-400 mb-2">Preview Cicilan</p>
                            <div class="flex justify-between text-sm text-slate-600 pb-2 border-b border-indigo-200">
                                <span>Cicilan per Periode</span>
                                <span id="kCicilan" class="font-semibold text-slate-800">-</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600 pb-2 border-b border-indigo-200">
                                <span>Pokok per Cicilan</span>
                                <span id="kPokok" class="font-semibold text-slate-800">-</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600 pb-2 border-b border-indigo-200">
                                <span>Bunga per Cicilan</span>
                                <span id="kBungaPerCicilan" class="font-semibold text-slate-800">-</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-indigo-700">
                                <span>Total Kewajiban</span>
                                <span id="kKewajiban">-</span>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 shrink-0">
            <button onclick="closeDrawer()"
                class="px-5 py-2 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-100 transition">
                Batal
            </button>
            <button onclick="submitForm()"
                class="px-6 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow transition">
                <i class="fa-solid fa-save mr-1"></i> Simpan
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID');

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        }

        function parseRupiah(value) {
            return parseInt(String(value).replace(/\D/g, '')) || 0;
        }

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('rupiah-input')) {
                const raw = parseRupiah(e.target.value);
                e.target.value = raw ? formatRupiah(raw) : '';
            }
        });
        // ── Preview kalkulasi otomatis ────────────────────────────────
        function hitungOtomatis() {
            const pokok = parseRupiah(document.getElementById('inputJumlahPinjaman').value);
            const bunga = parseRupiah(document.getElementById('inputTotalBunga').value);
            const tenor = parseInt(document.getElementById('inputTenor').value) || 0;

            const result = document.getElementById('kalkulasiResult');

            if (!pokok || !tenor) {
                result.classList.add('hidden');
                return;
            }

            const totalKewajiban = pokok + bunga;
            const cicilanPerPeriode = totalKewajiban / tenor;
            const pokokPerCicilan = pokok / tenor;
            const bungaPerCicilan = bunga / tenor;

            document.getElementById('kCicilan').textContent = fmt(cicilanPerPeriode);
            document.getElementById('kPokok').textContent = fmt(pokokPerCicilan);
            document.getElementById('kBungaPerCicilan').textContent = fmt(bungaPerCicilan);
            document.getElementById('kKewajiban').textContent = fmt(totalKewajiban);

            result.classList.remove('hidden');
        }

        // ── Drawer ────────────────────────────────────────────────────
        function openTambah() {
            document.getElementById('drawerTitle').textContent = 'Tambah Pinjaman';
            document.getElementById('modalId').value = '';
            document.getElementById('formModal').reset();
            document.getElementById('kalkulasiResult').classList.add('hidden');
            document.getElementById('drawer').classList.remove('translate-x-full');
            document.getElementById('drawerOverlay').classList.remove('hidden');
        }

        function closeDrawer() {
            document.getElementById('drawer').classList.add('translate-x-full');
            document.getElementById('drawerOverlay').classList.add('hidden');
        }

        async function submitForm() {
            const id = document.getElementById('modalId').value;
            const form = document.getElementById('formModal');
            const data = Object.fromEntries(new FormData(form).entries());
            data.jumlah_pinjaman = parseRupiah(data.jumlah_pinjaman);
            data.nominal_pencairan = parseRupiah(data.nominal_pencairan);
            data.total_bunga = parseRupiah(data.total_bunga);

            const res = await fetch(id ? `/modals/${id}` : '/modals', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(data)
            });
            const json = await res.json();
            if (json.success) { closeDrawer(); location.reload(); }
            else Swal.fire({ icon: 'error', title: 'Gagal', text: json.message });
        }

        function editModal(m) {
            document.getElementById('drawerTitle').textContent = 'Edit Pinjaman';
            document.getElementById('modalId').value = m.id;
            document.getElementById('kalkulasiResult').classList.add('hidden');
            const form = document.getElementById('formModal');
            for (const [k, v] of Object.entries(m)) {
                const el = form.querySelector(`[name="${k}"]`);
                if (el) el.value = v ?? '';
            }
            document.getElementById('drawer').classList.remove('translate-x-full');
            document.getElementById('drawerOverlay').classList.remove('hidden');
            // Tampilkan preview kalkulasi saat edit
            hitungOtomatis();
        }

        async function hapusModal(id, kode, sudahCair) {
            if (sudahCair) {
                Swal.fire({ icon: 'warning', title: 'Tidak Bisa', text: 'Modal yang sudah dicairkan tidak dapat dihapus.' });
                return;
            }
            const { isConfirmed } = await Swal.fire({
                icon: 'question', title: `Hapus ${kode}?`,
                text: 'Data akan dihapus permanen.',
                showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444'
            });
            if (!isConfirmed) return;
            const res = await fetch(`/modals/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken } });
            const json = await res.json();
            if (json.success) location.reload();
            else Swal.fire({ icon: 'error', title: 'Gagal', text: json.message });
        }

        async function cairkan(id, kode, nominal) {
            const { isConfirmed } = await Swal.fire({
                icon: 'info', title: 'Cairkan Dana?',
                html: `<strong>${kode}</strong> sebesar <strong>${fmt(nominal)}</strong><br>akan otomatis menambah saldo kas.`,
                showCancelButton: true, confirmButtonText: 'Ya, cairkan', cancelButtonText: 'Batal',
                confirmButtonColor: '#4f46e5'
            });
            if (!isConfirmed) return;
            const res = await fetch(`/modals/${id}/cairkan`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
            const json = await res.json();
            Swal.fire({ icon: json.success ? 'success' : 'error', text: json.message });
            if (json.success) setTimeout(() => location.reload(), 1500);
        }
    </script>
@endpush