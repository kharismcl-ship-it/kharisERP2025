<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_orders', function (Blueprint $table) {
            $table->string('delivery_landmark')->nullable()->after('delivery_address');
            $table->unsignedBigInteger('shop_customer_id')->nullable()->after('company_id');
            $table->foreign('shop_customer_id')->references('id')->on('shop_customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('farm_orders', function (Blueprint $table) {
            $table->dropForeign(['shop_customer_id']);
            $table->dropColumn(['delivery_landmark', 'shop_customer_id']);
        });
    }
};
