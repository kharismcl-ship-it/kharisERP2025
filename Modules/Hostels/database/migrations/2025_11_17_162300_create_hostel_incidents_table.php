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
        Schema::create('hostel_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->foreignId('hostel_occupant_id')->nullable()->constrained('hostel_occupants')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['minor', 'major', 'critical']);
            $table->foreignId('reported_by_user_id')->constrained('users');
            $table->text('action_taken')->nullable();
            $table->enum('status', ['open', 'resolved', 'escalated']);
            $table->timestamp('reported_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_incidents');
    }
};
