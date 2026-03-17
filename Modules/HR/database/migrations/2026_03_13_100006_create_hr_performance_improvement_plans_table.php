<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_performance_improvement_plans', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // PIP-YYYYMM-00001
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('manager_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->foreignId('hr_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('review_date')->nullable();
            $table->string('status')->default('draft'); // draft, active, completed, cancelled, escalated
            $table->text('performance_issue')->nullable();  // description of underperformance
            $table->text('improvement_goals');              // SMART goals
            $table->text('support_provided')->nullable();   // training, coaching, resources
            $table->text('milestones')->nullable();         // JSON-ish or text checkpoints
            $table->text('progress_notes')->nullable();     // periodic updates
            $table->string('outcome')->nullable();          // successful, unsuccessful, extended
            $table->text('outcome_notes')->nullable();
            $table->boolean('employee_acknowledged')->default(false);
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_performance_improvement_plans');
    }
};
