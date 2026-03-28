<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_grower_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('farm_cooperative_id')->nullable()->constrained('farm_cooperatives')->nullOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->string('payment_ref')->unique();
            $table->enum('payment_type', ['produce_purchase', 'input_advance', 'input_recovery', 'bonus', 'other']);
            $table->foreignId('harvest_record_id')->nullable()->constrained('harvest_records')->nullOnDelete();
            $table->decimal('quantity_kg', 10, 2)->nullable();
            $table->decimal('price_per_kg', 10, 4)->nullable();
            $table->decimal('gross_amount', 12, 2);
            $table->json('deductions')->nullable(); // [{label, amount}]
            $table->decimal('net_amount', 12, 2);
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer'])->default('mobile_money');
            $table->string('momo_number')->nullable();
            $table->date('payment_date');
            $table->enum('status', ['pending', 'paid', 'reversed'])->default('pending');
            $table->string('paid_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_grower_payments');
    }
};