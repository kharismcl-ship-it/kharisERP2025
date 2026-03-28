<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_equipment_maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_equipment_id');
            $table->string('service_type');
            $table->enum('interval_type', ['hours', 'days', 'km']);
            $table->unsignedInteger('interval_value');
            $table->date('last_service_at')->nullable();
            $table->decimal('last_service_hours', 10, 2)->nullable();
            $table->date('next_service_date')->nullable();
            $table->decimal('next_service_hours', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_equipment_id')->references('id')->on('farm_equipment')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_equipment_maintenance_schedules');
    }
};