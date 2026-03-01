<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dining_order_id')->index();
            $table->unsignedBigInteger('catalog_item_id')->index();
            $table->decimal('quantity', 10, 3)->default(1);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, in_prep, ready, served, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_order_items');
    }
};