<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('sales_share_mode', 20)->nullable();
            $table->decimal('sales_share_value', 15, 2)->nullable();
            $table->string('rental_share_mode', 20)->nullable();
            $table->decimal('rental_share_value', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['sales_share_mode', 'sales_share_value', 'rental_share_mode', 'rental_share_value']);
        });
    }
};
