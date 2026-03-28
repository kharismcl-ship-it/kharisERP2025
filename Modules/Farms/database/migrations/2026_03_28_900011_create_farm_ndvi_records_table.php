<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_ndvi_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('farm_plot_id')->nullable();
            $table->date('recorded_date');
            $table->decimal('ndvi_value', 5, 4);
            $table->decimal('ndvi_min', 5, 4)->nullable();
            $table->decimal('ndvi_max', 5, 4)->nullable();
            $table->enum('source', ['sentinel2', 'planet', 'manual', 'drone'])->default('manual');
            $table->decimal('cloud_cover_pct', 5, 2)->nullable();
            $table->boolean('stress_detected')->default(false);
            $table->boolean('alert_sent')->default(false);
            $table->json('zone_data')->nullable();
            $table->string('image_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('farm_plot_id')->references('id')->on('farm_plots')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_ndvi_records');
    }
};