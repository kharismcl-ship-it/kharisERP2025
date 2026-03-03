<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('documentable_type')->nullable();
            $table->unsignedBigInteger('documentable_id')->nullable();
            $table->string('title');
            $table->enum('document_type', ['photo', 'video', 'document', 'report', 'contract', 'other'])->default('document');
            $table->string('file_path');
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_documents');
    }
};
