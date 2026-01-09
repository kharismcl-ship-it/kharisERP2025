<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hostel_staff_attendance')) {
            return;
        }

        Schema::create('hostel_staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hostel_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->date('attendance_date');
            $table->time('clock_in_time')->nullable();
            $table->time('clock_out_time')->nullable();
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->enum('status', ['present', 'absent', 'late', 'early_departure', 'half_day'])->default('present');
            $table->text('notes')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('hr_employees')->onDelete('cascade');

            $table->unique(['hostel_id', 'employee_id', 'attendance_date'], 'unique_staff_attendance');
            $table->index(['hostel_id', 'attendance_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_staff_attendance');
    }
};
