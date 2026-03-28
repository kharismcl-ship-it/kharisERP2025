<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_iot_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_plot_id')->nullable()->constrained('farm_plots')->nullOnDelete();
            $table->string('device_name');
            $table->enum('device_type', ['soil_moisture', 'weather_station', 'temperature', 'humidity', 'water_flow', 'ph_sensor', 'other']);
            $table->string('manufacturer')->nullable();
            $table->string('model_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('api_endpoint')->nullable();
            $table->string('api_key')->nullable();
            $table->unsignedSmallInteger('reading_interval_minutes')->default(60);
            $table->datetime('last_reading_at')->nullable();
            $table->decimal('last_reading_value', 10, 4)->nullable();
            $table->decimal('battery_pct', 5, 2)->nullable();
            $table->enum('status', ['active', 'offline', 'maintenance'])->default('active');
            $table->timestamps();

            $table->index('company_id');
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_iot_devices');
    }
};