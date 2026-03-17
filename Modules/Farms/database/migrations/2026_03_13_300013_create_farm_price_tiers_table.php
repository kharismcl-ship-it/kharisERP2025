<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_produce_inventory_id')->constrained('farm_produce_inventories')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->decimal('min_quantity', 10, 3);   // e.g. 10.000
            $table->decimal('price_per_unit', 12, 2); // e.g. 8.50
            $table->string('label')->nullable();       // e.g. "Wholesale", "Bulk (10+)"
            $table->timestamps();

            $table->index('farm_produce_inventory_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_price_tiers');
    }
};
