<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_weight_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_batch_id')->constrained('livestock_batches')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('record_date');
            $table->unsignedInteger('sample_size')->default(1);
            $table->decimal('avg_weight_kg', 10, 3);
            $table->decimal('min_weight_kg', 10, 3)->nullable();
            $table->decimal('max_weight_kg', 10, 3)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_weight_records');
    }
};