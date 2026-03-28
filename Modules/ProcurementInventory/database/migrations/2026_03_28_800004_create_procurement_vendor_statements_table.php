<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_vendor_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('statement_reference')->nullable();
            $table->date('statement_date');
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->decimal('total_invoiced', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->enum('status', ['uploaded', 'reconciled', 'disputed'])->default('uploaded');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_vendor_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statement_id')
                ->constrained('procurement_vendor_statements')
                ->cascadeOnDelete();
            $table->date('transaction_date');
            $table->enum('transaction_type', ['invoice', 'payment', 'credit', 'debit_note', 'opening']);
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('matched_po_id')->nullable();
            $table->foreign('matched_po_id')->references('id')->on('purchase_orders')->nullOnDelete();
            $table->enum('match_status', ['matched', 'unmatched', 'disputed'])->default('unmatched');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_vendor_statement_lines');
        Schema::dropIfExists('procurement_vendor_statements');
    }
};