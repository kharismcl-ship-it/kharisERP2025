<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_expense_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->string('claim_number')->unique();
            $table->date('claim_date');
            $table->text('purpose');
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft/submitted/approved/rejected/paid
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('fin_expense_claim_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('fin_expense_claims')->cascadeOnDelete();
            $table->foreignId('expense_category_id')->nullable()->constrained('fin_expense_categories')->nullOnDelete();
            $table->string('description');
            $table->date('expense_date');
            $table->decimal('amount', 15, 2);
            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_expense_claim_lines');
        Schema::dropIfExists('fin_expense_claims');
    }
};