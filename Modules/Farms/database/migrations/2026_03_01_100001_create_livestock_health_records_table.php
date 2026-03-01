<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_batch_id')->constrained('livestock_batches')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('event_type'); // treatment|vaccination|vet_visit|deworming|other
            $table->date('event_date');
            $table->text('description');
            $table->string('medicine_used')->nullable();
            $table->string('dosage')->nullable();
            $table->decimal('cost', 18, 2)->default(0);
            $table->string('administered_by')->nullable();
            $table->date('next_due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_health_records');
    }
};
