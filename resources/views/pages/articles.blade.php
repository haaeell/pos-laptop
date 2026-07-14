@extends('layouts.catalog')

@section('title', 'Artikel | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))
@section('meta_description', 'Tips seputar laptop, komputer, dan teknologi dari ' . ($navSettings['nama_toko'] ?? 'Barokah Computer') . ', toko komputer Subang.')

@section('styles')
    <style>
        .articles-section {
            padding: 32px 0 60px;
        }

        .articles-filter {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            margin-bottom: 24px;
            -webkit-overflow-scrolling: touch;
        }

        .articles-filter::-webkit-scrollbar {
            display: none;
        }

        .articles-filter a {
            flex-shrink: 0;
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--muted);
            white-space: nowrap;
        }

        .articles-filter a.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .article-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            transition: .2s;
        }

        .article-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .article-card-image {
            height: 170px;
            background: #F6F8FB;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .article-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .article-card-body {
            padding: 16px;
        }

        .article-card-cat {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-soft);
            padding: 3px 10px;
            border-radius: 999px;
            margin-bottom: 8px;
        }

        .article-card h3 {
            font-size: 15px;
            margin-bottom: 8px;
            min-height: 40px;
        }

        .article-card p {
            font-size: 12.5px;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .article-card-date {
            font-size: 11px;
            color: var(--muted);
        }

        .articles-empty {
            text-align: center;
            padding: 80px 0;
            color: var(--muted);
        }

        @media(max-width:960px) {
            .articles-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:640px) {
            .articles-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <section class="articles-section">
        <div class="container">
            <h1 style="font-size:24px;margin-bottom:6px;"><i class="fa-solid fa-newspaper"></i> Artikel</h1>
            <p style="color:var(--muted);font-size:13.5px;margin-bottom:22px;">
                Tips &amp; informasi seputar laptop, komputer, dan teknologi dari {{ $navSettings['nama_toko'] ?? 'Barokah Computer' }}.
            </p>

            @if ($categories->count())
                <div class="articles-filter">
                    <a href="{{ route('pages.articles') }}" class="{{ !$activeCategory ? 'active' : '' }}">Semua</a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('pages.articles', ['category' => $cat->slug]) }}" class="{{ $activeCategory === $cat->slug ? 'active' : '' }}">{{ $cat->name }}</a>
                    @endforeach
                </div>
            @endif

            @if ($articles->isEmpty())
                <div class="articles-empty">
                    <i class="fa-solid fa-newspaper" style="font-size:40px;color:#CBD5E1;margin-bottom:14px;display:block;"></i>
                    <p>Belum ada artikel{{ $activeCategory ? ' untuk kategori ini' : '' }}.</p>
                </div>
            @else
                <div class="articles-grid">
                    @foreach ($articles as $article)
                        <a href="{{ route('pages.article-show', $article->slug) }}" class="article-card">
                            <div class="article-card-image">
                                @if ($article->cover_image)
                                    <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}">
                                @else
                                    <i class="fa-solid fa-image" style="font-size:36px;color:#CBD5E1;"></i>
                                @endif
                            </div>
                            <div class="article-card-body">
                                @if ($article->category)
                                    <span class="article-card-cat">{{ $article->category->name }}</span>
                                @endif
                                <h3>{{ $article->title }}</h3>
                                <p>{{ \Illuminate\Support\Str::limit($article->excerpt ?: strip_tags($article->content), 90) }}</p>
                                <div class="article-card-date">
                                    <i class="fa-regular fa-calendar"></i> {{ $article->published_at?->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div style="margin-top:32px;">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
