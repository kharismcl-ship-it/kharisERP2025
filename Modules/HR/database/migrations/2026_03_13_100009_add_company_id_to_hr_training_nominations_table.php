<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_training_nominations', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('companies')
                ->nullOnDelete();
        });

        \Illuminate\Support\Facades\DB::statement('
            UPDATE hr_training_nominations tn
            JOIN hr_employees e ON e.id = tn.employee_id
            SET tn.company_id = e.company_id
        ');
    }

    public function down(): void
    {
        Schema::table('hr_training_nominations', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
