<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_grazing_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_pasture_id');
            $table->unsignedBigInteger('livestock_batch_id');
            $table->enum('event_type', ['move_in', 'move_out', 'foo_measurement', 'rotation_plan']);
            $table->date('event_date');
            $table->decimal('foo_kg_ha', 8, 2)->nullable();
            $table->decimal('stock_density', 8, 4)->nullable();
            $table->integer('days_in_paddock')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_pasture_id')->references('id')->on('farm_pastures')->onDelete('cascade');
            $table->foreign('livestock_batch_id')->references('id')->on('livestock_batches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_grazing_events');
    }
};