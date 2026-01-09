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
        Schema::create('hr_employment_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('contract_number')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('contract_type')->default('permanent'); // permanent, fixed_term, casual
            $table->date('probation_end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->string('currency')->default('GHS');
            $table->decimal('working_hours_per_week', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_employment_contracts');
    }
};
