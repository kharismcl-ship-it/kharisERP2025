<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('shop_customer_id')->constrained('shop_customers')->cascadeOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->json('cart_data');
            $table->decimal('cart_total', 12, 2)->default(0);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->unique('shop_customer_id'); // one record per customer
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_abandoned_carts');
    }
};
