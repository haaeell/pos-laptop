<?php

namespace App\Http\Controllers;

use App\Exports\TemplateProductExport;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->latest();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }


        return view('master.products.index', [
            'products' => $query->get(),
            'categories' => Category::all(),
            'brands' => Brand::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_code' => 'required|unique:products,product_code',
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable'
        ], [
            'product_code.required' => 'Kode produk harus diisi',
            'product_code.unique' => 'Kode produk sudah digunakan',
            'name.required' => 'Nama produk harus diisi',
            'category_id.required' => 'Kategori harus diisi',
            'category_id.exists' => 'Kategori tidak ditemukan',
            'brand_id.exists' => 'Brand tidak ditemukan',
            'purchase_price.required' => 'Harga beli harus diisi',
            'purchase_price.numeric' => 'Harga beli harus berupa angka',
            'selling_price.required' => 'Harga jual harus diisi',
            'selling_price.numeric' => 'Harga jual harus berupa angka',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format file harus .jpg, .jpeg, atau .png',
            'image.max' => 'Ukuran file maksimal 2MB'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->back()->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->back()->with('success', 'Produk dihapus');
    }

    public function template()
    {
        return Excel::download(new TemplateProductExport(), 'template_produk_laptop.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'Silahkan pilih file terlebih dahulu',
            'file.mimes' => 'Format file harus .xlsx atau .xls'
        ]);

        try {
            Excel::import(new ProductsImport(), $request->file('file'));
            return redirect()->back()->with('success', 'Data produk berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ada masalah saat import: ' . $e->getMessage());
        }
    }
}
