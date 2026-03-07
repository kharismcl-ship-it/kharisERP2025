<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->json('notification_channels')->nullable()->after('due_by');
            $table->foreignId('template_id')->nullable()->constrained('requisition_templates')->nullOnDelete()->after('notification_channels');
            $table->foreignId('preferred_vendor_id')->nullable()->constrained('vendors')->nullOnDelete()->after('template_id');
        });

        // Extend status enum to add 'closed'
        DB::statement("ALTER TABLE requisitions MODIFY COLUMN status ENUM(
            'draft','submitted','under_review','pending_revision',
            'approved','rejected','fulfilled','closed'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropForeign(['preferred_vendor_id']);
            $table->dropColumn(['notification_channels', 'template_id', 'preferred_vendor_id']);
        });

        DB::statement("ALTER TABLE requisitions MODIFY COLUMN status ENUM(
            'draft','submitted','under_review','pending_revision',
            'approved','rejected','fulfilled'
        ) NOT NULL DEFAULT 'draft'");
    }
};