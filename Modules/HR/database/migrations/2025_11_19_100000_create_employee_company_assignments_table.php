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
        Schema::create('employee_company_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('company_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('assignment_reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('employee_id')
                ->references('id')
                ->on('hr_employees')
                ->onDelete('cascade');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->unique(['employee_id', 'company_id', 'start_date'], 'emp_comp_assign_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_company_assignments');
    }
};
