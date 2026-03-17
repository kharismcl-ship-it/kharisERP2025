<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_certifications', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('companies')
                ->nullOnDelete();
        });

        \Illuminate\Support\Facades\DB::statement('
            UPDATE hr_certifications c
            JOIN hr_employees e ON e.id = c.employee_id
            SET c.company_id = e.company_id
        ');
    }

    public function down(): void
    {
        Schema::table('hr_certifications', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
