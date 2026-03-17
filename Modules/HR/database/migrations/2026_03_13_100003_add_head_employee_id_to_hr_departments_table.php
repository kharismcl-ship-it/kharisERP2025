<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_departments', function (Blueprint $table) {
            $table->foreignId('head_employee_id')
                ->nullable()
                ->after('parent_id')
                ->constrained('hr_employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hr_departments', function (Blueprint $table) {
            $table->dropForeign(['head_employee_id']);
            $table->dropColumn('head_employee_id');
        });
    }
};
