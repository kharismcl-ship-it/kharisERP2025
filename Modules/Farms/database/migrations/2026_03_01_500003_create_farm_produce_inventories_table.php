<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_produce_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('product_name');
            $table->string('unit', 50)->nullable();
            $table->decimal('total_quantity', 14, 3)->default(0)->comment('Total harvested into stock');
            $table->decimal('current_stock', 14, 3)->default(0)->comment('Remaining available stock');
            $table->decimal('reserved_stock', 14, 3)->default(0)->comment('Allocated to pending sales');
            $table->decimal('sold_stock', 14, 3)->default(0)->comment('Already sold');
            $table->decimal('unit_cost', 14, 4)->nullable()->comment('Average cost per unit');
            $table->date('harvest_date')->nullable();
            $table->date('expiry_date')->nullable()->comment('For perishables');
            $table->string('storage_location')->nullable();
            $table->string('status', 30)->default('in_stock'); // in_stock, low_stock, depleted
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_produce_inventories');
    }
};