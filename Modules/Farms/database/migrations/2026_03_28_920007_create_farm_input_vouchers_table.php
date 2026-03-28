<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_input_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_input_credit_account_id')->nullable();
            $table->unsignedBigInteger('farm_id');
            $table->string('voucher_code')->unique();
            $table->string('beneficiary_name')->nullable();
            $table->string('beneficiary_phone')->nullable();
            $table->enum('voucher_type', ['seed', 'fertilizer', 'chemical', 'equipment', 'general'])->default('general');
            $table->string('input_item')->nullable();
            $table->decimal('face_value', 12, 2);
            $table->decimal('redeemed_value', 12, 2)->default(0);
            $table->string('redeemed_at_supplier')->nullable();
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['issued', 'partially_redeemed', 'redeemed', 'expired', 'cancelled'])->default('issued');
            $table->string('verification_pin', 6)->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_input_vouchers');
    }
};