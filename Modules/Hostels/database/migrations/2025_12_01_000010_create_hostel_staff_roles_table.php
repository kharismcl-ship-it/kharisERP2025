<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hostel_staff_roles')) {
            return;
        }

        Schema::create('hostel_staff_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // JSON array of permissions
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->string('salary_currency', 3)->default('GHS');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug']);
        });

        Schema::create('hostel_staff_role_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hostel_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('role_id')->index();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('assignment_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('hostel_staff_roles')->onDelete('cascade');

            $table->index(['hostel_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_staff_role_assignments');
        Schema::dropIfExists('hostel_staff_roles');
    }
};
