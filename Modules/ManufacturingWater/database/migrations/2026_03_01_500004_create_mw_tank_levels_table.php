<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mw_tank_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained('mw_plants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('tank_name');
            $table->decimal('capacity_liters', 15, 2);
            $table->decimal('current_level_liters', 15, 2)->default(0);
            $table->dateTime('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mw_tank_levels');
    }
};