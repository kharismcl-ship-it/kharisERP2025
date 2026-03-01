<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_levels', function (Blueprint $table) {
            // Add a plain index first so any FK constraints on these columns
            // still have an index to reference when we drop the unique below.
            $table->index(['company_id', 'item_id'], 'stock_levels_co_item_idx');

            // Now safe to drop the old unique
            $table->dropUnique(['company_id', 'item_id']);

            if (! Schema::hasColumn('stock_levels', 'warehouse_id')) {
                $table->foreignId('warehouse_id')
                    ->nullable()
                    ->after('item_id')
                    ->constrained('warehouses')
                    ->nullOnDelete();
            }

            // New unique: one row per company + item + warehouse (null = company-wide)
            $table->unique(['company_id', 'item_id', 'warehouse_id']);

            // Drop the temporary plain index — superseded by the new unique above
            $table->dropIndex('stock_levels_co_item_idx');
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