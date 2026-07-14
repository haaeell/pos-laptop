<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Simpan list sparepart sebagai JSON array: [{ "name": "...", "price": 0 }]
            if (!Schema::hasColumn('services', 'spare_parts')) {
                $table->json('spare_parts')->nullable()->after('technician_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'spare_parts')) {
                $table->dropColumn('spare_parts');
            }
        });
    }
};
