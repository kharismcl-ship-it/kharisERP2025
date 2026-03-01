<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_training_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained('hr_training_programs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->enum('status', ['nominated', 'confirmed', 'attended', 'completed', 'cancelled'])->default('nominated');
            $table->date('completion_date')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['training_program_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_training_nominations');
    }
};