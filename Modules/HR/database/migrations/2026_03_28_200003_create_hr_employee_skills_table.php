<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('hr_skills')->cascadeOnDelete();
            $table->unsignedTinyInteger('proficiency_level')->default(1); // 1=Beginner, 2=Basic, 3=Intermediate, 4=Advanced, 5=Expert
            $table->date('acquired_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('verified_by_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->date('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_skills');
    }
};
