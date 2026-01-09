<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hostel_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->string('name');
            $table->string('category'); // linen, furniture, equipment, consumables
            $table->text('description')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->integer('max_stock_level')->nullable();
            $table->string('uom')->default('pcs'); // unit of measure
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['hostel_id', 'category']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_inventory_items');
    }
};
