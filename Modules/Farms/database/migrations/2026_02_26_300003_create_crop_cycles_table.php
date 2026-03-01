<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_plot_id')->nullable()->constrained('farm_plots')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('crop_name');
            $table->string('variety')->nullable();
            $table->string('season')->nullable(); // major, minor, dry season
            $table->date('planting_date');
            $table->date('expected_harvest_date')->nullable();
            $table->date('actual_harvest_date')->nullable();
            $table->decimal('planted_area', 14, 4)->nullable();
            $table->string('planted_area_unit')->default('acres');
            $table->string('status')->default('growing'); // preparing, growing, harvested, failed
            $table->decimal('expected_yield', 14, 3)->nullable(); // kg or bags
            $table->string('yield_unit')->nullable(); // kg, bags, tonnes
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_cycles');
    }
};
