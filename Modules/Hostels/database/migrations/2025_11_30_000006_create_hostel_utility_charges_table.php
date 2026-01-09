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
        Schema::create('hostel_utility_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('hostel_occupant_id')->nullable()->constrained('hostel_occupants')->nullOnDelete();
            $table->enum('utility_type', ['electricity', 'water', 'internet', 'gas', 'maintenance', 'service']);
            $table->string('meter_number')->nullable();
            $table->decimal('previous_reading', 15, 2)->nullable();
            $table->decimal('current_reading', 15, 2)->nullable();
            $table->decimal('consumption', 15, 2)->nullable();
            $table->decimal('rate_per_unit', 15, 4)->default(0);
            $table->decimal('fixed_charge', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->date('due_date');
            $table->enum('status', ['pending', 'billed', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('billing_cycle_id')->nullable()->constrained('hostel_billing_cycles')->nullOnDelete();
            $table->timestamps();

            $table->index(['hostel_id', 'utility_type']);
            $table->index(['room_id', 'billing_period_start']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_utility_charges');
    }
};
