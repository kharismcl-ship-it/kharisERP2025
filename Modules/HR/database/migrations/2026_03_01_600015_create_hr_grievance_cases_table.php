<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_grievance_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('grievance_type');
            $table->date('filed_date');
            $table->text('description');
            $table->enum('status', ['filed', 'under_investigation', 'hearing_scheduled', 'resolved', 'closed', 'escalated'])->default('filed');
            $table->text('resolution')->nullable();
            $table->date('resolution_date')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->foreignId('assigned_to_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_grievance_cases');
    }
};