@extends('layouts.catalog')

@section('title', $article->title . ' | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))
@section('meta_description', $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 155))
@if ($article->cover_image)
    @section('og_image', asset('storage/' . $article->cover_image))
@endif

@section('styles')
    <style>
        .article-detail-section {
            padding: 28px 0 60px;
        }

        .article-detail-wrap {
            max-width: 760px;
            margin: 0 auto;
        }

        .article-breadcrumb {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 16px;
        }

        .article-breadcrumb a {
            color: var(--muted);
            font-weight: 600;
        }

        .article-cat-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-soft);
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 12px;
        }

        .article-detail-title {
            font-size: 26px;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .article-detail-meta {
            font-size: 12.5px;
            color: var(--muted);
            margin-bottom: 22px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .article-cover {
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 26px;
            background: #F6F8FB;
        }

        .article-cover img {
            width: 100%;
            display: block;
        }

        .article-content {
            font-size: 14.5px;
            line-height: 1.85;
            color: #344054;
        }

        .article-content img {
            max-width: 100%;
            border-radius: 10px;
            margin: 14px 0;
        }

        .article-content h2, .article-content h3 {
            margin: 24px 0 12px;
        }

        .related-articles {
            margin-top: 46px;
            padding-top: 28px;
            border-top: 1px solid var(--line);
        }

        .related-articles h3 {
            font-size: 16px;
            margin-bottom: 16px;
        }

        .related-articles-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .related-article-card {
            display: flex;
            gap: 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
        }

        .related-article-thumb {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            background: #F6F8FB;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .related-article-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .related-article-card h4 {
            font-size: 12.5px;
            line-height: 1.4;
        }

        @media(max-width:640px) {
            .related-articles-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <section class="article-detail-section">
        <div class="container">
            <div class="article-detail-wrap">
                <div class="article-breadcrumb">
                    <a href="{{ url('/') }}">Beranda</a> /
                    <a href="{{ route('pages.articles') }}">Artikel</a> /
                    <span>{{ \Illuminate\Support\Str::limit($article->title, 40) }}</span>
                </div>

                @if ($article->category)
                    <span class="article-cat-badge">{{ $article->category->name }}</span>
                @endif

                <h1 class="article-detail-title">{{ $article->title }}</h1>

                <div class="article-detail-meta">
                    <span><i class="fa-regular fa-calendar"></i> {{ $article->published_at?->translatedFormat('d M Y') }}</span>
                    <span><i class="fa-regular fa-eye"></i> {{ $article->views }} dilihat</span>
                </div>

                @if ($article->cover_image)
                    <div class="article-cover">
                        <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}">
                    </div>
                @endif

                <div class="article-content">
                    {!! $article->content !!}
                </div>

                @if ($related->count())
                    <div class="related-articles">
                        <h3>Artikel Lainnya</h3>
                        <div class="related-articles-grid">
                            @foreach ($related as $r)
                                <a href="{{ route('pages.article-show', $r->slug) }}" class="related-article-card">
                                    <div class="related-article-thumb">
                                        @if ($r->cover_image)
                                            <img src="{{ asset('storage/' . $r->cover_image) }}" alt="{{ $r->title }}">
                                        @else
                                            <i class="fa-solid fa-image" style="color:#CBD5E1;"></i>
                                        @endif
                                    </div>
                                    <h4>{{ $r->title }}</h4>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
