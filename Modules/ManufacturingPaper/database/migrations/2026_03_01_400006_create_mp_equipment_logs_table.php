<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mp_equipment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained('mp_plants')->cascadeOnDelete();
            $table->foreignId('production_line_id')->nullable()->constrained('mp_production_lines')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('equipment_name');
            $table->string('log_type', 40)->default('maintenance'); // maintenance, breakdown, inspection, upgrade, calibration
            $table->text('description');
            $table->dateTime('logged_at');
            $table->dateTime('resolved_at')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('status', 30)->default('open'); // open, in_progress, resolved, closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mp_equipment_logs');
    }
};