<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_training_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('trainer_employee_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('session_type', ['workshop', 'webinar', 'self_paced', 'on_the_job', 'certification']);
            $table->dateTime('scheduled_at');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('location')->nullable();
            $table->unsignedInteger('max_attendees')->nullable();
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');
            $table->string('materials_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('trainer_employee_id')->references('id')->on('hr_employees')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('hr_departments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_training_sessions');
    }
};
