<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('construction_project_id')->constrained('construction_projects')->cascadeOnDelete();
            $table->unsignedBigInteger('contractor_id');
            $table->foreignId('project_phase_id')->nullable()->constrained('project_phases')->nullOnDelete();
            $table->enum('request_type', ['materials', 'funds', 'labour', 'equipment', 'support', 'other'])->default('materials');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'fulfilled'])->default('pending');
            $table->decimal('requested_amount', 15, 2)->nullable();
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->date('required_by')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->unsignedBigInteger('procurement_po_id')->nullable();
            $table->unsignedBigInteger('finance_invoice_id')->nullable();
            $table->timestamps();

            $table->foreign('contractor_id')->references('id')->on('contractors')->cascadeOnDelete();
            $table->foreign('procurement_po_id')->references('id')->on('purchase_orders')->nullOnDelete();
            $table->foreign('finance_invoice_id')->references('id')->on('invoices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_requests');
    }
};
