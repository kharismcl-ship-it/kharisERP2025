<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_shop_settings', function (Blueprint $table) {
            $table->boolean('loyalty_enabled')->default(false)->after('footer_about_text');
            $table->decimal('loyalty_points_per_ghs', 8, 2)->default(1.00)->after('loyalty_enabled');
            $table->decimal('loyalty_points_value_ghs', 8, 4)->default(0.0100)->after('loyalty_points_per_ghs');
            // e.g. 1 point = GHS 0.01 — 100 points = GHS 1.00
        });
    }

    public function down(): void
    {
        Schema::table('farm_shop_settings', function (Blueprint $table) {
            $table->dropColumn(['loyalty_enabled', 'loyalty_points_per_ghs', 'loyalty_points_value_ghs']);
        });
    }
};
