<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Brands
        |--------------------------------------------------------------------------
        */
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Products
        | 1 code = 1 product (unique physical item)
        |--------------------------------------------------------------------------
        */
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('product_code')->unique();
            $table->string('name');

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('condition', ['new', 'used'])->default('used');

            $table->decimal('purchase_price', 15, 2);
            $table->decimal('selling_price', 15, 2);

            $table->enum('status', ['available', 'sold', 'bonus'])->default('available');
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Sales
        |--------------------------------------------------------------------------
        */
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained();
            $table->decimal('grand_total', 15, 2);

            // total profit
            $table->decimal('benefit', 15, 2);

            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'credit']);
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid');

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Sale Items (sold products)
        |--------------------------------------------------------------------------
        */
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->decimal('purchase_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('final_price', 15, 2);

            // profit per item
            $table->decimal('benefit', 15, 2);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Sale Bonuses
        |--------------------------------------------------------------------------
        */
        Schema::create('sale_bonuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // bonus = loss
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('benefit', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_bonuses');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
