<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_varieties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('crop_name');                          // e.g. Maize
            $table->string('variety_name');                       // e.g. Pioneer 30B74
            $table->string('seed_supplier')->nullable();
            $table->text('description')->nullable();
            $table->decimal('typical_yield_per_acre', 10, 3)->nullable();
            $table->string('yield_unit', 50)->nullable();         // kg, bags, tonnes
            $table->unsignedSmallInteger('growing_period_days')->nullable();
            $table->string('planting_season')->nullable();        // e.g. Major, Minor, Dry
            $table->decimal('spacing_cm', 6, 2)->nullable();
            $table->decimal('seed_rate_per_acre', 8, 3)->nullable();
            $table->string('seed_unit', 50)->nullable();          // kg, seeds, packets
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_varieties');
    }
};