<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id')->index();
            $table->unsignedBigInteger('waiter_id')->nullable()->index(); // User FK
            $table->string('status')->default('open'); // open, in_kitchen, ready, served, paid, cancelled
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->unsignedBigInteger('invoice_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('sent_to_kitchen_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_orders');
    }
};