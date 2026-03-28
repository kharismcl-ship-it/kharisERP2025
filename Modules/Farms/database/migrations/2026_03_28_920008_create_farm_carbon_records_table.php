<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_carbon_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('crop_cycle_id')->nullable();
            $table->unsignedBigInteger('livestock_batch_id')->nullable();
            $table->string('record_period');
            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('fertilizer_emissions_tco2e', 10, 4)->default(0);
            $table->decimal('fuel_emissions_tco2e', 10, 4)->default(0);
            $table->decimal('livestock_emissions_tco2e', 10, 4)->default(0);
            $table->decimal('electricity_emissions_tco2e', 10, 4)->default(0);
            $table->decimal('other_emissions_tco2e', 10, 4)->default(0);
            $table->decimal('total_emissions_tco2e', 10, 4)->default(0);

            $table->decimal('soil_sequestration_tco2e', 10, 4)->default(0);
            $table->decimal('tree_sequestration_tco2e', 10, 4)->default(0);
            $table->decimal('net_emissions_tco2e', 10, 4)->default(0);

            $table->decimal('farm_area_ha', 8, 4)->nullable();
            $table->decimal('total_production_kg', 10, 2)->nullable();
            $table->decimal('emissions_per_ha', 10, 4)->nullable();
            $table->decimal('emissions_per_kg', 10, 6)->nullable();

            $table->decimal('water_used_m3', 12, 2)->default(0);
            $table->decimal('water_per_tonne_produce', 10, 4)->nullable();

            $table->text('methodology_notes')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_carbon_records');
    }
};