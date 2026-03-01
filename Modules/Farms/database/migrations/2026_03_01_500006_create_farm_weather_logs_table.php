<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_weather_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('log_date');
            $table->decimal('rainfall_mm', 8, 2)->nullable();
            $table->decimal('min_temp_c', 5, 2)->nullable();
            $table->decimal('max_temp_c', 5, 2)->nullable();
            $table->unsignedSmallInteger('humidity_pct')->nullable();
            $table->decimal('wind_speed_kmh', 6, 2)->nullable();
            $table->string('weather_condition', 30)->nullable(); // sunny, partly_cloudy, cloudy, rainy, stormy, dry, foggy
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['farm_id', 'log_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_weather_logs');
    }
};