<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_feed_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_batch_id')->constrained('livestock_batches')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('feed_date');
            $table->string('feed_type');
            $table->decimal('quantity_kg', 10, 3);
            $table->decimal('unit_cost', 14, 4)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_feed_records');
    }
};