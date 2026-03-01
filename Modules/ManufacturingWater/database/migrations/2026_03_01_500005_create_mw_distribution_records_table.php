<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mw_distribution_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained('mw_plants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('distribution_date');
            $table->string('destination')->maxLength(255);
            $table->decimal('volume_liters', 15, 2);
            $table->decimal('unit_price', 10, 4)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('vehicle_info')->nullable();
            $table->string('distribution_reference', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mw_distribution_records');
    }
};
