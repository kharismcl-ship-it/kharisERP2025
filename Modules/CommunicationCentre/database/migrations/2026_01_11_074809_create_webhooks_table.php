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
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('secret');
            $table->json('events')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('provider')->nullable();
            $table->json('headers')->nullable();
            $table->integer('timeout')->default(30);
            $table->integer('retry_attempts')->default(3);
            $table->timestamp('last_called_at')->nullable();
            $table->integer('last_response_status')->nullable();
            $table->text('last_response_body')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
            $table->index(['provider', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
