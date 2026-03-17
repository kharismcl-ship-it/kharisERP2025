<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_order_id')->constrained('farm_orders')->cascadeOnDelete();
            $table->foreignId('farm_produce_inventory_id')->constrained('farm_produce_inventories')->cascadeOnDelete();
            $table->string('product_name');   // snapshot at time of order
            $table->string('unit');           // snapshot
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_order_items');
    }
};
