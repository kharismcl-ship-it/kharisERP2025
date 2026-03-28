<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_petty_cash_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('custodian_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->decimal('float_amount', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->foreignId('gl_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fin_petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained('fin_petty_cash_funds')->cascadeOnDelete();
            $table->string('transaction_type')->default('expense'); // expense/replenishment/adjustment
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->foreignId('expense_category_id')->nullable()->constrained('fin_expense_categories')->nullOnDelete();
            $table->string('receipt_path')->nullable();
            $table->date('transaction_date');
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_petty_cash_transactions');
        Schema::dropIfExists('fin_petty_cash_funds');
    }
};