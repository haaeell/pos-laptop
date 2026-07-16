<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;

class PageController extends Controller
{
    private const SITEMAP_MAX_URLS = 140;

    public function service()
    {
        return view('pages.service', [
            'contacts' => Contact::where('is_active', true)->get(),
        ]);
    }

    public function about()
    {
        return view('pages.about', [
            'contacts' => Contact::where('is_active', true)->get(),
        ]);
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function security()
    {
        return view('pages.security');
    }

    public function sitemap()
    {
        $staticPages = collect([
            [
                'loc' => url('/'),
                'lastmod' => now(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('catalog.listing'),
                'lastmod' => now(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('pages.service'),
                'lastmod' => now(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('pages.about'),
                'lastmod' => now(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => route('pages.privacy'),
                'lastmod' => now(),
                'changefreq' => 'monthly',
                'priority' => '0.4',
            ],
            [
                'loc' => route('pages.security'),
                'lastmod' => now(),
                'changefreq' => 'monthly',
                'priority' => '0.4',
            ],
            [
                'loc' => route('pages.articles'),
                'lastmod' => Article::published()->max('updated_at') ?? now(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
        ]);

        $remainingSlots = max(self::SITEMAP_MAX_URLS - $staticPages->count(), 0);

        $products = Product::query()
            ->where('status', 'available')
            ->where('is_active', true)
            ->latest('updated_at')
            ->take($remainingSlots)
            ->get(['slug', 'updated_at'])
            ->map(fn ($product) => [
                'loc' => route('catalog.show', $product->slug),
                'lastmod' => $product->updated_at ?? now(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $remainingSlots -= $products->count();

        $articles = Article::published()
            ->latest('updated_at')
            ->take(max($remainingSlots, 0))
            ->get(['slug', 'updated_at', 'published_at'])
            ->map(fn ($article) => [
                'loc' => route('pages.article-show', $article->slug),
                'lastmod' => $article->updated_at ?? $article->published_at ?? now(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        return response()
            ->view('pages.sitemap', [
                'urls' => $staticPages->concat($products)->concat($articles),
            ])
            ->header('Content-Type', 'application/xml');
    }

    public function articles(Request $request)
    {
        $query = Article::published()->with('category')->latest('published_at');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        return view('pages.articles', [
            'articles' => $query->paginate(9)->withQueryString(),
            'categories' => ArticleCategory::orderBy('name')->get(),
            'activeCategory' => $request->category,
        ]);
    }

    public function articleShow($slug)
    {
        $article = Article::published()->with('category')->where('slug', $slug)->firstOrFail();
        $article->increment('views');

        $related = Article::published()
            ->where('id', '!=', $article->id)
            ->when($article->article_category_id, fn ($q) => $q->where('article_category_id', $article->article_category_id))
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('pages.article-show', compact('article', 'related'));
    }
}
