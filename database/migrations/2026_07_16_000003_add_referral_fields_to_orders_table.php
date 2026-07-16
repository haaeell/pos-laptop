<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'sales_person_id')) {
                $table->foreignId('sales_person_id')
                    ->nullable()
                    ->after('customer_id')
                    ->constrained('sales_peoples')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'referral_code')) {
                $table->string('referral_code')->nullable()->after('shipping_cost');
            }

            if (!Schema::hasColumn('orders', 'marketing_name')) {
                $table->string('marketing_name')->nullable()->after('referral_code');
            }

            if (!Schema::hasColumn('orders', 'referral_discount')) {
                $table->decimal('referral_discount', 15, 2)->default(0)->after('marketing_name');
            }

            if (!Schema::hasColumn('orders', 'marketing_fee_before_discount')) {
                $table->decimal('marketing_fee_before_discount', 15, 2)->default(0)->after('referral_discount');
            }

            if (!Schema::hasColumn('orders', 'marketing_fee_discount')) {
                $table->decimal('marketing_fee_discount', 15, 2)->default(0)->after('marketing_fee_before_discount');
            }

            if (!Schema::hasColumn('orders', 'marketing_fee_after_discount')) {
                $table->decimal('marketing_fee_after_discount', 15, 2)->default(0)->after('marketing_fee_discount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'sales_person_id')) {
                $table->dropConstrainedForeignId('sales_person_id');
            }

            foreach ([
                'referral_code',
                'marketing_name',
                'referral_discount',
                'marketing_fee_before_discount',
                'marketing_fee_discount',
                'marketing_fee_after_discount',
            ] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
