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
            $table->boolean('has_accrual')->default(true)->after('is_active');
            $table->decimal('accrual_rate', 5, 2)->default(1.67)->comment('Days per month')->after('has_accrual');
            $table->string('accrual_frequency')->default('monthly')->comment('monthly, quarterly, annually')->after('accrual_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_leave_types', function (Blueprint $table) {
            $table->dropColumn(['has_accrual', 'accrual_rate', 'accrual_frequency']);
        });
    }
};
