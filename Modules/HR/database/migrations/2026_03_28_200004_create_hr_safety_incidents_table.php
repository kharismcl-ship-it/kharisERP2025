<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_safety_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->nullOnDelete(); // injured employee (if any)
            $table->string('ref_number')->unique();
            $table->dateTime('incident_date');
            $table->string('location');
            $table->string('incident_type'); // near_miss, first_aid, medical_treatment, lost_time, fatality, property_damage
            $table->text('description');
            $table->string('severity')->default('minor'); // minor, moderate, serious, critical, fatal
            $table->string('injury_type')->nullable(); // cut, burn, fracture, strain, bruise, etc.
            $table->string('body_part_affected')->nullable();
            $table->text('immediate_action_taken')->nullable();
            $table->foreignId('reported_by_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->string('status')->default('open'); // open, under_investigation, closed
            $table->text('root_cause')->nullable();
            $table->text('corrective_action')->nullable();
            $table->foreignId('investigated_by_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->boolean('reported_to_authorities')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_safety_incidents');
    }
};
