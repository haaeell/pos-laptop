<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // php artisan make:migration create_services_table
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_number')->unique(); // SVC-20260001
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('device_type')->nullable();   // Laptop, HP, PC, dll
            $table->string('device_brand')->nullable();
            $table->string('device_sn')->nullable();     // Serial number
            $table->text('complaint');                   // Keluhan
            $table->text('notes')->nullable();           // Catatan tambahan
            $table->text('technician_notes')->nullable(); // Catatan teknisi
            $table->decimal('spare_part_cost', 15, 2)->default(0);
            $table->decimal('service_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            // Status: pending → estimated → approved/rejected → in_progress → done
            $table->enum('status', [
                'pending',      // baru masuk, belum ada estimasi
                'estimated',    // teknisi sudah kasih estimasi
                'approved',     // konsumen setuju, lanjut proses
                'rejected',     // konsumen tidak setuju, batal
                'in_progress',  // sedang dikerjakan
                'done',         // selesai, siap diambil
                'taken',        // sudah diambil konsumen
            ])->default('pending');

            $table->date('estimated_done')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamp('taken_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('service_technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('fee_share', 15, 2)->default(0); // bagian fee teknisi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
