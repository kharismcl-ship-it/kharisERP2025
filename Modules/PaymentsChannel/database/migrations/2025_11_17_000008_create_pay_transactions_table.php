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
        Schema::create('pay_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pay_intent_id');
            $table->unsignedBigInteger('company_id');
            $table->string('provider'); // flutterwave, paystack, payswitch, stripe, ghanapay, manual
            $table->string('transaction_type'); // payment, refund, fee, payout
            $table->decimal('amount', 15, 2);
            $table->string('currency'); // e.g. GHS, USD
            $table->string('provider_transaction_id'); // provider transaction id
            $table->string('status')->default('pending'); // pending, successful, failed
            $table->json('raw_payload'); // gateway response or webhook payload
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->foreign('pay_intent_id')->references('id')->on('pay_intents')->onDelete('cascade');
            $table->index(['status']);
            $table->index(['provider']);
            $table->index(['transaction_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_transactions');
    }
};
