<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('total_sales_profit_share', 15, 2)->default(0);
            $table->decimal('total_rental_share', 15, 2)->default(0);
        });

        Schema::table('payroll_details', function (Blueprint $table) {
            $table->decimal('sales_profit_share', 15, 2)->default(0);
            $table->decimal('rental_share', 15, 2)->default(0);
            $table->unsignedInteger('sales_transaction_count')->default(0);
            $table->unsignedInteger('rental_transaction_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn(['sales_profit_share', 'rental_share', 'sales_transaction_count', 'rental_transaction_count']);
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['total_sales_profit_share', 'total_rental_share']);
        });
    }
};
