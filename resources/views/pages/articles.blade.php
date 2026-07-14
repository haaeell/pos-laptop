@extends('layouts.catalog')

@section('title', 'Artikel | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .articles-empty {
            padding: 100px 0;
            text-align: center;
        }

        .articles-empty i {
            font-size: 48px;
            color: var(--primary-soft);
            background: var(--primary-soft);
            color: var(--primary);
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .articles-empty h1 {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .articles-empty p {
            color: var(--muted);
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <section class="container articles-empty">
        <i class="fa-solid fa-newspaper"></i>
        <h1>Segera Hadir</h1>
        <p>Fitur Artikel sedang kami siapkan. Nantikan tips seputar laptop, komputer, dan teknologi di sini.</p>
    </section>
@endsection
