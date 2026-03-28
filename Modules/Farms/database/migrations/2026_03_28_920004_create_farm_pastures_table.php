<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_pastures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->string('pasture_name');
            $table->string('pasture_type')->nullable();
            $table->decimal('area_ha', 8, 4)->nullable();
            $table->decimal('current_foo_kg_ha', 8, 2)->nullable();
            $table->decimal('target_foo_kg_ha', 8, 2)->nullable();
            $table->decimal('carrying_capacity_au_ha', 8, 4)->nullable();
            $table->boolean('is_occupied')->default(false);
            $table->unsignedBigInteger('current_batch_id')->nullable();
            $table->date('last_grazed_date')->nullable();
            $table->date('available_from_date')->nullable();
            $table->integer('rest_days_required')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_pastures');
    }
};