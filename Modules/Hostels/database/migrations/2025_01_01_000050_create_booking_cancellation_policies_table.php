<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_cancellation_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('hostel_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('cancellation_window_hours')->default(24);
            $table->decimal('refund_percentage', 5, 2)->default(100.00);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint removed due to data type incompatibility
            // $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_cancellation_policies');
    }
};
