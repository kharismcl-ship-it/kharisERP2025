<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_item_cost_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_item_id')->constrained('requisition_items')->cascadeOnDelete();
            $table->foreignId('cost_centre_id')->constrained('cost_centres')->cascadeOnDelete();
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_item_cost_allocations');
    }
};