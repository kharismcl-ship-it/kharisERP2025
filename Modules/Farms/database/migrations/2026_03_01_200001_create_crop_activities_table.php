<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_cycle_id')->constrained('crop_cycles')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_plot_id')->nullable()->constrained('farm_plots')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('activity_type'); // planting|weeding|spraying|irrigation|pruning|harvesting|soil_prep|other
            $table->date('activity_date');
            $table->text('description')->nullable();
            $table->decimal('duration_hours', 8, 2)->default(0);
            $table->unsignedInteger('labour_count')->default(1);
            $table->decimal('cost', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_activities');
    }
};
