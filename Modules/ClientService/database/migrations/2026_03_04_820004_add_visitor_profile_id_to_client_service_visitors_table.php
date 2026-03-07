<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_service_visitors', function (Blueprint $table) {
            $table->foreignId('visitor_profile_id')
                ->nullable()
                ->after('company_id')
                ->constrained('client_service_visitor_profiles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('client_service_visitors', function (Blueprint $table) {
            $table->dropForeign(['visitor_profile_id']);
            $table->dropColumn('visitor_profile_id');
        });
    }
};