<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->smallInteger('budget_year');
            $table->string('period_type')->default('annual'); // annual/quarterly/monthly
            $table->string('status')->default('draft'); // draft/approved/active/closed
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('fin_budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('fin_budgets')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('cost_centre_id')->nullable()->constrained('cost_centres')->nullOnDelete();
            $table->string('description')->nullable();
            $table->decimal('jan', 15, 2)->default(0);
            $table->decimal('feb', 15, 2)->default(0);
            $table->decimal('mar', 15, 2)->default(0);
            $table->decimal('apr', 15, 2)->default(0);
            $table->decimal('may', 15, 2)->default(0);
            $table->decimal('jun', 15, 2)->default(0);
            $table->decimal('jul', 15, 2)->default(0);
            $table->decimal('aug', 15, 2)->default(0);
            $table->decimal('sep', 15, 2)->default(0);
            $table->decimal('oct', 15, 2)->default(0);
            $table->decimal('nov', 15, 2)->default(0);
            $table->decimal('dec', 15, 2)->default(0);
            $table->decimal('annual_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_budget_lines');
        Schema::dropIfExists('fin_budgets');
    }
};