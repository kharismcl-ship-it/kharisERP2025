<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('construction_project_id')->nullable()->constrained('construction_projects')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->unsignedBigInteger('contractor_id')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('national_id')->nullable();
            $table->enum('category', ['day_labour', 'project_staff', 'subcontractor'])->default('day_labour');
            $table->string('trade')->nullable();
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();

            $table->foreign('contractor_id')->references('id')->on('contractors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_workers');
    }
};
