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
        Schema::create('hr_leave_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('leave_request_id');
            $table->unsignedBigInteger('uploaded_by_employee_id');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->text('description')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('leave_request_id')->references('id')->on('hr_leave_requests')->onDelete('cascade');
            $table->foreign('uploaded_by_employee_id')->references('id')->on('hr_employees')->onDelete('cascade');

            // Indexes
            $table->index(['company_id', 'leave_request_id']);
            $table->index(['uploaded_by_employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_leave_attachments');
    }
};
