<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cs_visitor_badges', function (Blueprint $table) {
            $table->id();
            $table->string('badge_code', 8)->unique();          // VB-0001 … VB-9999
            $table->enum('status', ['available', 'issued', 'void'])->default('available')->index();
            $table->string('batch_number')->nullable()->index();
            $table->foreignId('issued_to_visitor_id')
                ->nullable()
                ->constrained('client_service_visitors')
                ->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('issued_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'batch_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cs_visitor_badges');
    }
};