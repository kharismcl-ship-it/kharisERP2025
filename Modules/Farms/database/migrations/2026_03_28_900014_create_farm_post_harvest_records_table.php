<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_post_harvest_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('harvest_record_id');
            $table->unsignedBigInteger('farm_storage_location_id')->nullable();
            $table->unsignedBigInteger('farm_produce_lot_id')->nullable();
            $table->enum('record_type', ['grading', 'storage_in', 'storage_out', 'loss_record', 'treatment', 'quality_test']);
            $table->decimal('grade_a_qty', 10, 2)->nullable();
            $table->decimal('grade_b_qty', 10, 2)->nullable();
            $table->decimal('grade_c_qty', 10, 2)->nullable();
            $table->decimal('reject_qty', 10, 2)->nullable();
            $table->decimal('total_loss_qty', 10, 2)->nullable();
            $table->enum('loss_cause', ['spoilage', 'pest_damage', 'moisture', 'mechanical', 'theft', 'unknown'])->nullable();
            $table->string('treatment_type')->nullable();
            $table->string('quality_test_type')->nullable();
            $table->string('quality_test_result')->nullable();
            $table->boolean('quality_test_passed')->nullable();
            $table->unsignedBigInteger('recorded_by_worker_id')->nullable();
            $table->text('notes')->nullable();
            $table->date('record_date');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('harvest_record_id')->references('id')->on('harvest_records')->onDelete('cascade');
            $table->foreign('farm_storage_location_id')->references('id')->on('farm_storage_locations')->onDelete('set null');
            $table->foreign('farm_produce_lot_id')->references('id')->on('farm_produce_lots')->onDelete('set null');
            $table->foreign('recorded_by_worker_id')->references('id')->on('farm_workers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_post_harvest_records');
    }
};