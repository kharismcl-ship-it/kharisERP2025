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
            $table->date('due_by')->nullable()->after('notes');
        });

        // Extend the status enum to include pending_revision
        DB::statement("ALTER TABLE requisitions MODIFY COLUMN status ENUM(
            'draft','submitted','under_review','pending_revision',
            'approved','rejected','fulfilled'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn('due_by');
        });

        DB::statement("ALTER TABLE requisitions MODIFY COLUMN status ENUM(
            'draft','submitted','under_review','approved','rejected','fulfilled'
        ) NOT NULL DEFAULT 'draft'");
    }
};