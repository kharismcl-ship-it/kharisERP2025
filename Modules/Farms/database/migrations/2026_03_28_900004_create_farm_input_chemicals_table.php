<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_input_chemicals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('product_name');
            $table->string('brand_name')->nullable();
            $table->string('active_ingredient')->nullable();
            $table->string('chemical_class')->nullable();
            $table->unsignedSmallInteger('phi_days')->nullable();
            $table->decimal('mrl_mg_per_kg', 8, 4)->nullable();
            $table->boolean('approved_for_organic')->default(false);
            $table->string('registration_number')->nullable();
            $table->string('application_rate_per_ha')->nullable();
            $table->text('safety_notes')->nullable();
            $table->boolean('is_restricted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_input_chemicals');
    }
};