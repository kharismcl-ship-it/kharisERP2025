<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mw_water_test_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained('mw_plants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('test_date');
            $table->string('test_type', 30)->default('treated'); // raw, treated, final, distribution
            $table->decimal('ph', 5, 2)->nullable();
            $table->decimal('turbidity_ntu', 10, 3)->nullable();
            $table->decimal('tds_ppm', 10, 2)->nullable();          // total dissolved solids
            $table->decimal('coliform_count', 10, 2)->nullable();   // CFU/100ml
            $table->decimal('chlorine_residual', 8, 3)->nullable(); // mg/L
            $table->decimal('temperature_c', 6, 2)->nullable();
            $table->decimal('dissolved_oxygen', 8, 3)->nullable();  // mg/L
            $table->boolean('passed')->default(false);
            $table->string('tested_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mw_water_test_records');
    }
};
