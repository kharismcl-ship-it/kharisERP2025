<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('shop_customer_id')->constrained('shop_customers')->cascadeOnDelete();
            $table->foreignId('farm_order_id')->nullable()->constrained('farm_orders')->nullOnDelete();
            $table->integer('points');          // positive = earned, negative = redeemed
            $table->string('type');             // earn | redeem | adjustment
            $table->integer('balance_after');   // running balance snapshot
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            // No updated_at — immutable ledger

            $table->index(['shop_customer_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_loyalty_points');
    }
};
