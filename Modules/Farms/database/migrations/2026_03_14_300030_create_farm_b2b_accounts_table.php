<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_b2b_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id'); // the farm shop company

            // Business identity
            $table->string('business_name');
            $table->string('business_type')->default('restaurant'); // restaurant|hotel|caterer|school|other
            $table->string('contact_name');
            $table->string('contact_phone', 30);
            $table->string('contact_email')->nullable();
            $table->text('business_address')->nullable();
            $table->string('tax_id', 50)->nullable()->comment('TIN / VAT / GhanaCard No.');
            $table->string('ghc_reg', 50)->nullable()->comment('Ghana REGISTRAR-GENERAL certificate number');

            // Approval
            $table->string('status')->default('pending'); // pending|approved|rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();

            // Wholesale terms
            $table->decimal('discount_percent', 5, 2)->default(0.00)
                ->comment('% discount applied to all prices for this account');
            $table->string('payment_terms')->default('prepay'); // prepay|net7|net14|net30
            $table->decimal('credit_limit', 12, 2)->nullable()
                ->comment('Max outstanding balance allowed for credit-term orders');
            $table->decimal('credit_used', 12, 2)->default(0.00);

            $table->text('internal_notes')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_b2b_accounts');
    }
};
