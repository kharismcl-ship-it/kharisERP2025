<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_catalogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('source_module'); // ManufacturingWater, ManufacturingPaper, Farms, ProcurementInventory, Fleet, Construction, Hostels, Restaurant
            $table->string('source_type')->nullable();  // FQN class name or short alias
            $table->unsignedBigInteger('source_id')->nullable()->index(); // FK to source record (null = generic/service)
            $table->string('sku')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit')->default('pcs'); // pcs, kg, litre, night, trip, etc.
            $table->decimal('base_price', 15, 4)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->string('availability_mode')->default('always'); // always, on_request, stock
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['company_id', 'source_module']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_catalogs');
    }
};