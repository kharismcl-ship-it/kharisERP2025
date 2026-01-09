<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use raw SQL to modify the enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE beds MODIFY COLUMN status ENUM('available', 'reserved', 'reserved_pending_approval', 'occupied', 'maintenance', 'blocked') NOT NULL DEFAULT 'available'");
        } else {
            // For other databases, we can use Schema::table
            Schema::table('beds', function (Blueprint $table) {
                $table->enum('status', ['available', 'reserved', 'reserved_pending_approval', 'occupied', 'maintenance', 'blocked'])->default('available')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE beds MODIFY COLUMN status ENUM('available', 'reserved', 'occupied', 'maintenance', 'blocked') NOT NULL DEFAULT 'available'");
        } else {
            Schema::table('beds', function (Blueprint $table) {
                $table->enum('status', ['available', 'reserved', 'occupied', 'maintenance', 'blocked'])->default('available')->change();
            });
        }
    }
};
