<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_invoice_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->unsignedBigInteger('finance_invoice_id')->nullable();
            $table->foreign('finance_invoice_id')->references('id')->on('fin_invoices')->nullOnDelete();
            $table->decimal('po_total', 15, 2);
            $table->decimal('grn_total', 15, 2);
            $table->decimal('invoice_total', 15, 2)->nullable();
            $table->decimal('po_grn_variance', 15, 2);
            $table->decimal('grn_invoice_variance', 15, 2)->nullable();
            $table->decimal('tolerance_percent', 5, 2)->default(2.00);
            $table->enum('status', ['matched', 'po_grn_mismatch', 'grn_invoice_mismatch', 'pending_invoice'])->default('pending_invoice');
            $table->text('notes')->nullable();
            $table->timestamp('matched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_invoice_matches');
    }
};