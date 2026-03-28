<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->cascadeOnDelete(); // NULL = template
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('assignee_type')->default('employee'); // employee, hr, manager, it, finance
            $table->integer('due_days_from_hire')->default(1); // day 1, day 30, day 90
            $table->string('status')->default('pending'); // pending, in_progress, completed, skipped
            $table->boolean('is_template')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('completed_by_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_onboarding_tasks');
    }
};
