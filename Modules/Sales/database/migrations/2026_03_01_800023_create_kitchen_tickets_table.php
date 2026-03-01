<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchen_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dining_order_id')->index();
            $table->string('station')->nullable(); // grill, fryer, salads, bar, etc.
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->timestamp('fired_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_tickets');
    }
};