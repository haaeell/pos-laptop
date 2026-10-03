<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_rate_tiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('min_qty')->unique();
            $table->decimal('daily_rate', 15, 2);
            $table->timestamps();
        });

        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('rental_number')->unique();
            $table->foreignId('user_id')->constrained();
            $table->string('renter_name');
            $table->text('address');
            $table->string('purpose');
            $table->string('person_in_charge');
            $table->string('phone', 30);
            $table->json('rental_dates');
            $table->date('planned_return_date');
            $table->unsignedInteger('total_qty');
            $table->decimal('daily_rate', 15, 2);
            $table->decimal('rental_total', 15, 2);
            $table->decimal('fine_amount', 15, 2)->default(0);
            $table->text('return_notes')->nullable();
            $table->enum('status', ['active', 'returned'])->default('active');
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rental_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('product_name');
            $table->string('product_code');
            $table->unsignedInteger('qty');
            $table->string('accessories')->default('Laptop dan Charger');
            $table->string('condition_out')->default('Normal');
            $table->string('condition_in')->nullable();
            $table->text('return_issue')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_items');
        Schema::dropIfExists('rentals');
        Schema::dropIfExists('rental_rate_tiers');
    }
};
