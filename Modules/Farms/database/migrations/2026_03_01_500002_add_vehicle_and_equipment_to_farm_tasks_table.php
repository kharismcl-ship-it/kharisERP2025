<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_tasks', function (Blueprint $table) {
            // Farm-owned equipment (tractors, implements, etc.) from FarmEquipment model
            if (! Schema::hasColumn('farm_tasks', 'farm_equipment_id')) {
                $table->unsignedBigInteger('farm_equipment_id')->nullable()->after('assigned_to_worker_id');
            }
            if (Schema::hasTable('farm_equipment')) {
                $table->foreign('farm_equipment_id')->references('id')->on('farm_equipment')->nullOnDelete();
            }

            // Company fleet vehicle (transport tasks) — only add FK if Fleet module exists
            if (! Schema::hasColumn('farm_tasks', 'vehicle_id')) {
                $table->unsignedBigInteger('vehicle_id')->nullable()->after('farm_equipment_id');
            }
            if (Schema::hasTable('vehicles')) {
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('farm_tasks', function (Blueprint $table) {
            if (Schema::hasTable('farm_equipment')) {
                $table->dropForeign(['farm_equipment_id']);
            }
            if (Schema::hasTable('vehicles')) {
                $table->dropForeign(['vehicle_id']);
            }
            $table->dropColumn(['farm_equipment_id', 'vehicle_id']);
        });
    }
};