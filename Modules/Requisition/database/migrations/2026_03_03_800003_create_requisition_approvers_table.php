<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisitions')->onDelete('cascade');
            $table->unsignedBigInteger('employee_id');
            $table->enum('role', ['reviewer', 'approver'])->default('reviewer');
            $table->enum('decision', ['pending', 'approved', 'rejected', 'commented'])->default('pending');
            $table->dateTime('decided_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_approvers');
    }
};
