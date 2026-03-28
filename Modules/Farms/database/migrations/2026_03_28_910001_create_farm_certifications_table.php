<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_certifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->string('certification_type'); // GlobalGAP, Organic, Fairtrade, etc.
            $table->string('certifying_body')->nullable();
            $table->string('certificate_number')->nullable();
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended', 'pending_renewal', 'under_audit'])->default('pending_renewal');
            $table->text('scope')->nullable();
            $table->string('document_path')->nullable();
            $table->unsignedSmallInteger('renewal_reminder_days')->default(60);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index(['farm_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_certifications');
    }
};