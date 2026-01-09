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
        Schema::create('hostel_billing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->string('name');
            $table->enum('rule_type', ['late_fee', 'damage_charge', 'service_fee', 'discount', 'tax', 'penalty']);
            $table->enum('calculation_method', ['fixed', 'percentage', 'per_day', 'per_unit']);
            $table->decimal('amount', 15, 4);
            $table->string('gl_account_code')->nullable()->comment('Finance module GL account for posting');
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_apply')->default(false);
            $table->json('conditions')->nullable()->comment('JSON conditions for auto-application');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['hostel_id', 'rule_type']);
            $table->index('gl_account_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_billing_rules');
    }
};
