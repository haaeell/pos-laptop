<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        $categories = ['Laptop', 'Mouse', 'Accessories', 'Keyboard', 'Monitor'];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Brands
        |--------------------------------------------------------------------------
        */
        $brands = ['Acer', 'ASUS', 'Lenovo', 'Logitech', 'Razer', 'Samsung', 'HP'];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand,
                'slug' => Str::slug($brand),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Products (20+ Items)
        |--------------------------------------------------------------------------
        */
        $productData = [
            // Laptops
            ['name' => 'Acer Swift 3', 'cat' => 'Laptop', 'brand' => 'Acer', 'price' => 8500000],
            ['name' => 'ASUS ROG Zephyrus', 'cat' => 'Laptop', 'brand' => 'ASUS', 'price' => 25000000],
            ['name' => 'Lenovo Legion 5', 'cat' => 'Laptop', 'brand' => 'Lenovo', 'price' => 18000000],
            ['name' => 'HP Pavilion Gaming', 'cat' => 'Laptop', 'brand' => 'HP', 'price' => 12000000],
            ['name' => 'Acer Predator Helios', 'cat' => 'Laptop', 'brand' => 'Acer', 'price' => 22000000],
            ['name' => 'ASUS Vivobook 14', 'cat' => 'Laptop', 'brand' => 'ASUS', 'price' => 7500000],

            // Mouse
            ['name' => 'Logitech MX Master 3S', 'cat' => 'Mouse', 'brand' => 'Logitech', 'price' => 1500000],
            ['name' => 'Razer DeathAdder V3', 'cat' => 'Mouse', 'brand' => 'Razer', 'price' => 1200000],
            ['name' => 'Logitech G502 Hero', 'cat' => 'Mouse', 'brand' => 'Logitech', 'price' => 700000],
            ['name' => 'ASUS ROG Gladius', 'cat' => 'Mouse', 'brand' => 'ASUS', 'price' => 900000],

            // Keyboards
            ['name' => 'Logitech G Pro X', 'cat' => 'Keyboard', 'brand' => 'Logitech', 'price' => 1800000],
            ['name' => 'Razer BlackWidow V4', 'cat' => 'Keyboard', 'brand' => 'Razer', 'price' => 2500000],
            ['name' => 'ASUS ROG Strix Flare', 'cat' => 'Keyboard', 'brand' => 'ASUS', 'price' => 2100000],
            ['name' => 'Keychron K2 (Generic)', 'cat' => 'Keyboard', 'brand' => 'Logitech', 'price' => 1300000],

            // Monitors
            ['name' => 'Samsung Odyssey G5', 'cat' => 'Monitor', 'brand' => 'Samsung', 'price' => 4500000],
            ['name' => 'Acer Nitro VG240Y', 'cat' => 'Monitor', 'brand' => 'Acer', 'price' => 2100000],
            ['name' => 'ASUS TUF Gaming VG249Q', 'cat' => 'Monitor', 'brand' => 'ASUS', 'price' => 3000000],
            ['name' => 'Samsung Flat 24 Inch', 'cat' => 'Monitor', 'brand' => 'Samsung', 'price' => 1500000],

            // Accessories
            ['name' => 'Logitech C922 Webcam', 'cat' => 'Accessories', 'brand' => 'Logitech', 'price' => 1400000],
            ['name' => 'Razer Seiren Mini', 'cat' => 'Accessories', 'brand' => 'Razer', 'price' => 750000],
            ['name' => 'Samsung External SSD 1TB', 'cat' => 'Accessories', 'brand' => 'Samsung', 'price' => 1900000],
            ['name' => 'HP Bluetooth Speaker', 'cat' => 'Accessories', 'brand' => 'HP', 'price' => 400000],
        ];

        foreach ($productData as $index => $item) {
            $category = Category::where('name', $item['cat'])->first();
            $brand = Brand::where('name', $item['brand'])->first();

            // Logic untuk kode produk: CAT-BRAND-001
            $code = strtoupper(substr($item['cat'], 0, 2)) . '-' . strtoupper(substr($item['brand'], 0, 3)) . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Product::create([
                'product_code'   => $code,
                'name'           => $item['name'],
                'category_id'    => $category->id,
                'brand_id'       => $brand->id,
                'condition'      => $index % 2 == 0 ? 'new' : 'used', // Selang-seling baru dan bekas
                'purchase_price' => $item['price'],
                'selling_price'  => $item['price'] + ($item['price'] * 0.2), // Profit 20%
                'status'         => 'available',
                'notes'          => 'Stock unit ' . ($index + 1),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
