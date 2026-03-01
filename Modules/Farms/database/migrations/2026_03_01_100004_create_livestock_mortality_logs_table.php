<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_mortality_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_batch_id')->constrained('livestock_batches')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('event_date');
            $table->unsignedInteger('count')->default(1);
            $table->string('cause'); // disease|injury|natural|unknown|other
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_mortality_logs');
    }
};