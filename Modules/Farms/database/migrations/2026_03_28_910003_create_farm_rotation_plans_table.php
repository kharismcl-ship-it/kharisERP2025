<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_rotation_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_plot_id')->constrained('farm_plots')->cascadeOnDelete();
            $table->string('plan_name');
            $table->string('start_season'); // e.g. "2025 Season A"
            $table->unsignedSmallInteger('total_years')->default(3);
            $table->json('rotation_sequence')->nullable(); // [{year, season, crop_variety_id, crop_name, notes}]
            $table->text('nitrogen_balance_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_rotation_plans');
    }
};