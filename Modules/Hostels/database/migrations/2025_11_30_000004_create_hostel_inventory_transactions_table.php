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
        if (Schema::hasTable('hostel_inventory_transactions')) {
            return;
        }

        Schema::create('hostel_inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('hostel_inventory_items')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('transaction_type', ['receipt', 'issue', 'transfer', 'adjustment', 'consumption']);
            $table->integer('quantity');
            $table->integer('balance_after');
            $table->text('notes')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamp('transaction_date');
            $table->timestamps();

            $table->index(['hostel_id', 'transaction_date'], 'hit_hostel_trans_date_idx');
            $table->index(['inventory_item_id', 'transaction_date'], 'hit_item_trans_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_inventory_transactions');
    }
};
