<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('book_periods', function (Blueprint $table) {
            $table->id();
            $table->date('starts_at')->unique();
            $table->date('ends_at')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->longText('snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_periods');
    }
};
