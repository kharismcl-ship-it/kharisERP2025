<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_book_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_book_order_id')->constrained('hostel_book_orders')->cascadeOnDelete();
            $table->foreignId('hostel_book_id')->constrained('hostel_books')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_book_order_items');
    }
};
