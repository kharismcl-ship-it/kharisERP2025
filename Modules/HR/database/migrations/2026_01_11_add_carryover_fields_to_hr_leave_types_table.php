<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hr_leave_types', function (Blueprint $table) {
            $table->decimal('carryover_limit', 5, 2)->default(0)->comment('Maximum days that can be carried over to next year')->after('accrual_frequency');
            $table->decimal('max_balance', 5, 2)->nullable()->comment('Maximum balance cap')->after('carryover_limit');
            $table->boolean('pro_rata_enabled')->default(true)->comment('Enable pro-rata calculations for new hires')->after('max_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_leave_types', function (Blueprint $table) {
            $table->dropColumn(['carryover_limit', 'max_balance', 'pro_rata_enabled']);
        });
    }
};
