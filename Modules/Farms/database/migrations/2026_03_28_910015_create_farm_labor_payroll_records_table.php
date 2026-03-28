<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_labor_payroll_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_worker_id')->constrained('farm_workers')->cascadeOnDelete();
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->string('payment_ref')->unique();
            $table->enum('pay_type', ['daily_rate', 'piece_rate', 'monthly_salary', 'weekly_rate']);
            $table->decimal('days_worked', 5, 2)->nullable();
            $table->decimal('pieces_count', 10, 2)->nullable();
            $table->decimal('rate_per_day', 10, 4)->nullable();
            $table->decimal('rate_per_piece', 10, 4)->nullable();
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->decimal('gross_pay', 12, 2);
            $table->json('deductions_json')->nullable(); // [{label, amount}]
            $table->decimal('net_pay', 12, 2);
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer'])->default('cash');
            $table->string('momo_number')->nullable();
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('farm_id');
            $table->index('farm_worker_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_labor_payroll_records');
    }
};