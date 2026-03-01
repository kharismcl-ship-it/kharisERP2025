<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('batch_reference')->unique();
            $table->string('animal_type'); // cattle, sheep, goats, poultry, pigs, fish
            $table->string('breed')->nullable();
            $table->unsignedInteger('initial_count');
            $table->unsignedInteger('current_count');
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 18, 2)->default(0);
            $table->string('status')->default('active'); // active, sold, slaughtered, deceased
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_batches');
    }
};
