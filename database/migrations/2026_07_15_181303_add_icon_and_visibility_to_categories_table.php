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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('categories', 'show_on_customer_site')) {
                $table->boolean('show_on_customer_site')->default(true)->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'show_on_customer_site')) {
                $table->dropColumn('show_on_customer_site');
            }
            if (Schema::hasColumn('categories', 'icon')) {
                $table->dropColumn('icon');
            }
        });
    }
};
