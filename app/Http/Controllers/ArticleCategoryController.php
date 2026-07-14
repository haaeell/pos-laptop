<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        $categories = ArticleCategory::withCount('articles')->latest()->get();

        return view('master.article-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);

        ArticleCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Kategori artikel berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);

        ArticleCategory::findOrFail($id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Kategori artikel berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = ArticleCategory::withCount('articles')->findOrFail($id);

        if ($category->articles_count > 0) {
            return redirect()->back()->with(
                'error',
                'Kategori tidak bisa dihapus karena masih digunakan oleh artikel'
            );
        }

        $category->delete();

        return redirect()->back()->with('success', 'Kategori artikel berhasil dihapus');
    }
}
