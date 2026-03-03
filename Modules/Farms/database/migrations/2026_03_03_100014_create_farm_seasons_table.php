<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->smallInteger('season_year');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->decimal('target_yield', 15, 3)->nullable();
            $table->string('yield_unit')->nullable();
            $table->decimal('total_budget', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('farm_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_season_id')->constrained('farm_seasons')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('milestone_type', ['land_prep', 'planting', 'growing', 'scouting', 'harvesting', 'selling', 'reporting', 'other'])->default('other');
            $table->date('target_date');
            $table->date('actual_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'missed'])->default('pending');
            $table->text('progress_notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_milestones');
        Schema::dropIfExists('farm_seasons');
    }
};
