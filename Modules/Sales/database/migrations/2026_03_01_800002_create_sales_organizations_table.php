<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_organizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('name');
            $table->string('slug');
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable()->default('Ghana');
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->unsignedInteger('payment_terms')->default(30); // days
            $table->string('currency')->default('GHS');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_organizations');
    }
};