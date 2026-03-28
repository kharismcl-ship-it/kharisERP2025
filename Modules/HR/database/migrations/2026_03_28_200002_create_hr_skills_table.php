<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('skill_category_id')->nullable()->constrained('hr_skill_categories')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('skill_type')->default('technical'); // technical, soft, leadership, language, certification
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_skills');
    }
};