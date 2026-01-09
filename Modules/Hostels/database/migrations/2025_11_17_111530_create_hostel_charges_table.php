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
        Schema::create('hostel_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id');
            $table->string('name');
            $table->enum('charge_type', ['recurring', 'one_time']);
            $table->decimal('amount');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_charges');
    }
};
