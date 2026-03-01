<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mw_chemical_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained('mw_plants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('treatment_stage_id')->nullable()->constrained('mw_treatment_stages')->nullOnDelete();
            $table->string('chemical_name');
            $table->decimal('quantity', 12, 3);
            $table->string('unit', 20)->default('kg'); // kg, litres, ppm
            $table->decimal('unit_cost', 10, 4)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->date('usage_date');
            $table->string('purpose')->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mw_chemical_usages');
    }
};