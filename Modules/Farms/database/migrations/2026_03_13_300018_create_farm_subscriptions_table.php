<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('shop_customer_id')->constrained('shop_customers')->cascadeOnDelete();
            $table->string('frequency')->default('weekly'); // weekly|biweekly|monthly
            $table->string('status')->default('active');    // active|paused|cancelled
            $table->json('items');  // [{inventory_id, product_name, unit, quantity, unit_price}]
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->string('delivery_type')->default('pickup'); // pickup|delivery
            $table->text('delivery_address')->nullable();
            $table->string('delivery_landmark')->nullable();
            $table->text('notes')->nullable();
            $table->date('next_order_date');
            $table->date('last_order_date')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_subscriptions');
    }
};
