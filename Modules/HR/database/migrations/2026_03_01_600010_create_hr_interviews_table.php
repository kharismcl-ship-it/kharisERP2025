<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('hr_applicants')->cascadeOnDelete();
            $table->enum('interview_type', ['phone_screening', 'technical', 'hr', 'panel', 'final'])->default('hr');
            $table->dateTime('scheduled_at');
            $table->string('location')->nullable()->comment('Room name or video link');
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->enum('result', ['passed', 'failed', 'pending'])->default('pending');
            $table->tinyInteger('score')->nullable()->comment('Score out of 10');
            $table->text('feedback')->nullable();
            $table->foreignId('interviewer_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_interviews');
    }
};