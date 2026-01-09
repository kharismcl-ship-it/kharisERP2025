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
        // Add fields to hostels table for payment policy
        Schema::table('hostels', function (Blueprint $table) {
            $table->boolean('require_payment_before_checkin')->default(false)->after('status');
            $table->integer('reservation_hold_minutes')->default(30)->after('require_payment_before_checkin');
        });

        // Add fields to bookings table for hold expiry and terms acceptance
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('hold_expires_at')->nullable()->after('payment_status');
            $table->timestamp('accepted_terms_at')->nullable()->after('hold_expires_at');
            $table->index(['hold_expires_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn(['require_payment_before_checkin', 'reservation_hold_minutes']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['hold_expires_at', 'accepted_terms_at']);
            $table->dropIndex(['hold_expires_at', 'status']);
        });
    }
};
