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
        Schema::create('pay_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('code'); // unique per company: card, momo, bank_transfer, ghanapay, stripe_card, etc.
            $table->string('name'); // e.g. "Card (Flutterwave)", "Momo (Paystack)", "GhanaPay Wallet"
            $table->string('provider'); // flutterwave, paystack, payswitch, stripe, ghanapay, manual
            $table->string('channel'); // card, momo, bank, wallet
            $table->string('currency')->nullable(); // e.g. GHS, USD
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('config')->nullable(); // method-specific config

            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_methods');
    }
};
