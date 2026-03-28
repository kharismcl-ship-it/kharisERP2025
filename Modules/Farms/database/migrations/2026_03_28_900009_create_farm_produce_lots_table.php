<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_produce_lots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('harvest_record_id')->nullable();
            $table->string('lot_number');
            $table->unsignedBigInteger('produce_inventory_id')->nullable();
            $table->decimal('quantity_kg', 10, 2);
            $table->string('unit')->default('kg');
            $table->date('harvest_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('storage_location')->nullable();
            $table->enum('quality_grade', ['A', 'B', 'C', 'ungraded'])->default('ungraded');
            $table->decimal('moisture_content_pct', 5, 2)->nullable();
            $table->decimal('aflatoxin_ppb', 8, 4)->nullable();
            $table->boolean('is_recalled')->default(false);
            $table->text('recall_reason')->nullable();
            $table->string('qr_code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'lot_number']);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('harvest_record_id')->references('id')->on('harvest_records')->onDelete('set null');
            $table->foreign('produce_inventory_id')->references('id')->on('farm_produce_inventories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_produce_lots');
    }
};