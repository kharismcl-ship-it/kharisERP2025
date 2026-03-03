<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('reference')->unique()->nullable();
            $table->unsignedBigInteger('requester_employee_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('category', ['hardware', 'software', 'network', 'access', 'email', 'training', 'other']);
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'pending_info', 'resolved', 'closed', 'cancelled'])->default('open');
            $table->unsignedBigInteger('assigned_to_employee_id')->nullable();
            $table->date('estimated_resolution_date')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('requester_employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('hr_departments')->onDelete('set null');
            $table->foreign('assigned_to_employee_id')->references('id')->on('hr_employees')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_requests');
    }
};
