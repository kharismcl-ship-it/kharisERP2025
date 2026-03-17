<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_customer_wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_customer_id')->constrained('shop_customers')->cascadeOnDelete();
            $table->foreignId('farm_produce_inventory_id')->constrained('farm_produce_inventories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['shop_customer_id', 'farm_produce_inventory_id'], 'wishlist_customer_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_customer_wishlists');
    }
};
