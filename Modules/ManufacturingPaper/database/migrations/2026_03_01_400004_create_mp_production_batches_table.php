<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mp_production_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained('mp_plants')->cascadeOnDelete();
            $table->foreignId('production_line_id')->constrained('mp_production_lines')->cascadeOnDelete();
            $table->foreignId('paper_grade_id')->constrained('mp_paper_grades')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('batch_number', 50)->unique();
            $table->decimal('quantity_planned', 12, 3)->default(0);
            $table->decimal('quantity_produced', 12, 3)->default(0);
            $table->string('unit', 20)->default('tonnes');
            $table->decimal('waste_quantity', 12, 3)->default(0);
            $table->decimal('raw_material_used', 12, 3)->nullable();
            $table->decimal('production_cost', 15, 2)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('status', 30)->default('planned'); // planned, in_progress, completed, cancelled, on_hold
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mp_production_batches');
    }
};