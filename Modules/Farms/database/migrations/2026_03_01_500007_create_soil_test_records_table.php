<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soil_test_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_plot_id')->nullable()->constrained('farm_plots')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('test_date');
            $table->string('tested_by')->nullable();
            $table->string('lab_reference')->nullable();
            $table->decimal('ph_level', 4, 2)->nullable();
            $table->decimal('nitrogen_pct', 6, 3)->nullable();
            $table->decimal('phosphorus_ppm', 8, 3)->nullable();
            $table->decimal('potassium_ppm', 8, 3)->nullable();
            $table->decimal('organic_matter_pct', 5, 2)->nullable();
            $table->string('texture', 30)->nullable(); // clay, loam, sandy, silt, clay_loam, sandy_loam
            $table->text('recommendations')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soil_test_records');
    }
};