<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('trip_logs') && ! Schema::hasColumn('trip_logs', 'fare_amount')) {
            Schema::table('trip_logs', function (Blueprint $table) {
                $table->decimal('fare_amount', 15, 2)->nullable()->after('end_odometer');
                $table->string('client_name')->nullable()->after('fare_amount');
                $table->string('client_phone')->nullable()->after('client_name');
                $table->string('client_email')->nullable()->after('client_phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('trip_logs')) {
            Schema::table('trip_logs', function (Blueprint $table) {
                $table->dropColumn(['fare_amount', 'client_name', 'client_phone', 'client_email']);
            });
        }
    }
};