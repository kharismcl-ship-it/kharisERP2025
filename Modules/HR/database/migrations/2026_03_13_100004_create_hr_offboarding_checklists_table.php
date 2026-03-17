<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_offboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('last_working_day')->nullable();
            $table->string('resignation_type')->default('resignation'); // resignation, termination, retirement, redundancy
            $table->text('reason')->nullable();
            $table->string('status')->default('initiated'); // initiated, in_progress, completed
            // Checklist items
            $table->boolean('assets_returned')->default(false);
            $table->boolean('access_revoked')->default(false);
            $table->boolean('knowledge_transfer_done')->default(false);
            $table->boolean('clearance_signed')->default(false);
            $table->boolean('final_payroll_processed')->default(false);
            $table->boolean('exit_interview_done')->default(false);
            // Metadata
            $table->text('exit_interview_notes')->nullable();
            $table->text('assets_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_offboarding_checklists');
    }
};
