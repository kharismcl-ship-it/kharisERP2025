<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'receipt',       // goods received from a PO
                'adjustment',    // manual stock adjustment
                'issue',         // stock issued / consumed
                'transfer',      // inter-location transfer
                'return',        // returned to vendor or from production
                'opening',       // opening balance entry
            ]);
            $table->decimal('quantity', 14, 4);          // positive or negative
            $table->decimal('quantity_before', 14, 4);   // on-hand before this movement
            $table->decimal('quantity_after', 14, 4);    // on-hand after this movement
            $table->string('reference')->nullable();      // PO number / GRN number / etc.
            $table->nullableMorphs('source');             // polymorphic link to GoodsReceipt etc.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'item_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
