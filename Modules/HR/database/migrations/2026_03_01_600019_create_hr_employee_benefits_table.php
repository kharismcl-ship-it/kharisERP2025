<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('benefit_type_id')->constrained('hr_benefit_types')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->decimal('employer_contribution_override', 15, 2)->nullable();
            $table->decimal('employee_contribution_override', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_benefits');
    }
};