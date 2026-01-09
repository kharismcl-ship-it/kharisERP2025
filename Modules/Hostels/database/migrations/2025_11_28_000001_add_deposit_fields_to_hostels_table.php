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
        Schema::table('hostels', function (Blueprint $table) {
            // Deposit configuration fields
            $table->boolean('require_deposit')->default(false)->after('reservation_hold_minutes');
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('require_deposit');
            $table->decimal('deposit_percentage', 5, 2)->nullable()->after('deposit_amount');
            $table->string('deposit_type')->default('fixed')->after('deposit_percentage'); // fixed, percentage
            $table->text('deposit_refund_policy')->nullable()->after('deposit_type');
            $table->boolean('allow_partial_payments')->default(false)->after('deposit_refund_policy');
            $table->integer('partial_payment_min_percentage')->default(0)->after('allow_partial_payments');

            // Indexes for performance
            $table->index('require_deposit');
            $table->index('deposit_type');
            $table->index('allow_partial_payments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn([
                'require_deposit',
                'deposit_amount',
                'deposit_percentage',
                'deposit_type',
                'deposit_refund_policy',
                'allow_partial_payments',
                'partial_payment_min_percentage',
            ]);

            $table->dropIndex(['require_deposit']);
            $table->dropIndex(['deposit_type']);
            $table->dropIndex(['allow_partial_payments']);
        });
    }
};
