<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_training_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('it_training_session_id')->constrained('it_training_sessions')->onDelete('cascade');
            $table->unsignedBigInteger('employee_id');
            $table->tinyInteger('attended')->default(0);
            $table->text('feedback')->nullable();
            $table->tinyInteger('rating')->nullable()->comment('1-5');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
            $table->unique(['it_training_session_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_training_attendees');
    }
};
