<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('hostel_id')->nullable()->constrained('hostels')->nullOnDelete();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('book_type', ['physical', 'digital'])->default('physical');
            $table->string('digital_file')->nullable();
            $table->unsignedInteger('stock_qty')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_globally_available')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_books');
    }
};
