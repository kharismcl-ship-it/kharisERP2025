<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mw_treatment_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained('mw_plants')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('stage_order')->default(1);
            $table->string('stage_type', 50)->default('filtration'); // filtration, chlorination, UV, RO, ozone, sedimentation, fluoridation
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mw_treatment_stages');
    }
};