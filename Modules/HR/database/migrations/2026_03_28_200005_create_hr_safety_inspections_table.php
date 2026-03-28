<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_safety_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->string('location');
            $table->date('inspection_date');
            $table->foreignId('inspected_by_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->json('checklist_items')->nullable(); // [{item, passed, notes}]
            $table->boolean('overall_passed')->default(false);
            $table->text('summary_notes')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_safety_inspections');
    }
};
