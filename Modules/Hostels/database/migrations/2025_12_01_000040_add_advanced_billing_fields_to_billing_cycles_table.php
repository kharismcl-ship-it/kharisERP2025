<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostel_billing_cycles', function (Blueprint $table) {
            $table->integer('grace_period_days')->default(0)->after('due_date');
            $table->decimal('late_fee_percentage', 8, 4)->default(0)->after('grace_period_days');
            $table->decimal('late_fee_fixed_amount', 15, 2)->default(0)->after('late_fee_percentage');
            $table->boolean('auto_post_to_gl')->default(false)->after('auto_generate');
            $table->boolean('include_utilities')->default(true)->after('auto_post_to_gl');
            $table->boolean('include_deposits')->default(false)->after('include_utilities');
            $table->json('billing_rules')->nullable()->after('include_deposits');
            $table->json('notification_settings')->nullable()->after('billing_rules');
        });
    }

    public function down(): void
    {
        Schema::table('hostel_billing_cycles', function (Blueprint $table) {
            $table->dropColumn([
                'grace_period_days',
                'late_fee_percentage',
                'late_fee_fixed_amount',
                'auto_post_to_gl',
                'include_utilities',
                'include_deposits',
                'billing_rules',
                'notification_settings',
            ]);
        });
    }
};
