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
        Schema::create('pay_intents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('provider'); // flutterwave, paystack, payswitch, stripe, ghanapay, manual
            $table->unsignedBigInteger('pay_method_id')->nullable();
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            $table->string('reference')->unique(); // internal unique reference, e.g. PMT-2025-00001
            $table->string('provider_reference')->nullable(); // e.g. Flutterwave tx_ref, Stripe payment_intent_id
            $table->decimal('amount', 15, 2);
            $table->string('currency'); // e.g. GHS, USD
            $table->string('status')->default('pending'); // pending, initiated, processing, successful, failed, cancelled, expired
            $table->string('description')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('return_url')->nullable(); // for redirect after payment
            $table->string('callback_url')->nullable(); // override
            $table->json('metadata')->nullable(); // free extra data
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index(['payable_type', 'payable_id']);
            $table->index(['status']);
            $table->index(['provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_intents');
    }
};
