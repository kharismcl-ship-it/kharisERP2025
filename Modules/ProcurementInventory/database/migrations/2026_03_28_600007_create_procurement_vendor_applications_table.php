<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_vendor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('trading_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Ghana');
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('tax_number')->nullable();
            $table->smallInteger('payment_terms')->default(30);
            $table->string('currency', 10)->default('GHS');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->enum('business_type', [
                'sole_proprietor',
                'partnership',
                'limited_company',
                'ngo',
                'government',
            ])->nullable();
            $table->tinyInteger('years_in_business')->nullable();
            $table->enum('annual_revenue_band', [
                'below_100k',
                '100k_500k',
                '500k_2m',
                'above_2m',
            ])->nullable();
            $table->json('categories_supplied')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected'])->default('submitted');
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('application_token', 64)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_vendor_applications');
    }
};