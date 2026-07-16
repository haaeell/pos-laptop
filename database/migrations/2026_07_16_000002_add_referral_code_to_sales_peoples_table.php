<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_peoples', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_peoples', 'referral_code')) {
                $table->string('referral_code')->nullable()->after('fee');
            }
        });

        $salesPeople = DB::table('sales_peoples')->select('id', 'name', 'referral_code')->orderBy('id')->get();

        foreach ($salesPeople as $salesPerson) {
            if (!blank($salesPerson->referral_code)) {
                continue;
            }

            $baseCode = 'SALES-' . Str::upper(Str::slug((string) $salesPerson->name, '-'));
            $baseCode = trim($baseCode, '-');
            $baseCode = $baseCode !== 'SALES' ? $baseCode : 'SALES-' . $salesPerson->id;
            $code = $baseCode;
            $suffix = 1;

            while (DB::table('sales_peoples')->where('referral_code', $code)->where('id', '!=', $salesPerson->id)->exists()) {
                $code = $baseCode . '-' . $suffix;
                $suffix++;
            }

            DB::table('sales_peoples')->where('id', $salesPerson->id)->update(['referral_code' => $code]);
        }

        Schema::table('sales_peoples', function (Blueprint $table) {
            if (Schema::hasColumn('sales_peoples', 'referral_code')) {
                $table->unique('referral_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_peoples', function (Blueprint $table) {
            if (Schema::hasColumn('sales_peoples', 'referral_code')) {
                $table->dropUnique(['referral_code']);
                $table->dropColumn('referral_code');
            }
        });
    }
};
