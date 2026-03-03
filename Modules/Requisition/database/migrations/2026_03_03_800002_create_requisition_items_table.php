<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisitions')->onDelete('cascade');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('description');
            $table->decimal('quantity', 10, 3)->default(1);
            $table->string('unit')->default('pcs');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_items');
    }
};
