<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('payment_status');
            }
            if (!Schema::hasColumn('sales', 'collateral_path')) {
                $table->string('collateral_path')->nullable()->after('paid_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $drop = array_filter(['paid_amount', 'collateral_path'], fn ($col) => Schema::hasColumn('sales', $col));
            if ($drop) {
                $table->dropColumn($drop);
            }
        });
    }
};
