<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_input_credit_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('farm_id');
            $table->string('account_ref')->unique();
            $table->string('farmer_name');
            $table->string('farmer_phone')->nullable();
            $table->string('scheme_name')->nullable();
            $table->enum('scheme_type', ['government_subsidy', 'cooperative_advance', 'commercial_credit', 'ngo_program'])->default('cooperative_advance');
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('amount_drawn', 12, 2)->default(0);
            $table->decimal('amount_repaid', 12, 2)->default(0);
            $table->date('season_start')->nullable();
            $table->date('repayment_due_date')->nullable();
            $table->enum('status', ['active', 'repaid', 'defaulted', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_input_credit_accounts');
    }
};