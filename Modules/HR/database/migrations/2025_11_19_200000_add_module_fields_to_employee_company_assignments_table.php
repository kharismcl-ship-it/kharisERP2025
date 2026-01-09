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
        Schema::table('employee_company_assignments', function (Blueprint $table) {
            $table->string('role')->nullable()->after('assignment_reason');
            $table->timestamp('assigned_at')->nullable()->after('role');
            $table->timestamp('expires_at')->nullable()->after('assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_company_assignments', function (Blueprint $table) {
            $table->dropColumn(['role', 'assigned_at', 'expires_at']);
        });
    }
};
