<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_floors', function (Blueprint $table) {
            $table->id();
$table->foreignId('hostel_id')->constrained('hostels');
$table->foreignId('hostel_block_id')->constrained('hostel_blocks');
$table->string('name');
$table->integer('level')->nullable();
$table->timestamps();//
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_floors');
    }
};
