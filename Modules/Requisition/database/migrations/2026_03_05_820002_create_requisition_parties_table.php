<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisitions')->cascadeOnDelete();
            $table->enum('party_type', ['employee', 'department'])->default('employee');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('reason', ['for_info', 'for_action', 'for_approval'])->default('for_info');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('hr_employees')->nullOnDelete();
            $table->foreign('department_id')->references('id')->on('hr_departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_parties');
    }
};