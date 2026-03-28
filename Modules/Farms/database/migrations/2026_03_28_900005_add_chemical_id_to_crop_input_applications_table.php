<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crop_input_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('farm_input_chemical_id')->nullable()->after('id');
            $table->string('weather_condition')->nullable();
            $table->decimal('wind_speed_kmh', 5, 2)->nullable();
            $table->decimal('temperature_c', 5, 2)->nullable();
            $table->decimal('humidity_pct', 5, 2)->nullable();
            $table->unsignedBigInteger('applicator_worker_id')->nullable();
            $table->boolean('phi_compliant')->nullable();

            $table->foreign('farm_input_chemical_id')->references('id')->on('farm_input_chemicals')->onDelete('set null');
            $table->foreign('applicator_worker_id')->references('id')->on('farm_workers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('crop_input_applications', function (Blueprint $table) {
            $table->dropForeign(['farm_input_chemical_id']);
            $table->dropForeign(['applicator_worker_id']);
            $table->dropColumn([
                'farm_input_chemical_id', 'weather_condition', 'wind_speed_kmh',
                'temperature_c', 'humidity_pct', 'applicator_worker_id', 'phi_compliant',
            ]);
        });
    }
};