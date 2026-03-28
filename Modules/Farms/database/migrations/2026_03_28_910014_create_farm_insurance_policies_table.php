<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->string('policy_number')->unique();
            $table->string('insurer_name');
            $table->enum('insurance_type', ['weather_index', 'multi_peril_crop', 'livestock', 'property'])->default('weather_index');
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->foreignId('livestock_batch_id')->nullable()->constrained('livestock_batches')->nullOnDelete();
            $table->string('covered_crop')->nullable();
            $table->decimal('covered_area_ha', 8, 4)->nullable();
            $table->decimal('sum_insured', 12, 2);
            $table->decimal('premium_amount', 12, 2);
            $table->date('premium_paid_date')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('trigger_description')->nullable();
            $table->enum('status', ['active', 'expired', 'claimed', 'cancelled'])->default('active');
            $table->decimal('claim_amount', 12, 2)->nullable();
            $table->date('claim_date')->nullable();
            $table->enum('claim_status', ['pending', 'approved', 'rejected', 'paid'])->nullable();
            $table->text('claim_notes')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_insurance_policies');
    }
};