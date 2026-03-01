<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_assignments', function (Blueprint $table) {
            // Column already exists as unsignedBigInteger — add FK constraint
            $table->foreign('employee_id')
                ->references('id')
                ->on('hr_employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('driver_assignments', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
    }
};
