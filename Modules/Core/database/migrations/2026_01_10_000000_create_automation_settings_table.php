<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_settings', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // e.g., 'HR', 'Finance'
            $table->string('action'); // e.g., 'leave_accrual', 'invoice_generation'
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->boolean('is_enabled')->default(false);
            $table->string('schedule_type')->nullable(); // daily, weekly, monthly, yearly, custom
            $table->string('schedule_value')->nullable(); // For custom schedules
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->json('config')->nullable(); // Module-specific configuration
            $table->timestamps();

            $table->unique(['module', 'action', 'company_id']);
            $table->index(['module', 'action']);
            $table->index(['company_id', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_settings');
    }
};
