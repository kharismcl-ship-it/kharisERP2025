<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_produce_inventories', function (Blueprint $table) {
            // Selling price for the public marketplace (separate from cost price)
            $table->decimal('unit_price', 12, 2)->nullable()->after('unit_cost');
            // Optional product description for the shop listing
            $table->text('description')->nullable()->after('notes');
            // Whether this item is listed on the public marketplace
            $table->boolean('marketplace_listed')->default(false)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('farm_produce_inventories', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'description', 'marketplace_listed']);
        });
    }
};
