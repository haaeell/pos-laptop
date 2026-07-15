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
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $couriers = [
            ['code' => 'jne', 'name' => 'JNE'],
            ['code' => 'jnt', 'name' => 'J&T'],
            ['code' => 'sicepat', 'name' => 'SiCepat'],
            ['code' => 'anteraja', 'name' => 'AnterAja'],
            ['code' => 'ninja', 'name' => 'Ninja Xpress'],
            ['code' => 'pos', 'name' => 'POS Indonesia'],
            ['code' => 'tiki', 'name' => 'TIKI'],
            ['code' => 'grab', 'name' => 'GrabExpress'],
            ['code' => 'gojek', 'name' => 'GoSend'],
            ['code' => 'lalamove', 'name' => 'Lalamove'],
        ];

        foreach ($couriers as $courier) {
            \App\Models\Courier::create($courier);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
