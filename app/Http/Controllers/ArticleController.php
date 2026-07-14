<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('category')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('master.articles.index', [
            'articles' => $query->get(),
            'categories' => ArticleCategory::orderBy('name')->get(),
        ]);
    }

    protected function rules($id = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'article_category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,published',
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['user_id'] = Auth::id();
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        Article::create($data);

        return redirect()->back()->with('success', 'Artikel berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $data = $request->validate($this->rules($id));

        if ($data['title'] !== $article->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $article->id);
        }

        if ($data['status'] === 'published' && !$article->published_at) {
            $data['published_at'] = now();
        } elseif ($data['status'] === 'draft') {
            $data['published_at'] = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()->back()->with('success', 'Artikel berhasil diperbarui');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        $article->delete();

        return redirect()->back()->with('success', 'Artikel berhasil dihapus');
    }

    protected function uniqueSlug(string $title, $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Article::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    public function uploadContentImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $path = $request->file('image')->store('article-content-images', 'public');

        return response()->json(['url' => asset('storage/' . $path)]);
    }
}
