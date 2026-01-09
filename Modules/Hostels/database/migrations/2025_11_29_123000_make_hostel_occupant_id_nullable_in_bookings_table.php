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
            // Make hostel_occupant_id nullable to allow bookings for new occupants who don't exist yet
            $table->foreignId('hostel_occupant_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert back to non-nullable
            $table->foreignId('hostel_occupant_id')->nullable(false)->change();
        });
    }
};
