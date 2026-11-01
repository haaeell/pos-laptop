@extends('layouts.app')

@section('title', 'Tutup / Buka Buku')

@section('content')
    @php
        $latestBook = $books->first();
        $tomorrow = now()->addDay()->toDateString();
        $minimumNextStart = $active?->starts_at?->copy()->addDay()->toDateString();
        $canClose = ! $minimumNextStart || $minimumNextStart <= $tomorrow;
        $hasOpeningBalance = $active && (float) $active->opening_balance > 0;
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="flex items-center gap-2 text-2xl font-semibold text-slate-800">
                    <i class="fa-solid fa-book-open text-indigo-600" aria-hidden="true"></i> Tutup / Buka Buku
                </h1>
                <p class="mt-1 text-sm text-slate-500">Kelola periode pembukuan dan akses laporan yang sudah diarsipkan.</p>
                <nav aria-label="Breadcrumb" class="mt-2 text-sm text-slate-500">
                    <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                    <span class="mx-1.5" aria-hidden="true">/</span>
                    <span class="font-medium text-slate-700">Tutup / Buka Buku</span>
                </nav>
            </div>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center gap-2 self-start rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <i class="fa-solid fa-chart-line" aria-hidden="true"></i> Lihat Laporan Keuangan
            </a>
        </div>

        @if (session('success'))
            <div role="status" class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <i class="fa-solid fa-circle-check mt-0.5" aria-hidden="true"></i><span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <p class="font-semibold"><i class="fa-solid fa-circle-exclamation mr-1" aria-hidden="true"></i> Periksa kembali isian Anda</p>
                <ul class="mt-1 list-disc space-y-1 pl-6">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 {{ $hasOpeningBalance ? 'md:grid-cols-3' : 'md:grid-cols-2' }}">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status pembukuan</p>
                        <p class="mt-2 text-lg font-bold text-slate-800">{{ $active ? 'Buku aktif' : ($books->isEmpty() ? 'Buku pertama' : 'Menunggu buku baru') }}</p></div>
                    <span class="rounded-xl p-2.5 {{ $active || $books->isEmpty() ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}"><i class="fa-solid fa-book-open" aria-hidden="true"></i></span>
                </div>
                <p class="mt-2 text-xs text-slate-500">@if ($active) Sejak {{ $active->starts_at->format('d/m/Y') }} @elseif ($latestBook) Buku terakhir berakhir {{ $latestBook->ends_at?->format('d/m/Y') }} @else Periode pertama belum ditutup @endif</p>
            </div>
            @if ($hasOpeningBalance)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Saldo awal buku aktif</p>
                            <p class="mt-2 text-lg font-bold text-slate-800">Rp {{ number_format($active->opening_balance, 0, ',', '.') }}</p></div>
                        <span class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Modal awal periode saat ini</p>
                </div>
            @endif
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Arsip buku</p>
                        <p class="mt-2 text-lg font-bold text-slate-800">{{ $books->whereNotNull('snapshot')->count() }} periode</p></div>
                    <span class="rounded-xl bg-blue-50 p-2.5 text-blue-600"><i class="fa-solid fa-box-archive" aria-hidden="true"></i></span>
                </div>
                <p class="mt-2 text-xs text-slate-500">Laporan periode yang sudah ditutup</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <section aria-labelledby="book-action-heading" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2">
                @if ($active || $books->isEmpty())
                    <div class="flex items-start gap-3">
                        <span class="rounded-xl bg-rose-50 p-3 text-rose-600"><i class="fa-solid fa-lock" aria-hidden="true"></i></span>
                        <div><h2 id="book-action-heading" class="text-lg font-semibold text-slate-800">Tutup buku berjalan</h2>
                            <p class="mt-1 text-sm leading-relaxed text-slate-500">Simpan laporan periode ini sebagai arsip. Transaksi dalam periode yang ditutup akan dikunci.</p></div>
                    </div>
                    <form id="close-book-form" method="POST" action="{{ route('books.close') }}" class="mt-6 space-y-4">
                        @csrf
                        <div class="max-w-sm">
                            <label for="next_start" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal mulai buku berikutnya</label>
                            <input id="next_start" type="date" name="next_start" value="{{ old('next_start', $tomorrow) }}"
                                @if ($minimumNextStart) min="{{ $minimumNextStart }}" @endif max="{{ $tomorrow }}" required @disabled(! $canClose)
                                aria-describedby="next-start-help" aria-invalid="{{ $errors->has('next_start') ? 'true' : 'false' }}"
                                class="w-full rounded-xl border {{ $errors->has('next_start') ? 'border-rose-400' : 'border-slate-300' }} bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 disabled:cursor-not-allowed disabled:opacity-60">
                            <p id="next-start-help" class="mt-1.5 text-xs leading-relaxed text-slate-500">Buku berjalan berakhir satu hari sebelum tanggal ini. Tanggal mulai paling lambat besok.</p>
                        </div>
                        @unless ($canClose)<p class="text-sm text-amber-700">Buku aktif baru dimulai setelah besok dan belum dapat ditutup.</p>@endunless
                        <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
                            <button type="submit" @disabled(! $canClose) class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i> Tutup Buku
                            </button>
                            <span class="text-xs text-slate-500">Buku baru dibuka setelah penutupan selesai.</span>
                        </div>
                    </form>
                @else
                    <div class="flex items-start gap-3">
                        <span class="rounded-xl bg-indigo-50 p-3 text-indigo-600"><i class="fa-solid fa-book-open" aria-hidden="true"></i></span>
                        <div><h2 id="book-action-heading" class="text-lg font-semibold text-slate-800">Buka buku baru</h2>
                            <p class="mt-1 text-sm leading-relaxed text-slate-500">Buku baru mulai pada {{ $latestBook->ends_at->copy()->addDay()->format('d/m/Y') }}. Masukkan saldo awal untuk periode tersebut.</p></div>
                    </div>
                    <form method="POST" action="{{ route('books.open') }}" class="mt-6 space-y-4">
                        @csrf
                        <div class="max-w-sm">
                            <label for="opening_balance" class="mb-1.5 block text-sm font-semibold text-slate-700">Saldo awal / modal awal (Rp)</label>
                            <input id="opening_balance" type="text" inputmode="decimal" data-rupiah name="opening_balance" value="{{ old('opening_balance') }}" placeholder="Rp 100.000.000" autocomplete="off" required
                                aria-describedby="opening-balance-help" aria-invalid="{{ $errors->has('opening_balance') ? 'true' : 'false' }}"
                                class="w-full rounded-xl border {{ $errors->has('opening_balance') ? 'border-rose-400' : 'border-slate-300' }} bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
                            <p id="opening-balance-help" class="mt-1.5 text-xs leading-relaxed text-slate-500">Isi jumlah modal yang menjadi saldo awal buku baru. Gunakan 0 jika tidak ada.</p>
                        </div>
                        <div class="border-t border-slate-100 pt-4">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <i class="fa-solid fa-book-open" aria-hidden="true"></i> Buka Buku
                            </button>
                        </div>
                    </form>
                @endif
            </section>
            <aside class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-5 sm:p-6">
                <h2 class="flex items-center gap-2 text-sm font-semibold text-indigo-900"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Alur pembukuan</h2>
                <ol class="mt-4 space-y-4 text-sm text-slate-600">
                    <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-indigo-600">1</span><span>Pilih tanggal mulai buku berikutnya untuk menutup periode berjalan.</span></li>
                    <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-indigo-600">2</span><span>Laporan buku yang ditutup tersimpan di riwayat dan dapat diunduh sebagai PDF.</span></li>
                    <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-indigo-600">3</span><span>Masukkan saldo awal untuk membuka buku baru dan melanjutkan transaksi.</span></li>
                </ol>
            </aside>
        </div>

        <section aria-labelledby="book-history-heading" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div><h2 id="book-history-heading" class="text-lg font-semibold text-slate-800">Riwayat Buku</h2><p class="mt-0.5 text-xs text-slate-500">Periode terbaru ditampilkan lebih dulu.</p></div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $books->count() }} periode</span>
            </div>
            @forelse ($books as $book)
                @php($laterBooks = $books->filter(fn ($item) => $item->starts_at->gt($book->starts_at))->count())
                <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-4 last:border-b-0 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-slate-800">{{ $book->starts_at->format('d/m/Y') }} – {{ $book->ends_at?->format('d/m/Y') ?? 'Sekarang' }}</p>
                            @if ($book->snapshot)<span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Diarsipkan</span>
                            @else<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aktif</span>@endif
                        </div>
                        @if ((float) $book->opening_balance > 0)
                            <p class="mt-1 text-xs text-slate-500">Saldo awal Rp {{ number_format($book->opening_balance, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        @if ($book->snapshot)
                            <a href="{{ route('books.archive', $book) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 font-semibold text-indigo-600 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"><i class="fa-solid fa-eye text-xs" aria-hidden="true"></i> Lihat Arsip</a>
                            <a href="{{ route('books.archive.pdf', $book) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 font-semibold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><i class="fa-solid fa-file-pdf text-xs" aria-hidden="true"></i> Unduh PDF</a>
                            <form method="POST" action="{{ route('books.cancel', $book) }}" class="cancel-book-form" data-later-books="{{ $laterBooks }}" data-period="{{ $book->starts_at->format('d/m/Y') }} – {{ $book->ends_at->format('d/m/Y') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 font-semibold text-rose-600 transition hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-500"><i class="fa-solid fa-rotate-left text-xs" aria-hidden="true"></i> Batalkan Penutupan</button>
                            </form>
                        @else
                            <a href="{{ route('reports.index', ['book' => $book->id]) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 font-semibold text-indigo-600 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"><i class="fa-solid fa-chart-line text-xs" aria-hidden="true"></i> Lihat Laporan</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center sm:px-6">
                    <span class="inline-flex rounded-full bg-slate-100 p-4 text-slate-400"><i class="fa-solid fa-box-archive text-xl" aria-hidden="true"></i></span>
                    <p class="mt-3 font-semibold text-slate-700">Belum ada riwayat buku</p>
                    <p class="mt-1 text-sm text-slate-500">Buku pertama akan muncul di sini setelah ditutup.</p>
                </div>
            @endforelse
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/rental-money.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const closeForm = document.getElementById('close-book-form');
            closeForm?.addEventListener('submit', async (event) => {
                event.preventDefault();
                const date = document.getElementById('next_start').value;
                const message = `Buku berjalan akan berakhir sehari sebelum ${new Date(date + 'T00:00:00').toLocaleDateString('id-ID')}. Transaksi pada periode itu akan dikunci.`;
                const confirmed = window.Swal
                    ? (await Swal.fire({ icon: 'warning', title: 'Tutup buku berjalan?', text: message, showCancelButton: true, confirmButtonText: 'Ya, Tutup Buku', cancelButtonText: 'Kembali', confirmButtonColor: '#e11d48' })).isConfirmed
                    : window.confirm(message);
                if (confirmed) closeForm.submit();
            });
            document.querySelectorAll('.cancel-book-form').forEach((form) => {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const laterBooks = Number(form.dataset.laterBooks);
                    const impact = laterBooks > 0
                        ? `${laterBooks} buku setelah periode ini akan digabung dan saldo awal buku tersebut tidak lagi dihitung.`
                        : 'Periode ini akan kembali menjadi buku aktif.';
                    const message = `${form.dataset.period}. ${impact} Transaksi tetap tersimpan.`;
                    const confirmed = window.Swal
                        ? (await Swal.fire({ icon: 'warning', title: 'Batalkan penutupan buku?', text: message, showCancelButton: true, confirmButtonText: 'Ya, Batalkan Penutupan', cancelButtonText: 'Kembali', confirmButtonColor: '#e11d48' })).isConfirmed
                        : window.confirm(message);
                    if (confirmed) form.submit();
                });
            });
        });
    </script>
@endpush
