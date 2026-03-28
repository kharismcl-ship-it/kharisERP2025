<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_agronomist_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('farm_agronomist_id')->constrained('farm_agronomists')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->date('visit_date');
            $table->enum('visit_type', ['routine', 'problem_diagnosis', 'compliance_audit', 'training', 'other']);
            $table->text('observations')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index('company_id');
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_agronomist_visits');
    }
};