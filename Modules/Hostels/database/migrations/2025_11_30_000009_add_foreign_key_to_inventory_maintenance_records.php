<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_maintenance_records', function (Blueprint $table) {
            $table->foreign('inventory_item_id')
                ->references('id')
                ->on('hostel_inventory_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_maintenance_records', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
        });
    }
};
