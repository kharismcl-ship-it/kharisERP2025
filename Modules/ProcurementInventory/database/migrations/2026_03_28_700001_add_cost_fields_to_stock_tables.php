<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 4)->default(0)->after('quantity_after');
            $table->decimal('total_cost', 15, 4)->default(0)->after('unit_cost');
        });

        Schema::table('stock_levels', function (Blueprint $table) {
            $table->decimal('average_unit_cost', 15, 4)->default(0)->after('last_counted_at');
            $table->decimal('total_value', 15, 4)->default(0)->after('average_unit_cost');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['unit_cost', 'total_cost']);
        });

        Schema::table('stock_levels', function (Blueprint $table) {
            $table->dropColumn(['average_unit_cost', 'total_value']);
        });
    }
};