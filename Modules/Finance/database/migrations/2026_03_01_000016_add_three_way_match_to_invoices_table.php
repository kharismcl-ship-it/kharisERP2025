<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Three-way match: PO → GRN → Invoice
            $table->unsignedBigInteger('purchase_order_id')->nullable()->after('vendor_id');
            $table->unsignedBigInteger('grn_id')->nullable()->after('purchase_order_id');
            $table->enum('match_status', [
                'not_applicable', // e.g. customer invoices
                'pending',        // PO/GRN not yet linked
                'matched',        // quantities and prices align
                'price_variance', // unit price differs from PO
                'qty_variance',   // received qty differs from invoiced qty
                'exception',      // manual override / escalation
            ])->default('not_applicable')->after('status');

            $table->decimal('match_variance_amount', 15, 2)->nullable()->after('match_status');
            $table->text('match_notes')->nullable()->after('match_variance_amount');

            $table->index('purchase_order_id');
            $table->index('match_status');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['purchase_order_id']);
            $table->dropIndex(['match_status']);
            $table->dropColumn([
                'purchase_order_id',
                'grn_id',
                'match_status',
                'match_variance_amount',
                'match_notes',
            ]);
        });
    }
};
