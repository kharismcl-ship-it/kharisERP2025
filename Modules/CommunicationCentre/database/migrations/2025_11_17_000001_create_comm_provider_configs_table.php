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
        Schema::create('comm_provider_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('channel'); // email, sms, whatsapp
            $table->string('provider'); // laravel_mail, twilio, mnotify, wasender
            $table->string('name'); // display label
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('config'); // Provider specific configuration

            $table->timestamps();

            $table->index(['company_id', 'channel', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comm_provider_configs');
    }
};
