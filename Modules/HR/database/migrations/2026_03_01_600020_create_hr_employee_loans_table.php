<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->enum('loan_type', ['salary_advance', 'personal_loan', 'emergency_loan'])->default('personal_loan');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('outstanding_balance', 15, 2);
            $table->decimal('monthly_deduction', 15, 2)->nullable();
            $table->date('approved_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->unsignedTinyInteger('repayment_months')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'cleared'])->default('pending');
            $table->text('purpose')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_loans');
    }
};