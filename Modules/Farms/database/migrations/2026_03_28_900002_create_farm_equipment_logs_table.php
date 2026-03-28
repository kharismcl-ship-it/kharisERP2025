<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_equipment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_equipment_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('farm_plot_id')->nullable();
            $table->unsignedBigInteger('operator_worker_id')->nullable();
            $table->string('operation_type');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->decimal('hours_used', 8, 2)->nullable();
            $table->decimal('area_covered_ha', 8, 4)->nullable();
            $table->decimal('fuel_used_litres', 8, 2)->nullable();
            $table->decimal('fuel_cost', 12, 2)->nullable();
            $table->decimal('cost_per_ha', 12, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_equipment_id')->references('id')->on('farm_equipment')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('farm_plot_id')->references('id')->on('farm_plots')->onDelete('set null');
            $table->foreign('operator_worker_id')->references('id')->on('farm_workers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_equipment_logs');
    }
};