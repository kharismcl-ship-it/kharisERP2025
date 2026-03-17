<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_produce_inventories', function (Blueprint $table) {
            $table->decimal('min_order_quantity', 8, 3)->default(0)->after('current_stock');
        });
    }

    public function down(): void
    {
        Schema::table('farm_produce_inventories', function (Blueprint $table) {
            $table->dropColumn('min_order_quantity');
        });
    }
};
