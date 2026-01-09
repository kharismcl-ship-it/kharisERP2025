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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_type')->nullable(); // hostel_occupant, external, etc.
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('status'); // draft, sent, paid, overdue, cancelled
            $table->decimal('sub_total', 15, 2);
            $table->decimal('tax_total', 15, 2);
            $table->decimal('total', 15, 2);
            $table->unsignedBigInteger('hostel_id')->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('construction_project_id')->nullable();
            $table->unsignedBigInteger('plant_id')->nullable();
            $table->timestamps();

            $table->index(['customer_type', 'customer_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
