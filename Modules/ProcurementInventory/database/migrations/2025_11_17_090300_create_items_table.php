<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('item_category_id');
            $table->string('name');
            $table->string('sku');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['company_id', 'sku']);
            $table->unique(['company_id', 'slug']);

            // Explicitly named foreign keys to avoid naming collisions on some MySQL variants
            $table->foreign('company_id', 'items_company_id_fk')
                ->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('item_category_id', 'items_item_category_id_fk')
                ->references('id')->on('item_categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
