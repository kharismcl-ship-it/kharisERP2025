<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hostel_housekeeping_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('assigned_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->date('schedule_date');
            $table->enum('cleaning_type', ['daily', 'deep', 'weekly', 'monthly']);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('quality_score')->nullable()->comment('1-5 rating');
            $table->timestamps();

            $table->index(['hostel_id', 'schedule_date']);
            $table->index(['room_id', 'schedule_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_housekeeping_schedules');
    }
};
