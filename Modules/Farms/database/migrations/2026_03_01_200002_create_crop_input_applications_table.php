<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_input_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_cycle_id')->constrained('crop_cycles')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('application_date');
            $table->string('input_type'); // seed|fertilizer|pesticide|herbicide|irrigation_water|other
            $table->string('product_name');
            $table->decimal('quantity', 14, 4);
            $table->string('unit');
            $table->decimal('unit_cost', 14, 4)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->string('application_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_input_applications');
    }
};