<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_peoples', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_peoples', 'fee')) {
                $table->decimal('fee', 15, 2)->default(0)->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_peoples', function (Blueprint $table) {
            if (Schema::hasColumn('sales_peoples', 'fee')) {
                $table->dropColumn('fee');
            }
        });
    }
};
