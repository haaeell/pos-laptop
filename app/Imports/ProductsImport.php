<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $category = $row['kategori']
            ? Category::firstOrCreate(
                ['name' => $row['kategori']],
                ['slug' => Str::slug($row['kategori'])]
            )
            : null;

        $brand = $row['brand']
            ? Brand::firstOrCreate(
                ['name' => $row['brand']],
                ['slug' => Str::slug($row['brand'])]
            )
            : null;


        return new Product([
            'product_code' => $row['kode_produk'],
            'name' => $row['nama_produk'],
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'purchase_price' => $row['harga_beli'],
            'selling_price' => $row['harga_jual'],
            'description' => $row['deskripsi'],
        ]);
    }
}
