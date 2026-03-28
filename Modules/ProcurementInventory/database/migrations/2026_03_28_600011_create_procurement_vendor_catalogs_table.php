<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_vendor_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('procurement_vendor_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_id')->constrained('procurement_vendor_catalogs')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('vendor_sku')->nullable();
            $table->decimal('unit_price', 15, 4);
            $table->decimal('min_order_quantity', 15, 4)->default(1);
            $table->tinyInteger('lead_time_days')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['catalog_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_vendor_catalog_items');
        Schema::dropIfExists('procurement_vendor_catalogs');
    }
};