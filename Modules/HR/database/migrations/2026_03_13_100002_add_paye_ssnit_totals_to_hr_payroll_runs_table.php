<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_payroll_runs', function (Blueprint $table) {
            $table->decimal('total_paye', 15, 2)->default(0)->after('total_net');
            $table->decimal('total_ssnit', 15, 2)->default(0)->after('total_paye');
        });
    }

    public function down(): void
    {
        Schema::table('hr_payroll_runs', function (Blueprint $table) {
            $table->dropColumn(['total_paye', 'total_ssnit']);
        });
    }
};
