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
        Schema::create('hr_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('performance_cycle_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('reviewer_employee_id')->nullable()->index();
            $table->decimal('rating', 3, 2)->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('performance_cycle_id')->references('id')->on('hr_performance_cycles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_performance_reviews');
    }
};
