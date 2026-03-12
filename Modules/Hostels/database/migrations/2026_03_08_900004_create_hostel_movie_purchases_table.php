<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_movie_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_movie_id')->constrained('hostel_movies')->cascadeOnDelete();
            $table->foreignId('hostel_occupant_id')->constrained('hostel_occupants')->cascadeOnDelete();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['pending', 'paid', 'expired'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_movie_purchases');
    }
};
