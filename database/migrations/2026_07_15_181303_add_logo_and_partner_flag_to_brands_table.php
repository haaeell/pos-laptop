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
        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'logo')) {
                $table->string('logo')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('brands', 'show_as_partner')) {
                $table->boolean('show_as_partner')->default(true)->after('logo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'show_as_partner')) {
                $table->dropColumn('show_as_partner');
            }
            if (Schema::hasColumn('brands', 'logo')) {
                $table->dropColumn('logo');
            }
        });
    }
};
