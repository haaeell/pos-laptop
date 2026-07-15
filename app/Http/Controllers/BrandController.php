<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount([
            'products as available_products_count' => function ($query) {
                $query->where('status', 'available');
            }
        ])->latest()->get();

        return view('master.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'logo' => 'nullable|image|max:2048',
        ]);

        Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'logo' => $request->hasFile('logo') ? $request->file('logo')->store('brands', 'public') : null,
            'show_as_partner' => $request->boolean('show_as_partner', true),
        ]);

        return redirect()->back()->with('success', 'Brand berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'logo' => 'nullable|image|max:2048',
        ]);

        $brand = Brand::findOrFail($id);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'show_as_partner' => $request->boolean('show_as_partner', true),
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()->back()->with('success', 'Brand berhasil diperbarui');
    }

    public function togglePartner($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['show_as_partner' => !$brand->show_as_partner]);

        return redirect()->back()->with('success', 'Status brand partner berhasil diperbarui');
    }

    public function destroy($id)
    {
        $brand = Brand::withCount('products')->findOrFail($id);

        if ($brand->products_count > 0) {
            return redirect()->back()->with(
                'error',
                'Brand tidak bisa dihapus karena masih digunakan oleh produk'
            );
        }

        $brand->delete();

        return redirect()->back()->with('success', 'Brand berhasil dihapus');
    }
}
