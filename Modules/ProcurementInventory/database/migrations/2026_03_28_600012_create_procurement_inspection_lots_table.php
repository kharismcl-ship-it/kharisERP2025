<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_inspection_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->string('lot_number')->unique();
            $table->date('inspection_date')->nullable();
            $table->foreignId('inspected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'passed', 'failed', 'conditionally_passed'])->default('pending');
            $table->enum('overall_result', ['accept', 'reject', 'conditional_accept'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_inspection_lot_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('procurement_inspection_lots')->cascadeOnDelete();
            $table->foreignId('goods_receipt_line_id')->constrained('goods_receipt_lines')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->decimal('quantity_inspected', 15, 4);
            $table->decimal('quantity_passed', 15, 4);
            $table->decimal('quantity_failed', 15, 4)->default(0);
            $table->enum('defect_type', [
                'damaged',
                'wrong_spec',
                'short_supply',
                'expired',
                'contaminated',
                'other',
            ])->nullable();
            $table->text('defect_description')->nullable();
            $table->enum('disposition', ['accept', 'reject', 'quarantine', 'return_to_vendor'])->default('accept');
            $table->timestamps();
        });

        Schema::create('procurement_rtv_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->string('rtv_number')->unique();
            $table->date('return_date');
            $table->text('reason');
            $table->enum('status', ['draft', 'submitted', 'completed', 'cancelled'])->default('draft');
            $table->boolean('debit_note_raised')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_rtv_orders');
        Schema::dropIfExists('procurement_inspection_lot_lines');
        Schema::dropIfExists('procurement_inspection_lots');
    }
};