<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_shift_assignments', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('companies')
                ->nullOnDelete();
        });

        // Back-fill company_id from the employee record
        \Illuminate\Support\Facades\DB::statement('
            UPDATE hr_shift_assignments sa
            JOIN hr_employees e ON e.id = sa.employee_id
            SET sa.company_id = e.company_id
        ');
    }

    public function down(): void
    {
        Schema::table('hr_shift_assignments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
