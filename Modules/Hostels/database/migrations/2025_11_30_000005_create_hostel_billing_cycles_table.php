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
        Schema::create('hostel_billing_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->string('name');
            $table->enum('cycle_type', ['monthly', 'quarterly', 'semester', 'custom']);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('billing_date');
            $table->date('due_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_generate')->default(false);
            $table->json('charge_types')->nullable()->comment('JSON array of charge types to include');
            $table->timestamps();

            $table->index(['hostel_id', 'cycle_type']);
            $table->index('billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_billing_cycles');
    }
};
