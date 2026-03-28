<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_trial_observations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_trial_id');
            $table->unsignedBigInteger('farm_trial_plot_id')->nullable();
            $table->date('observation_date');
            $table->string('observation_type');
            $table->string('value')->nullable();
            $table->string('unit')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->foreign('farm_trial_id')->references('id')->on('farm_trials')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_trial_observations');
    }
};