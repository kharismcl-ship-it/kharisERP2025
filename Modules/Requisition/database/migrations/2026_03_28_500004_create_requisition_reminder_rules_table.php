<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_reminder_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger_status');
            $table->unsignedSmallInteger('hours_after_trigger');
            $table->json('reminder_channels');
            $table->boolean('notify_requester')->default(true);
            $table->boolean('notify_approvers')->default(true);
            $table->boolean('escalate_urgency')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_reminder_rules');
    }
};