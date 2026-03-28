<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_vendor_performance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->smallInteger('days_late')->default(0);
            $table->decimal('quantity_ordered', 15, 4);
            $table->decimal('quantity_received', 15, 4);
            $table->decimal('quantity_rejected', 15, 4)->default(0);
            $table->decimal('quality_rate', 5, 2);
            $table->decimal('po_unit_price', 15, 4);
            $table->decimal('grn_unit_price', 15, 4);
            $table->decimal('price_variance_pct', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('procurement_vendor_scorecards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->smallInteger('period_year');
            $table->tinyInteger('period_month');
            $table->unsignedSmallInteger('total_orders')->default(0);
            $table->decimal('on_time_rate', 5, 2)->default(0);
            $table->decimal('avg_quality_rate', 5, 2)->default(0);
            $table->decimal('avg_price_variance_pct', 5, 2)->default(0);
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['company_id', 'vendor_id', 'period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_vendor_scorecards');
        Schema::dropIfExists('procurement_vendor_performance_records');
    }
};