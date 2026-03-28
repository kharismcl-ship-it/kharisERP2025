<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('farm_produce_lot_id')->nullable()->after('id');
            $table->foreign('farm_produce_lot_id')->references('id')->on('farm_produce_lots')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('farm_order_items', function (Blueprint $table) {
            $table->dropForeign(['farm_produce_lot_id']);
            $table->dropColumn('farm_produce_lot_id');
        });
    }
};