<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Gunakan pencarian yang lebih aman dengan trim
        // Pastikan key ini sesuai dengan headings() di TemplateProductExport
        $categoryName = $row['kategori'] ?? null;
        $brandName    = $row['brand'] ?? null;

        $category = $categoryName ? Category::where('name', 'LIKE', trim($categoryName))->first() : null;
        $brand    = $brandName ? Brand::where('name', 'LIKE', trim($brandName))->first() : null;

        return new Product([
            'product_code'   => $row['kode_produk'],
            'name'           => $row['nama_produk'],
            'category_id'    => $category ? $category->id : null,
            'brand_id'       => $brand ? $brand->id : null,
            'purchase_price' => $row['harga_beli'] ?? 0,
            'selling_price'  => $row['harga_jual'] ?? 0,
            'status'         => 'available',
        ]);
    }
}
