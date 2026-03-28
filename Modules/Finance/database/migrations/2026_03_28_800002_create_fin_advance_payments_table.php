<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_advance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('advance_number')->unique();
            $table->enum('advance_type', ['customer_deposit', 'vendor_advance'])->default('customer_deposit');
            $table->string('party_name');
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_type')->nullable(); // 'customer' or 'vendor'
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('GHS');
            $table->date('received_date');
            $table->decimal('applied_amount', 15, 2)->default(0);
            $table->enum('status', ['open', 'partially_applied', 'fully_applied'])->default('open');
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('gl_account_id')->nullable();
            $table->foreign('gl_account_id')->references('id')->on('accounts')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_advance_payments');
    }
};