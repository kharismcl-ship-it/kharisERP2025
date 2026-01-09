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
        Schema::create('hostel_occupant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_occupant_id')->constrained('hostel_occupants')->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_occupant_documents');
    }
};
