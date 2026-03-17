<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_orders', function (Blueprint $table) {
            $table->boolean('is_b2b')->default(false)->after('notes');
            $table->unsignedBigInteger('b2b_account_id')->nullable()->after('is_b2b');
            $table->string('po_number', 100)->nullable()->after('b2b_account_id')
                ->comment('Customer Purchase Order reference');
            $table->string('payment_terms')->nullable()->after('po_number')
                ->comment('prepay|net7|net14|net30 — for B2B credit-term orders');
            $table->decimal('b2b_discount_amount', 10, 2)->default(0.00)->after('payment_terms');
        });
    }

    public function down(): void
    {
        Schema::table('farm_orders', function (Blueprint $table) {
            $table->dropColumn(['is_b2b', 'b2b_account_id', 'po_number', 'payment_terms', 'b2b_discount_amount']);
        });
    }
};
