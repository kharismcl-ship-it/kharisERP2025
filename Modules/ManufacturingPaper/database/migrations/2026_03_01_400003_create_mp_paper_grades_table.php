<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mp_paper_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('gsm', 8, 2)->nullable(); // grams per square metre
            $table->decimal('width_mm', 8, 2)->nullable();
            $table->string('color', 50)->default('white');
            $table->string('category', 50)->default('printing'); // printing, writing, packaging, tissue, specialty
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mp_paper_grades');
    }
};