<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_worker_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_worker_id')->constrained('farm_workers')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id')->index();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'half_day', 'leave'])->default('present');
            $table->decimal('hours_worked', 4, 2)->nullable();
            $table->decimal('overtime_hours', 4, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['farm_worker_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_worker_attendances');
    }
};
