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
        Schema::create('whatsapp_group_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('whatsapp_group_id');
            $table->unsignedBigInteger('sender_hostel_occupant_id')->nullable();
            $table->string('message_type')->default('text'); // text, image, video, document
            $table->text('content')->nullable();
            $table->string('media_url')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('whatsapp_group_id')
                ->references('id')
                ->on('hostel_whatsapp_groups')
                ->onDelete('cascade');

            $table->foreign('sender_hostel_occupant_id')
                ->references('id')
                ->on('hostel_occupants')
                ->onDelete('set null');

            $table->index(['whatsapp_group_id', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_group_messages');
    }
};
