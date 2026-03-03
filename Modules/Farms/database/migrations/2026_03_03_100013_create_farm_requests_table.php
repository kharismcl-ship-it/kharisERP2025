<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->string('reference')->unique();
            $table->enum('request_type', ['materials', 'funds', 'equipment', 'services', 'labour', 'other'])->default('materials');
            $table->string('title');
            $table->text('description');
            $table->enum('urgency', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'fulfilled'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('fulfilled_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('farm_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_request_id')->constrained('farm_requests')->cascadeOnDelete();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('description');
            $table->decimal('quantity', 10, 3);
            $table->string('unit')->default('unit');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_request_items');
        Schema::dropIfExists('farm_requests');
    }
};
