<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_user', function (Blueprint $table) {
            // Skip if already added (idempotent)
            if (Schema::hasColumn('company_user', 'is_active')) {
                return;
            }

            $table->string('position')->nullable()->after('user_id');
            $table->boolean('is_active')->default(true)->after('position');
            $table->timestamp('assigned_at')->nullable()->after('is_active');
            $table->timestamp('expires_at')->nullable()->after('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->dropColumn(['position', 'is_active', 'assigned_at', 'expires_at']);
        });
    }
};
