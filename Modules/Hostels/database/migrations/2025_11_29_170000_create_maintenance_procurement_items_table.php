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
        Schema::create('maintenance_procurement_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_record_id');
            $table->unsignedBigInteger('procurement_item_id');
            $table->integer('quantity_used')->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraints - procurement_item_id constraint will be added later
            $table->foreign('maintenance_record_id')
                ->references('id')->on('inventory_maintenance_records')
                ->onDelete('cascade');

            // Indexes with shorter names
            $table->index(['maintenance_record_id', 'procurement_item_id'], 'mt_proc_items_mr_pi_index');
            $table->index('procurement_item_id', 'mt_proc_items_pi_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_procurement_items');
    }
};
