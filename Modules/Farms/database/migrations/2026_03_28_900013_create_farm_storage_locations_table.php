<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_storage_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->string('name');
            $table->enum('type', ['silo', 'cold_room', 'warehouse', 'outdoor_stack', 'other']);
            $table->decimal('capacity_tonnes', 8, 2)->nullable();
            $table->decimal('current_stock_tonnes', 8, 2)->default(0);
            $table->decimal('temperature_c', 5, 2)->nullable();
            $table->decimal('humidity_pct', 5, 2)->nullable();
            $table->dateTime('last_checked_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_storage_locations');
    }
};