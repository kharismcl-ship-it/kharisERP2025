<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_benefit_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->enum('category', ['health', 'insurance', 'transport', 'housing', 'education', 'retirement', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->string('provider')->nullable();
            $table->decimal('employer_contribution', 15, 2)->nullable();
            $table->boolean('employee_contribution_required')->default(false);
            $table->decimal('employee_contribution', 15, 2)->nullable();
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_benefit_types');
    }
};