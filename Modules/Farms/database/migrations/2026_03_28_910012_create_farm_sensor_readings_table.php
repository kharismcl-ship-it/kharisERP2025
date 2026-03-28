<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_iot_device_id')->constrained('farm_iot_devices')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->decimal('reading_value', 10, 4);
            $table->string('reading_unit')->default('unit');
            $table->datetime('recorded_at');
            $table->enum('quality_flag', ['good', 'suspect', 'bad'])->default('good');
            $table->string('notes')->nullable();
            // No timestamps — recorded_at is the only time field

            $table->index('farm_iot_device_id');
            $table->index(['company_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_sensor_readings');
    }
};