<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_asns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('vendor_contact_id')->nullable()->constrained('vendor_contacts')->nullOnDelete();
            $table->string('asn_number')->unique();
            $table->date('expected_delivery_date');
            $table->string('carrier_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['submitted', 'acknowledged', 'received'])->default('submitted');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('procurement_asn_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asn_id')->constrained('procurement_asns')->cascadeOnDelete();
            $table->foreignId('purchase_order_line_id')->constrained('purchase_order_lines')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->decimal('quantity_shipped', 15, 4);
            $table->string('lot_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_asn_lines');
        Schema::dropIfExists('procurement_asns');
    }
};