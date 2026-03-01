<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('trip_reference')->unique();
            $table->date('trip_date');
            $table->string('origin');
            $table->string('destination');
            $table->string('purpose')->nullable();
            $table->decimal('start_mileage', 12, 2)->nullable();
            $table->decimal('end_mileage', 12, 2)->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->time('departure_time')->nullable();
            $table->time('return_time')->nullable();
            $table->string('status')->default('completed'); // planned, in_progress, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_logs');
    }
};
