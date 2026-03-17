<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_restock_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('farm_produce_inventory_id')->constrained('farm_produce_inventories')->cascadeOnDelete();
            $table->foreignId('shop_customer_id')->nullable()->constrained('shop_customers')->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index(['farm_produce_inventory_id', 'notified_at'], 'restock_product_notified_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_restock_notifications');
    }
};
