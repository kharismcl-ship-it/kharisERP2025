<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_employee_documents', function (Blueprint $table) {
            $table->date('expires_at')->nullable()->after('document_type');
            $table->string('version')->default('1.0')->after('expires_at');
            $table->boolean('requires_acknowledgment')->default(false)->after('version');
            $table->timestamp('acknowledged_at')->nullable()->after('requires_acknowledgment');
        });
    }

    public function down(): void
    {
        Schema::table('hr_employee_documents', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'version', 'requires_acknowledgment', 'acknowledged_at']);
        });
    }
};