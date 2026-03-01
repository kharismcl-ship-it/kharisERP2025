<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_batch_id')->constrained('livestock_batches')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('event_type', 50);   // birth, purchase, transfer_in, transfer_out, sale, death, other
            $table->date('event_date');
            $table->unsignedInteger('count')->default(1)->comment('Animals involved in this event');
            $table->decimal('unit_cost', 14, 4)->nullable()->comment('Purchase price or sale price per animal');
            $table->decimal('total_value', 18, 2)->nullable()->comment('Total monetary value of event');
            $table->string('source_or_destination')->nullable()->comment('Supplier, buyer, or other farm name');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_events');
    }
};