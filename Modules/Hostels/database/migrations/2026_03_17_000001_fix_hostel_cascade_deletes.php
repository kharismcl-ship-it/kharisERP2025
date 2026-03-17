<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Several hostel tables were created with hostel_id / hostel_block_id foreign keys
 * that had no ON DELETE CASCADE. When a company is deleted the cascade from
 * companies → hostels was blocked by these dangling FK constraints.
 *
 * This migration drops and re-creates those FKs with cascadeOnDelete().
 * Tables already using cascadeOnDelete() are left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        // hostel_blocks.hostel_id  (constrained, no cascade)
        Schema::table('hostel_blocks', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
            $table->foreign('hostel_id')->references('id')->on('hostels')->cascadeOnDelete();
        });

        // hostel_floors.hostel_id + hostel_block_id  (both constrained, no cascade)
        Schema::table('hostel_floors', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
            $table->dropForeign(['hostel_block_id']);
            $table->foreign('hostel_id')->references('id')->on('hostels')->cascadeOnDelete();
            $table->foreign('hostel_block_id')->references('id')->on('hostel_blocks')->cascadeOnDelete();
        });

        // hostel_occupants.hostel_id  (no FK at all — just bare foreignId)
        Schema::table('hostel_occupants', function (Blueprint $table) {
            $table->foreign('hostel_id')->references('id')->on('hostels')->cascadeOnDelete();
        });

        // bookings.hostel_id  (no FK at all — just bare foreignId)
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('hostel_id')->references('id')->on('hostels')->cascadeOnDelete();
        });

        // hostel_charges.hostel_id  (no FK at all — just bare foreignId)
        Schema::table('hostel_charges', function (Blueprint $table) {
            $table->foreign('hostel_id')->references('id')->on('hostels')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hostel_charges', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
        });

        Schema::table('hostel_occupants', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
        });

        Schema::table('hostel_floors', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
            $table->dropForeign(['hostel_block_id']);
            $table->foreign('hostel_id')->references('id')->on('hostels');
            $table->foreign('hostel_block_id')->references('id')->on('hostel_blocks');
        });

        Schema::table('hostel_blocks', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
            $table->foreign('hostel_id')->references('id')->on('hostels');
        });
    }
};