<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_weather_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->enum('alert_type', [
                'frost', 'heat_stress', 'heavy_rain', 'drought', 'high_wind',
                'disease_pressure', 'spray_window_open', 'spray_window_closed',
            ]);
            $table->enum('severity', ['info', 'warning', 'critical']);
            $table->string('title');
            $table->text('message');
            $table->decimal('temperature_c', 5, 2)->nullable();
            $table->decimal('humidity_pct', 5, 2)->nullable();
            $table->decimal('wind_speed_kmh', 5, 2)->nullable();
            $table->decimal('rainfall_mm', 6, 2)->nullable();
            $table->dateTime('triggered_at');
            $table->dateTime('resolved_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_weather_alerts');
    }
};