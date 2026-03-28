<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_commodity_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('commodity_name');
            $table->string('market_name');
            $table->decimal('price_per_unit', 10, 4);
            $table->string('unit')->default('kg');
            $table->date('price_date');
            $table->enum('source', ['manual', 'esoko_api', 'mofa_mis', 'other'])->default('manual');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['commodity_name', 'price_date']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_commodity_prices');
    }
};