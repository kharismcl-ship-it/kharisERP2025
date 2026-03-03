<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('site_monitor_id')->constrained('site_monitors')->cascadeOnDelete();
            $table->foreignId('construction_project_id')->constrained('construction_projects')->cascadeOnDelete();
            $table->foreignId('project_phase_id')->nullable()->constrained('project_phases')->nullOnDelete();
            $table->unsignedBigInteger('contractor_id')->nullable();
            $table->date('visit_date');
            $table->date('report_date');
            $table->text('findings');
            $table->text('recommendations')->nullable();
            $table->unsignedTinyInteger('compliance_score')->nullable();
            $table->string('weather_conditions')->nullable();
            $table->unsignedInteger('workers_on_site')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'actioned'])->default('draft');
            $table->json('attachment_paths')->nullable();
            $table->timestamps();

            $table->foreign('contractor_id')->references('id')->on('contractors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_reports');
    }
};
