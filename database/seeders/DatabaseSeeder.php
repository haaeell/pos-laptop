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
        $categories = [
            'Laptop',
            'Mouse',
            'Accessories',
        ];

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
        $brands = [
            'Acer',
            'ASUS',
            'Lenovo',
            'Logitech',
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand,
                'slug' => Str::slug($brand),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Products
        | 1 code = 1 product (no stock)
        |--------------------------------------------------------------------------
        */
        Product::insert([
            [
                'product_code'   => 'LP-AC-001',
                'name'           => 'Acer Z01',
                'category_id'    => Category::where('name', 'Laptop')->first()->id,
                'brand_id'       => Brand::where('name', 'Acer')->first()->id,
                'condition'      => 'used',
                'purchase_price' => 5000000,
                'selling_price'  => 6000000,
                'status'         => 'available',
                'notes'          => 'Normal condition',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'product_code'   => 'MS-LOG-001',
                'name'           => 'Logitech Wireless Mouse',
                'category_id'    => Category::where('name', 'Mouse')->first()->id,
                'brand_id'       => Brand::where('name', 'Logitech')->first()->id,
                'condition'      => 'new',
                'purchase_price' => 150000,
                'selling_price'  => 250000,
                'status'         => 'available',
                'notes'          => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}
