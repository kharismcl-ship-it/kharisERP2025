<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_ussd_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('phone_number', 20);
            $table->unsignedBigInteger('farm_worker_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->enum('current_menu', [
                'main', 'view_tasks', 'report_attendance', 'submit_report', 'check_weather', 'exit'
            ])->default('main');
            $table->json('session_data')->nullable();
            $table->enum('status', ['active', 'completed', 'timed_out'])->default('active');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_ussd_sessions');
    }
};