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
        Schema::create('comm_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->string('channel'); // email, sms, whatsapp
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('to_name')->nullable();
            $table->string('to_email')->nullable();
            $table->string('to_phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('queued'); // queued, sent, delivered, failed
            $table->text('error_message')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['status']);
            $table->index(['channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comm_messages');
    }
};
