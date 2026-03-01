<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_disciplinary_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->enum('type', ['verbal_warning', 'written_warning', 'final_warning', 'suspension', 'termination'])->default('verbal_warning');
            $table->date('incident_date');
            $table->text('incident_description');
            $table->text('action_taken')->nullable();
            $table->enum('status', ['open', 'under_review', 'resolved', 'appealed', 'closed'])->default('open');
            $table->date('resolution_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->foreignId('handled_by_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_disciplinary_cases');
    }
};