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
        Schema::create('hostel_whatsapp_group_occupant', function (Blueprint $table) {
            $table->unsignedBigInteger('whatsapp_group_id');
            $table->unsignedBigInteger('hostel_occupant_id');

            $table->primary(['whatsapp_group_id', 'hostel_occupant_id']);

            $table->foreign('whatsapp_group_id')
                ->references('id')
                ->on('hostel_whatsapp_groups')
                ->onDelete('cascade');

            $table->foreign('hostel_occupant_id')
                ->references('id')
                ->on('hostel_occupants')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_whatsapp_group_occupant');
    }
};
