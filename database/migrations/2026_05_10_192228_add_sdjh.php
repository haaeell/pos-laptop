<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'total_basic_salary')) {
                $table->decimal('total_basic_salary', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'total_sales_bonus')) {
                $table->decimal('total_sales_bonus', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'total_technician_fee')) {
                $table->decimal('total_technician_fee', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
