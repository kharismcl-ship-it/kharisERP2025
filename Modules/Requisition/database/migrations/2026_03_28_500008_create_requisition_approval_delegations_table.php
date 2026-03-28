<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_approval_delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('delegator_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('delegate_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_approval_delegations');
    }
};