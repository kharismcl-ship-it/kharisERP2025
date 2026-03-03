<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('requester_employee_id');
            $table->unsignedBigInteger('target_company_id')->nullable();
            $table->unsignedBigInteger('target_department_id')->nullable();
            $table->string('reference')->unique()->nullable();
            $table->enum('request_type', ['material', 'fund', 'general', 'equipment', 'service', 'other'])->default('general');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('urgency', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'fulfilled'])->default('draft');
            $table->unsignedBigInteger('cost_centre_id')->nullable();
            $table->decimal('total_estimated_cost', 12, 2)->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('fulfilled_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('requester_employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
            $table->foreign('target_department_id')->references('id')->on('hr_departments')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
