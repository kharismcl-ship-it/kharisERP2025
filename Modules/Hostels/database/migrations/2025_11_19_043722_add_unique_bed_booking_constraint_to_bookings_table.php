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
        Schema::table('bookings', function (Blueprint $table) {
            // We cannot create a partial unique index in MySQL
            // Instead, we'll handle this constraint at the application level
            // Add a comment to document the business rule (only for MySQL)
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE bookings COMMENT = 'Business rule: Only one active booking per bed allowed (not cancelled or checked_out)'");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE bookings COMMENT = ''");
            }
        });
    }
};
