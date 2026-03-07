<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_service_visitors', function (Blueprint $table) {
            $table->foreignId('group_lead_visitor_id')->nullable()->after('visitor_profile_id')
                  ->constrained('client_service_visitors')->nullOnDelete();
            $table->boolean('communication_opt_in')->default(false)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('client_service_visitors', function (Blueprint $table) {
            $table->dropForeign(['group_lead_visitor_id']);
            $table->dropColumn(['group_lead_visitor_id', 'communication_opt_in']);
        });
    }
};
