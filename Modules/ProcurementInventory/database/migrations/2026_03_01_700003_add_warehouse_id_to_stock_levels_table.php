<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_levels', function (Blueprint $table) {
            // Drop old company+item unique so we can scope per warehouse
            $table->dropUnique(['company_id', 'item_id']);

            $table->foreignId('warehouse_id')
                ->nullable()
                ->after('item_id')
                ->constrained('warehouses')
                ->nullOnDelete();

            // New unique: one row per company + item + warehouse (null = company-wide)
            $table->unique(['company_id', 'item_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'item_id', 'warehouse_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
            $table->unique(['company_id', 'item_id']);
        });
    }
};