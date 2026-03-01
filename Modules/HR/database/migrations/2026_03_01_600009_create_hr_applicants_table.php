<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_vacancy_id')->constrained('hr_job_vacancies')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('status', ['applied', 'shortlisted', 'interview_scheduled', 'interviewed', 'offered', 'hired', 'rejected', 'withdrawn'])->default('applied');
            $table->enum('source', ['direct', 'referral', 'job_board', 'social_media', 'agency', 'other'])->default('direct');
            $table->string('resume_path')->nullable();
            $table->string('cover_letter_path')->nullable();
            $table->text('notes')->nullable();
            $table->date('applied_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_applicants');
    }
};