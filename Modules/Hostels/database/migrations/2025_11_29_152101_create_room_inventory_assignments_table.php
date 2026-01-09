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
        Schema::create('room_inventory_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('inventory_item_id');
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('removed_at')->nullable();
            $table->string('status')->default('active'); // active, removed, damaged, maintenance
            $table->text('condition_notes')->nullable();
            $table->timestamps();

            // Foreign key constraints - inventory_item_id constraint will be added later
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            // Indexes
            $table->index(['room_id', 'inventory_item_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_inventory_assignments');
    }
};
