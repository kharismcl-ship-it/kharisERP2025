<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mw_distribution_records') && ! Schema::hasColumn('mw_distribution_records', 'customer_name')) {
            Schema::table('mw_distribution_records', function (Blueprint $table) {
                $table->string('customer_name')->nullable()->after('destination');
                $table->string('customer_phone')->nullable()->after('customer_name');
                $table->string('customer_email')->nullable()->after('customer_phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mw_distribution_records')) {
            Schema::table('mw_distribution_records', function (Blueprint $table) {
                $table->dropColumn(['customer_name', 'customer_phone', 'customer_email']);
            });
        }
    }
};