<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_trials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->string('trial_name');
            $table->enum('trial_type', ['variety_comparison', 'input_comparison', 'practice_comparison', 'other'])->default('variety_comparison');
            $table->text('hypothesis')->nullable();
            $table->text('objective')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('crop_season_id')->nullable();
            $table->string('crop_name')->nullable();
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');
            $table->text('methodology')->nullable();
            $table->text('conclusion')->nullable();
            $table->string('conducted_by')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_trials');
    }
};