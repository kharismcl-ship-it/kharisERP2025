<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_iot_alert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_iot_device_id')->constrained('farm_iot_devices')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id');
            $table->string('rule_name');
            $table->enum('condition', ['above', 'below', 'equal']);
            $table->decimal('threshold_value', 10, 4);
            $table->string('alert_message');
            $table->enum('severity', ['info', 'warning', 'critical']);
            $table->enum('notification_channel', ['in_app', 'sms', 'email', 'all'])->default('in_app');
            $table->boolean('is_active')->default(true);
            $table->datetime('last_triggered_at')->nullable();
            $table->timestamps();

            $table->index('farm_iot_device_id');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_iot_alert_rules');
    }
};