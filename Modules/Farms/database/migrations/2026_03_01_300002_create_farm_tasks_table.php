<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_plot_id')->nullable()->constrained('farm_plots')->nullOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->foreignId('livestock_batch_id')->nullable()->constrained('livestock_batches')->nullOnDelete();
            $table->foreignId('assigned_to_worker_id')->nullable()->constrained('farm_workers')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('task_type', 50)->default('other');
            $table->string('priority', 20)->default('medium');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_tasks');
    }
};