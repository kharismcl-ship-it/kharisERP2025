<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_orders', function (Blueprint $table) {
            $table->integer('loyalty_points_redeemed')->default(0)->after('discount_amount');
            $table->decimal('loyalty_discount', 12, 2)->default(0)->after('loyalty_points_redeemed');
            $table->integer('loyalty_points_earned')->default(0)->after('loyalty_discount');
        });
    }

    public function down(): void
    {
        Schema::table('farm_orders', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points_redeemed', 'loyalty_discount', 'loyalty_points_earned']);
        });
    }
};
