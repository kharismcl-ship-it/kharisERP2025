<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_price_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_list_id')->index();
            $table->unsignedBigInteger('catalog_item_id')->index();
            $table->decimal('override_price', 15, 4);
            $table->decimal('min_quantity', 10, 3)->default(1);
            $table->timestamps();

            $table->unique(['price_list_id', 'catalog_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_price_list_items');
    }
};