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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id');
            $table->foreignId('room_id');
            $table->foreignId('bed_id')->nullable();
            $table->foreignId('hostel_occupant_id');
            $table->string('booking_reference')->unique();
            $table->enum('booking_type', ['academic', 'short_stay', 'semester']);
            $table->string('academic_year')->nullable();
            $table->string('semester')->nullable();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->dateTime('actual_check_in_at')->nullable();
            $table->dateTime('actual_check_out_at')->nullable();
            $table->enum('status', ['pending', 'awaiting_payment', 'confirmed', 'checked_in', 'checked_out', 'no_show', 'cancelled']);
            $table->decimal('total_amount');
            $table->decimal('amount_paid')->default(0);
            $table->decimal('balance_amount');
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid', 'overpaid']);
            $table->enum('channel', ['walk_in', 'online', 'agent']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
