<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Total HPP sparepart — untuk kalkulasi profit service
            $table->unsignedBigInteger('spare_part_hpp')->default(0)->after('spare_part_cost');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('spare_part_hpp');
        });
    }
};
