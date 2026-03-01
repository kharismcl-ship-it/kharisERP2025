<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links production batches to a ProcurementInventory Item
 * representing the primary raw material input (e.g. wood pulp, recycled fibre).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mp_production_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')
                ->nullable()
                ->after('paper_grade_id')
                ->comment('Primary raw material item from ProcurementInventory');

            if (Schema::hasTable('items')) {
                $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('mp_production_batches', function (Blueprint $table) {
            if (Schema::hasTable('items')) {
                $table->dropForeign(['item_id']);
            }
            $table->dropColumn('item_id');
        });
    }
};
