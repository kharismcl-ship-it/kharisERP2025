<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_stock_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->string('lot_number');
            $table->string('batch_number')->nullable();
            $table->decimal('quantity_received', 15, 4);
            $table->decimal('quantity_on_hand', 15, 4);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('goods_receipt_id')->nullable()->constrained('goods_receipts')->nullOnDelete();
            $table->foreignId('goods_receipt_line_id')->nullable()->constrained('goods_receipt_lines')->nullOnDelete();
            $table->enum('status', ['available', 'quarantine', 'consumed', 'expired'])->default('available');
            $table->timestamps();

            $table->unique(['company_id', 'item_id', 'lot_number']);
        });

        Schema::create('procurement_serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('serial_number');
            $table->foreignId('lot_id')->nullable()->constrained('procurement_stock_lots')->nullOnDelete();
            $table->enum('status', ['in_stock', 'issued', 'returned', 'scrapped'])->default('in_stock');
            $table->foreignId('goods_receipt_id')->nullable()->constrained('goods_receipts')->nullOnDelete();
            $table->string('issued_to')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'item_id', 'serial_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_serial_numbers');
        Schema::dropIfExists('procurement_stock_lots');
    }
};