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
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->string('location');
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->integer('capacity');
            $table->enum('gender_policy', ['male', 'female', 'mixed']);
            $table->time('check_in_time_default')->nullable();
            $table->time('check_out_time_default')->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }
};
