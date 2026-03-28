<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->timestamp('vendor_acknowledged_at')->nullable()->after('received_at');
            $table->date('vendor_confirmed_delivery_date')->nullable()->after('vendor_acknowledged_at');
            $table->text('vendor_delivery_notes')->nullable()->after('vendor_confirmed_delivery_date');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['vendor_acknowledged_at', 'vendor_confirmed_delivery_date', 'vendor_delivery_notes']);
        });
    }
};