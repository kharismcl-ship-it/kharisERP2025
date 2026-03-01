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
        Schema::create('hr_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('leave_type_id')->index();
            $table->integer('year')->default(now()->year);
            $table->decimal('initial_balance', 5, 2)->default(0);
            $table->decimal('used_balance', 5, 2)->default(0);
            $table->decimal('current_balance', 5, 2)->default(0);
            $table->decimal('carried_over', 5, 2)->default(0);
            $table->decimal('adjustments', 5, 2)->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('hr_employees')->cascadeOnDelete();
            $table->foreign('leave_type_id')->references('id')->on('hr_leave_types')->cascadeOnDelete();

            // Unique constraint to prevent duplicate balances for same employee, leave type, and year
            $table->unique(['employee_id', 'leave_type_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_leave_balances');
    }
};
