<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_employee_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('performance_cycle_id')->nullable()->constrained('hr_performance_cycles')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('actual_value', 15, 2)->nullable();
            $table->string('unit_of_measure')->nullable()->comment('e.g. %, count, GHS');
            $table->date('due_date')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'cancelled'])->default('not_started');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_goals');
    }
};