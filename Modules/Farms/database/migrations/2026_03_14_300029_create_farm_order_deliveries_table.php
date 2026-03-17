<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_order_id')->unique();
            $table->unsignedBigInteger('trip_log_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('driver_user_id')->nullable();
            $table->dateTime('estimated_delivery_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->string('status')->default('pending_dispatch'); // pending_dispatch|dispatched|out_for_delivery|delivered|failed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('farm_order_id')->references('id')->on('farm_orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_order_deliveries');
    }
};
