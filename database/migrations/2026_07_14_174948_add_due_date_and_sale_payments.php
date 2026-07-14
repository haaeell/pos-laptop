<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'due_date')) {
                $table->date('due_date')->nullable()->after('collateral_path');
            }
        });

        if (!Schema::hasTable('sale_payments')) {
            Schema::create('sale_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained();
                $table->decimal('amount', 15, 2);
                $table->date('paid_at');
                $table->string('note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }
};
