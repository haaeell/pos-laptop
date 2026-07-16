<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Contact;
use Illuminate\Http\Request;

class PageController extends Controller
{
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
