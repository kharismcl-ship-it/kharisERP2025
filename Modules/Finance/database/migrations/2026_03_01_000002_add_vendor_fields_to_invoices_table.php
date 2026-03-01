<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // 'customer' = AR invoice, 'vendor' = AP/payable invoice
            $table->string('type')->default('customer')->after('company_id');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('type');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'vendor_id']);
        });
    }
};
