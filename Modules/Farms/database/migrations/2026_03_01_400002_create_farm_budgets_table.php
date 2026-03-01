<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('budget_name');
            $table->year('budget_year');
            $table->tinyInteger('budget_month')->nullable()->comment('null = full-year budget');
            $table->string('category', 100)->default('general'); // seeds, fertilizer, labour, equipment, general
            $table->decimal('budgeted_amount', 18, 2);
            $table->decimal('actual_amount', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_budgets');
    }
};