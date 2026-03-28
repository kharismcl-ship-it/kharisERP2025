<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_trial_plots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_trial_id');
            $table->unsignedBigInteger('farm_plot_id')->nullable();
            $table->string('treatment_label');
            $table->string('treatment_description')->nullable();
            $table->decimal('area_ha', 8, 4)->nullable();
            $table->decimal('expected_yield_kg', 10, 2)->nullable();
            $table->decimal('actual_yield_kg', 10, 2)->nullable();
            $table->decimal('total_input_cost', 12, 2)->nullable();
            $table->decimal('yield_per_ha', 10, 4)->nullable();
            $table->decimal('cost_per_kg', 10, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('farm_trial_id')->references('id')->on('farm_trials')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_trial_plots');
    }
};