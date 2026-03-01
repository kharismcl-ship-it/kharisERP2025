<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_opportunity_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('opportunity_id')->index();
            $table->unsignedBigInteger('catalog_item_id')->index();
            $table->decimal('quantity', 10, 3)->default(1);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_opportunity_items');
    }
};