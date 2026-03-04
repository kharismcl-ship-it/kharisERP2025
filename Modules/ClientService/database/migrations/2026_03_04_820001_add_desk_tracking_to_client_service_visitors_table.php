<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_service_visitors', function (Blueprint $table) {
            $table->foreignId('checked_in_by_user_id')->nullable()->constrained('users')->nullOnDelete()->after('notes');
            $table->foreignId('checked_out_by_user_id')->nullable()->constrained('users')->nullOnDelete()->after('checked_in_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('client_service_visitors', function (Blueprint $table) {
            $table->dropForeign(['checked_in_by_user_id']);
            $table->dropForeign(['checked_out_by_user_id']);
            $table->dropColumn(['checked_in_by_user_id', 'checked_out_by_user_id']);
        });
    }
};
