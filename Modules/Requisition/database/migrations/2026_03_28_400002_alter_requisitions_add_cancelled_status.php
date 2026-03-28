<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the status enum to add 'cancelled'
        DB::statement("ALTER TABLE requisitions MODIFY COLUMN status ENUM('draft','submitted','under_review','pending_revision','approved','rejected','fulfilled','closed','cancelled') NOT NULL DEFAULT 'draft'");

        Schema::table('requisitions', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
        });

        DB::statement("ALTER TABLE requisitions MODIFY COLUMN status ENUM('draft','submitted','under_review','pending_revision','approved','rejected','fulfilled','closed') NOT NULL DEFAULT 'draft'");
    }
};