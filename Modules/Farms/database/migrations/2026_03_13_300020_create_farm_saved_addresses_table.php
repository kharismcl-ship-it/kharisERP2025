<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_saved_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_customer_id')->constrained('shop_customers')->cascadeOnDelete();
            $table->string('label')->default('Home'); // Home|Work|Other|custom
            $table->text('address');
            $table->string('landmark')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_saved_addresses');
    }
};
