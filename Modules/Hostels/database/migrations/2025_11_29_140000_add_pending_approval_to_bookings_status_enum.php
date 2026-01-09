<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use raw SQL to modify the enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'pending_approval', 'awaiting_payment', 'confirmed', 'checked_in', 'checked_out', 'no_show', 'cancelled') NOT NULL DEFAULT 'pending'");
        } else {
            // For other databases, we can use Schema::table
            Schema::table('bookings', function (Blueprint $table) {
                $table->enum('status', ['pending', 'pending_approval', 'awaiting_payment', 'confirmed', 'checked_in', 'checked_out', 'no_show', 'cancelled'])->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'awaiting_payment', 'confirmed', 'checked_in', 'checked_out', 'no_show', 'cancelled') NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('bookings', function (Blueprint $table) {
                $table->enum('status', ['pending', 'awaiting_payment', 'confirmed', 'checked_in', 'checked_out', 'no_show', 'cancelled'])->default('pending')->change();
            });
        }
    }
};
