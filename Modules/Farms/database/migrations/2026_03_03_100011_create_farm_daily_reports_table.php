<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_worker_id')->constrained('farm_workers')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id')->index();
            $table->date('report_date');
            $table->text('summary');
            $table->text('activities_done');
            $table->text('issues_noted')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('weather_observation')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_daily_reports');
    }
};
