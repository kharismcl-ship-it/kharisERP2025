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
        Schema::create('hr_leave_approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_all_approvals')->default(false);
            $table->integer('timeout_days')->default(3)->comment('Days before auto-approval if no action');
            $table->timestamps();
        });

        Schema::create('hr_leave_approval_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('hr_leave_approval_workflows')->onDelete('cascade');
            $table->integer('level_number');
            $table->string('approver_type')->comment('manager, specific_employee, department_head, hr');
            $table->foreignId('approver_employee_id')->nullable()->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('approver_department_id')->nullable()->constrained('hr_departments')->onDelete('cascade');
            $table->string('approver_role')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('approval_order');
            $table->timestamps();
        });

        Schema::create('hr_leave_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_request_id')->constrained('hr_leave_requests')->onDelete('cascade');
            $table->foreignId('approval_level_id')->constrained('hr_leave_approval_levels')->onDelete('cascade');
            $table->foreignId('approver_employee_id')->nullable()->constrained('hr_employees')->onDelete('cascade');
            $table->string('status')->default('pending')->comment('pending, approved, rejected, delegated');
            $table->text('comments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_leave_approval_delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approver_employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('delegate_employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_leave_approval_delegations');
        Schema::dropIfExists('hr_leave_approval_requests');
        Schema::dropIfExists('hr_leave_approval_levels');
        Schema::dropIfExists('hr_leave_approval_workflows');
    }
};
