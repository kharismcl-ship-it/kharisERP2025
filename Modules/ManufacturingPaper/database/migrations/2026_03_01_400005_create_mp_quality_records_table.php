<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mp_quality_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_batch_id')->constrained('mp_production_batches')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('test_date');
            $table->string('tested_by')->nullable();
            $table->decimal('tensile_cd', 8, 3)->nullable();   // cross direction, kN/m
            $table->decimal('tensile_md', 8, 3)->nullable();   // machine direction, kN/m
            $table->decimal('burst_strength', 8, 3)->nullable(); // kPa
            $table->decimal('moisture_percent', 6, 2)->nullable();
            $table->decimal('brightness', 6, 2)->nullable();   // ISO brightness %
            $table->decimal('opacity', 6, 2)->nullable();      // opacity %
            $table->decimal('roughness', 8, 2)->nullable();    // PPS roughness ml/min
            $table->decimal('basis_weight', 8, 2)->nullable(); // actual g/m²
            $table->boolean('passed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mp_quality_records');
    }
};