<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id')->index();
            $table->string('section')->nullable(); // Main Hall, Terrace, VIP, etc.
            $table->string('table_number');
            $table->unsignedSmallInteger('capacity')->default(4);
            $table->string('status')->default('available'); // available, occupied, reserved, cleaning
            $table->timestamps();

            $table->unique(['restaurant_id', 'table_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_tables');
    }
};