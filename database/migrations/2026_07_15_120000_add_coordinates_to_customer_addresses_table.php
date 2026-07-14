<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_addresses', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('address_detail');
            }

            if (!Schema::hasColumn('customer_addresses', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $dropColumns = array_filter([
                Schema::hasColumn('customer_addresses', 'latitude') ? 'latitude' : null,
                Schema::hasColumn('customer_addresses', 'longitude') ? 'longitude' : null,
            ]);

            if ($dropColumns) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
