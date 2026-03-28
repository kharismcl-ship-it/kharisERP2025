<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_rfqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('requisition_id')->constrained('requisitions')->cascadeOnDelete();
            $table->string('rfq_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['draft', 'sent', 'evaluating', 'awarded', 'cancelled'])->default('draft');
            $table->foreignId('awarded_vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->text('award_justification')->nullable();
            $table->timestamp('awarded_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('requisition_rfq_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_id')->constrained('requisition_rfqs')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('vendor_contact_name')->nullable();
            $table->decimal('quoted_amount', 15, 2);
            $table->unsignedSmallInteger('delivery_days')->nullable();
            $table->string('payment_terms')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['received', 'shortlisted', 'rejected', 'awarded'])->default('received');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_rfq_bids');
        Schema::dropIfExists('requisition_rfqs');
    }
};