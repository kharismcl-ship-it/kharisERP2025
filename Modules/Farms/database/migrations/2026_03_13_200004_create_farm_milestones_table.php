<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('farm_milestones')) {
            return;
        }

        Schema::create('farm_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_season_id')->constrained('farm_seasons')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('milestone_type')->default('other'); // land_prep|planting|growing|scouting|harvesting|selling|reporting|other
            $table->date('target_date');
            $table->date('actual_date')->nullable();
            $table->string('status')->default('pending');      // pending|in_progress|completed|missed
            $table->text('progress_notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_milestones');
    }
};
