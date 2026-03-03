<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_service_visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('id_type', ['national_id', 'passport', 'drivers_license', 'other'])->nullable();
            $table->string('id_number')->nullable();
            $table->string('organization')->nullable();
            $table->text('purpose_of_visit');
            $table->unsignedBigInteger('host_employee_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->dateTime('check_in_at');
            $table->dateTime('check_out_at')->nullable();
            $table->string('badge_number')->nullable();
            $table->text('items_brought')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('host_employee_id')->references('id')->on('hr_employees')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('hr_departments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_service_visitors');
    }
};
