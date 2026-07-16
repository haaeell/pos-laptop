<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('product_code');
        });

        $usedSlugs = [];

        DB::table('products')
            ->select('id', 'name')
            ->orderBy('id')
            ->get()
            ->each(function ($product) use (&$usedSlugs) {
                $base = Str::slug($product->name);
                $base = $base !== '' ? $base : 'produk';
                $slug = $base;
                $suffix = 2;

                while (in_array($slug, $usedSlugs, true) || DB::table('products')->where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $base . '-' . $suffix;
                    $suffix++;
                }

                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['slug' => $slug]);

                $usedSlugs[] = $slug;
            });

        Schema::table('products', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
