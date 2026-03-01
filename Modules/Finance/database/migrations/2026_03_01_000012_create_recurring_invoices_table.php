<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_type')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'annually']);
            $table->date('start_date');
            $table->date('end_date')->nullable();   // null = indefinite
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();
            $table->unsignedInteger('day_of_month')->nullable(); // for monthly
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->unsignedInteger('invoices_generated')->default(0);
            $table->timestamps();

            $table->index(['status', 'next_run_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_invoices');
    }
};
