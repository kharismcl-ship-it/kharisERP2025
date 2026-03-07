<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requisition_items', function (Blueprint $table) {
            $table->decimal('fulfilled_quantity', 10, 3)->default(0)->after('quantity');
            $table->string('vendor_name')->nullable()->after('notes');
            $table->string('vendor_quote_ref')->nullable()->after('vendor_name');
            $table->decimal('vendor_unit_price', 12, 2)->nullable()->after('vendor_quote_ref');
        });
    }

    public function down(): void
    {
        Schema::table('requisition_items', function (Blueprint $table) {
            $table->dropColumn(['fulfilled_quantity', 'vendor_name', 'vendor_quote_ref', 'vendor_unit_price']);
        });
    }
};