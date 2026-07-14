@extends('layouts.app')

@section('title', 'Ulasan Produk')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Ulasan Produk</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Ulasan Produk</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="p-2 text-left">#</th>
                        <th class="p-2 text-left">Produk</th>
                        <th class="p-2 text-left">Pelanggan</th>
                        <th class="p-2 text-left">Rating</th>
                        <th class="p-2 text-left">Komentar</th>
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-center" width="8%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $i => $review)
                        <tr class="border-b align-top">
                            <td class="p-2">{{ $i + 1 }}</td>
                            <td class="p-2">{{ $review->product?->name ?? '-' }}</td>
                            <td class="p-2">{{ $review->customer?->name ?? '-' }}</td>
                            <td class="p-2 text-amber-500 text-nowrap">
                                @for ($s = 1; $s <= 5; $s++)
                                    <i class="fa-{{ $s <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                            </td>
                            <td class="p-2 max-w-sm">{{ $review->comment ?: '-' }}</td>
                            <td class="p-2 text-nowrap">{{ $review->created_at->translatedFormat('d M Y') }}</td>
                            <td class="p-2 text-center">
                                <form method="POST" action="{{ route('reviews.destroy', $review->id) }}"
                                    onsubmit="return confirm('Hapus ulasan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">Belum ada ulasan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
