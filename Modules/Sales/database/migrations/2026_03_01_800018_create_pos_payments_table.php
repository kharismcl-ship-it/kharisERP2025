<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_sale_id')->index();
            $table->string('method'); // cash, momo, card, credit, voucher
            $table->decimal('amount', 15, 2);
            $table->string('reference')->nullable(); // external transaction reference
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};