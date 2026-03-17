<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_kpi_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_review_id')->constrained('hr_performance_reviews')->cascadeOnDelete();
            $table->foreignId('kpi_definition_id')->constrained('hr_kpi_definitions')->cascadeOnDelete();
            $table->decimal('target_value', 10, 2)->nullable();
            $table->decimal('actual_value', 10, 2)->nullable();
            $table->decimal('score', 5, 2)->nullable()->comment('0-100 normalised score');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_kpi_scores');
    }
};
