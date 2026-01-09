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
        Schema::create('hostel_visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->foreignId('hostel_occupant_id')->nullable()->constrained('hostel_occupants')->nullOnDelete();
            $table->string('visitor_name');
            $table->string('visitor_phone')->nullable();
            $table->text('purpose')->nullable();
            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();
            $table->foreignId('recorded_by_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_visitor_logs');
    }
};
