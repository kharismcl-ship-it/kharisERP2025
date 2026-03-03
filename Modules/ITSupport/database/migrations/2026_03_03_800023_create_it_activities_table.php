<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('performed_by_employee_id')->nullable();
            $table->enum('activity_type', ['maintenance', 'audit', 'deployment', 'configuration', 'backup', 'security_check', 'upgrade', 'other']);
            $table->string('title');
            $table->text('description');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->text('affected_systems')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('performed_by_employee_id')->references('id')->on('hr_employees')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_activities');
    }
};
