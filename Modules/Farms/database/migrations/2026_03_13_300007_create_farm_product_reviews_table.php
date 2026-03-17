<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_produce_inventory_id')->constrained('farm_produce_inventories')->cascadeOnDelete();
            $table->foreignId('shop_customer_id')->nullable()->constrained('shop_customers')->nullOnDelete();
            $table->foreignId('farm_order_id')->nullable()->constrained('farm_orders')->nullOnDelete();
            $table->tinyInteger('rating'); // 1–5
            $table->text('review_text')->nullable();
            $table->string('reviewer_name', 100);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            // One review per customer per product
            $table->unique(['farm_produce_inventory_id', 'shop_customer_id'], 'unique_customer_product_review');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_product_reviews');
    }
};
