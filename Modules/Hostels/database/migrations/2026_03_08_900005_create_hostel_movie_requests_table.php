<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_movie_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_occupant_id')->constrained('hostel_occupants')->cascadeOnDelete();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('urgency', ['low', 'normal', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'fulfilled', 'rejected'])->default('pending');
            $table->unsignedBigInteger('fulfilled_movie_id')->nullable();
            $table->foreign('fulfilled_movie_id')->references('id')->on('hostel_movies')->nullOnDelete();
            $table->boolean('is_private')->default(false);
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_movie_requests');
    }
};
