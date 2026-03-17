<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0); // 0-100
            $table->boolean('is_active')->default(true);
            $table->json('images')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('farm_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_bundle_id')->constrained('farm_bundles')->cascadeOnDelete();
            $table->foreignId('farm_produce_inventory_id')->constrained('farm_produce_inventories')->cascadeOnDelete();
            $table->decimal('quantity', 10, 3)->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_bundle_items');
        Schema::dropIfExists('farm_bundles');
    }
};
