<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_setting_id')->constrained()->onDelete('cascade');
            $table->string('status'); // pending, running, completed, failed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('records_processed')->default(0);
            $table->text('error_message')->nullable();
            $table->float('execution_time')->nullable(); // in seconds
            $table->json('details')->nullable(); // Additional execution details
            $table->timestamps();

            $table->index(['automation_setting_id', 'status']);
            $table->index(['started_at', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
    }
};
