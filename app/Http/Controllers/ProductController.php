<?php

namespace App\Http\Controllers;

use App\Exports\TemplateProductExport;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\Contact;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images'])->latest();

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

        if ($request->filled('catalog_status')) {
            $query->where('is_active', $request->catalog_status === 'active');
        }

        if ($request->filled('barcode_search')) {
            $query->where('product_code', 'like', "%{$request->barcode_search}%");
        }


        return view('master.products.index', [
            'products' => $query->get(),
            'categories' => Category::all(),
            'brands' => Brand::all()
        ]);
    }

    public function store(Request $request)
    {
        $isSuperAdmin = $request->user()->isSuperAdmin();

        $data = $request->validate([
            'product_code' => 'required|unique:products,product_code',
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'purchase_price' => $isSuperAdmin ? 'required|numeric' : 'nullable|numeric',
            'selling_price' => 'required|numeric',
            'strike_price' => 'nullable|numeric|gt:selling_price',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable',
            'status' => 'required|in:available,sold,bonus',
            'stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
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
            'strike_price.numeric' => 'Harga coret harus berupa angka',
            'strike_price.gt' => 'Harga coret harus lebih besar dari harga jual',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format file harus .jpg, .jpeg, atau .png',
            'image.max' => 'Ukuran file maksimal 2MB',
            'images.*.image' => 'Foto tambahan harus berupa gambar',
            'images.*.mimes' => 'Format foto tambahan harus .jpg, .jpeg, atau .png',
            'images.*.max' => 'Ukuran setiap foto tambahan maksimal 2MB',
            'status.in' => 'Status tidak valid',
            'stock.integer' => 'Stok harus berupa angka',
            'stock.min' => 'Stok minimal 0'
        ]);

        $data['stock'] = in_array($data['status'], ['available', 'bonus'])
            ? (int) ($data['stock'] ?? 0)
            : 0;
        $data['weight'] = $data['weight'] ?? 1000;
        $data['is_active'] = $request->boolean('is_active', true);

        if (!$isSuperAdmin) {
            $data['purchase_price'] = 0;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            $imageData = collect($request->file('images'))
                ->map(fn($img) => [
                    'product_id' => $product->id,
                    'image' => $img->store('products', 'public'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->toArray();

            ProductImage::insert($imageData);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $isSuperAdmin = $request->user()->isSuperAdmin();

        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'purchase_price' => $isSuperAdmin ? 'required|numeric' : 'nullable|numeric',
            'selling_price' => 'required|numeric',
            'strike_price' => 'nullable|numeric|gt:selling_price',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable',
            'status' => 'required|in:available,sold,bonus',
            'stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama produk harus diisi',
            'category_id.required' => 'Kategori harus diisi',
            'category_id.exists' => 'Kategori tidak ditemukan',
            'brand_id.exists' => 'Brand tidak ditemukan',
            'purchase_price.required' => 'Harga beli harus diisi',
            'purchase_price.numeric' => 'Harga beli harus berupa angka',
            'selling_price.required' => 'Harga jual harus diisi',
            'selling_price.numeric' => 'Harga jual harus berupa angka',
            'strike_price.numeric' => 'Harga coret harus berupa angka',
            'strike_price.gt' => 'Harga coret harus lebih besar dari harga jual',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format file harus .jpg, .jpeg, atau .png',
            'image.max' => 'Ukuran file maksimal 2MB',
            'images.*.image' => 'Foto tambahan harus berupa gambar',
            'images.*.mimes' => 'Format foto tambahan harus .jpg, .jpeg, atau .png',
            'images.*.max' => 'Ukuran setiap foto tambahan maksimal 2MB',
            'status.in' => 'Status tidak valid',
            'stock.integer' => 'Stok harus berupa angka',
            'stock.min' => 'Stok minimal 0'
        ]);

        $data = $request->except(['image', 'images']);
        $data['stock'] = in_array($data['status'], ['available', 'bonus'])
            ? (int) ($data['stock'] ?? 0)
            : 0;
        $data['is_active'] = $request->boolean('is_active');

        if (!$isSuperAdmin) {
            unset($data['purchase_price']);
        }

        if ($request->filled('deleted_images')) {
            $ids = explode(',', $request->deleted_images);
            foreach ($ids as $imgId) {
                $img = ProductImage::find($imgId);
                if ($img) {
                    Storage::disk('public')->delete($img->image);
                    $img->delete();
                }
            }
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        $product->update($data);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $product = Product::withCount(['saleItem', 'saleBonus'])->findOrFail($id);

        if ($product->sale_item_count > 0 || $product->sale_bonus_count > 0) {
            return redirect()->back()->with(
                'error',
                'Produk tidak bisa dihapus karena masih digunakan di transaksi atau bonus'
            );
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus');
    }


    public function uploadDescriptionImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $path = $request->file('image')->store('description-images', 'public');

        return response()->json(['url' => asset('storage/' . $path)]);
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

    public function exportPdf(Request $request)
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

        if ($request->filled('catalog_status')) {
            $query->where('is_active', $request->catalog_status === 'active');
        }

        if ($request->filled('barcode_search')) {
            $query->where('product_code', 'like', "%{$request->barcode_search}%");
        }

        $products = $query->get();
        $contacts = Contact::where('is_active', true)->get();
        $settings = Setting::pluck('value', 'key');

        $pdf = Pdf::loadView('master.products.pdf', [
            'products' => $products,
            'contacts' => $contacts,
            'settings' => $settings,
            'filters' => [
                'category' => $request->filled('category')
                    ? Category::find($request->category)?->name
                    : null,
                'brand' => $request->filled('brand')
                    ? Brand::find($request->brand)?->name
                    : null,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'status' => $request->status,
                'catalog_status' => $request->catalog_status,
                'barcode_search' => $request->barcode_search,
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-produk.pdf');
    }
}
