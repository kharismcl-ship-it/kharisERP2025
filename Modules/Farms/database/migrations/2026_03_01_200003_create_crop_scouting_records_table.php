<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_scouting_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_cycle_id')->constrained('crop_cycles')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_plot_id')->nullable()->constrained('farm_plots')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('scouting_date');
            $table->string('scouted_by')->nullable();
            $table->string('finding_type'); // pest|disease|weed|nutrient_deficiency|weather_damage|normal|other
            $table->string('severity'); // low|medium|high|critical
            $table->text('description');
            $table->text('recommended_action')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_scouting_records');
    }
};