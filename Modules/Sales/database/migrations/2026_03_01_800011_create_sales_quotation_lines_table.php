<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_quotation_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id')->index();
            $table->unsignedBigInteger('catalog_item_id')->index();
            $table->decimal('quantity', 10, 3)->default(1);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('discount_pct', 5, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_quotation_lines');
    }
};