<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_service_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'leave', 'holiday'])->default('present');
            $table->unsignedInteger('late_minutes')->nullable();
            $table->unsignedInteger('overtime_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('hr_departments')->onDelete('set null');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_service_attendances');
    }
};
