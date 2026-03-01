<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mp_production_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained('mp_plants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('line_type', 50)->default('paper'); // paper, pulp, coating, finishing
            $table->decimal('capacity_per_day', 12, 2)->nullable();
            $table->string('capacity_unit', 20)->default('tonnes');
            $table->boolean('is_active')->default(true);
            $table->string('status', 30)->default('operational'); // operational, maintenance, idle, decommissioned
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mp_production_lines');
    }
};