<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('trade_in_product_id')->nullable()->constrained('products')->restrictOnDelete();
            $table->decimal('trade_in_value', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('trade_in_product_id');
            $table->dropColumn('trade_in_value');
        });
    }
};
