<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('request_type');
            $table->string('field_key');
            $table->string('field_label');
            $table->enum('field_type', ['text', 'textarea', 'number', 'date', 'select', 'checkbox']);
            $table->json('field_options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'request_type', 'field_key']);
        });

        Schema::create('requisition_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisitions')->cascadeOnDelete();
            $table->foreignId('custom_field_id')->constrained('requisition_custom_fields')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['requisition_id', 'custom_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_custom_field_values');
        Schema::dropIfExists('requisition_custom_fields');
    }
};