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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_occupant_id')->constrained('hostel_occupants')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('cascade');
            $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');

            $table->decimal('amount', 10, 2);
            $table->string('deposit_type')->default('security'); // security, reservation, etc.
            $table->string('status')->default('pending'); // pending, collected, refunded, partial_refund, forfeited

            $table->date('collected_date')->nullable();
            $table->date('refunded_date')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->json('deduction_reason')->nullable();

            // Finance integration
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('set null');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('hostel_occupant_id');
            $table->index('booking_id');
            $table->index('hostel_id');
            $table->index('status');
            $table->index('deposit_type');
            $table->index('collected_date');
            $table->index('refunded_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
