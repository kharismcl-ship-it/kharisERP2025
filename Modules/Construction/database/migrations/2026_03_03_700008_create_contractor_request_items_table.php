<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_request_id')->constrained('contractor_requests')->cascadeOnDelete();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('material_name');
            $table->string('unit')->nullable();
            $table->decimal('quantity', 10, 3)->default(1);
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_request_items');
    }
};
