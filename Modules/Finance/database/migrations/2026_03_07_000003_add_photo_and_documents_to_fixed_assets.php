<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add photo column to fixed_assets
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('serial_number');
        });

        // Documents table for fixed assets
        Schema::create('fixed_asset_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('document_type', [
                'contract', 'invoice', 'photo', 'manual',
                'warranty', 'insurance', 'inspection', 'other',
            ])->default('other');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_asset_documents');

        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};