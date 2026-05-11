<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->unsignedBigInteger('sales_person_id')->nullable();
            $table->string('full_name');
            $table->string('position');
            $table->date('join_date');
            $table->date('birth_date');
            $table->string('phone');
            $table->text('address');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('sales_person_id')
                ->references('id')
                ->on('sales_peoples')
                ->onDelete('set null');
        });

        // Add employee_id to sales_peoples table
        Schema::table('sales_peoples', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('id');
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_number')->unique();
            $table->year('period_year');
            $table->tinyInteger('period_month');
            $table->date('release_date');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['draft', 'released', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('released_by')->nullable();
            $table->timestamps();

            $table->foreign('released_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('sales_bonus', 15, 2)->default(0);
            $table->decimal('technician_fee', 15, 2)->default(0);
            $table->decimal('other_allowance', 15, 2)->default(0);
            $table->decimal('deduction', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2);
            $table->integer('total_transactions')->default(0);
            $table->timestamps();

            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('sales_peoples', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });

        Schema::dropIfExists('employees');
    }
};
