<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('type');                  // routine, repair, inspection, tire_change, etc.
            $table->text('description');
            $table->date('service_date');
            $table->decimal('mileage_at_service', 12, 2)->nullable();
            $table->date('next_service_date')->nullable();
            $table->decimal('next_service_mileage', 12, 2)->nullable();
            $table->string('service_provider')->nullable(); // garage / workshop name
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('status')->default('completed'); // scheduled, in_progress, completed
            $table->text('notes')->nullable();
            $table->foreignId('finance_expense_id')->nullable(); // linked Finance record
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
