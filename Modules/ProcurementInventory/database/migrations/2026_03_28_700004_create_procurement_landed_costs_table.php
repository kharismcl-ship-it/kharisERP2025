<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_landed_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->string('reference')->nullable();
            $table->decimal('total_freight', 15, 2)->default(0);
            $table->decimal('total_duty', 15, 2)->default(0);
            $table->decimal('total_insurance', 15, 2)->default(0);
            $table->decimal('total_customs_fee', 15, 2)->default(0);
            $table->decimal('total_other', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->enum('allocation_method', ['by_value', 'by_quantity', 'by_weight'])->default('by_value');
            $table->enum('status', ['draft', 'allocated'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_landed_cost_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landed_cost_id')->constrained('procurement_landed_costs')->cascadeOnDelete();
            $table->foreignId('goods_receipt_line_id')->constrained('goods_receipt_lines')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->decimal('allocated_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_landed_cost_lines');
        Schema::dropIfExists('procurement_landed_costs');
    }
};