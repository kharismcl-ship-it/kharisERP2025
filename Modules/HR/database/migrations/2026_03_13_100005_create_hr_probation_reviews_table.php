<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_probation_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('reviewer_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->date('probation_start_date');
            $table->date('probation_end_date');
            $table->date('review_date')->nullable();
            $table->string('status')->default('pending'); // pending, in_review, passed, extended, failed
            $table->integer('probation_months')->default(3);
            $table->integer('extension_months')->nullable();
            $table->date('extended_end_date')->nullable();
            $table->text('performance_summary')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('reviewer_recommendation')->nullable(); // confirm, extend, terminate
            $table->text('hr_decision_notes')->nullable();
            $table->integer('overall_rating')->nullable(); // 1-5
            $table->boolean('employee_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_probation_reviews');
    }
};
