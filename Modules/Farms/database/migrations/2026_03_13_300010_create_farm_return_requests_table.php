<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('farm_order_id')->constrained('farm_orders')->cascadeOnDelete();
            $table->string('reason');      // damaged / wrong_item / not_delivered / other
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending / approved / rejected
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_return_requests');
    }
};
