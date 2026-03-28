<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('survey_type')->default('pulse'); // pulse, engagement, lifecycle, exit, onboarding
            $table->string('status')->default('draft'); // draft, active, closed
            $table->boolean('is_anonymous')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('created_by_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('hr_survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('hr_surveys')->cascadeOnDelete();
            $table->text('question');
            $table->string('question_type')->default('rating'); // rating, text, multiple_choice, yes_no
            $table->json('options')->nullable(); // for multiple_choice: ["Option A","Option B"]
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('hr_survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('hr_surveys')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->nullOnDelete(); // NULL if anonymous
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained('hr_survey_responses')->cascadeOnDelete();
            $table->foreignId('survey_question_id')->constrained('hr_survey_questions')->cascadeOnDelete();
            $table->text('answer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_survey_answers');
        Schema::dropIfExists('hr_survey_responses');
        Schema::dropIfExists('hr_survey_questions');
        Schema::dropIfExists('hr_surveys');
    }
};
