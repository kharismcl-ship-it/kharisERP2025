<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_sms_commands', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 20);
            $table->unsignedBigInteger('farm_worker_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('raw_message', 500);
            $table->string('command_type')->nullable();
            $table->json('parsed_data')->nullable();
            $table->enum('status', ['received', 'processed', 'failed'])->default('received');
            $table->string('response_message', 500)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_sms_commands');
    }
};