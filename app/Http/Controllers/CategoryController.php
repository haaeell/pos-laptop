<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount([
            'products as available_products_count' => function ($query) {
                $query->where('status', 'available');
            }
        ])->latest()->get();

        return view('master.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'nullable|string|max:100',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'show_on_customer_site' => $request->boolean('show_on_customer_site', true),
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'nullable|string|max:100',
        ]);

        Category::findOrFail($id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'show_on_customer_site' => $request->boolean('show_on_customer_site', true),
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui');
    }

    public function toggleVisibility($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['show_on_customer_site' => !$category->show_on_customer_site]);

        return redirect()->back()->with('success', 'Visibilitas kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = Category::withCount('products')->findOrFail($id);

        if ($category->products_count > 0) {
            return redirect()->back()->with(
                'error',
                'Kategori tidak bisa dihapus karena masih digunakan oleh produk'
            );
        }

        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }
}
