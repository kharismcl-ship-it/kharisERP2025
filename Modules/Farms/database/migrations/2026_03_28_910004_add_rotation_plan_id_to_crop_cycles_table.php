<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crop_cycles', function (Blueprint $table) {
            $table->foreignId('farm_rotation_plan_id')
                ->nullable()
                ->after('farm_plot_id')
                ->constrained('farm_rotation_plans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crop_cycles', function (Blueprint $table) {
            $table->dropForeignIdFor(\Modules\Farms\Models\FarmRotationPlan::class);
            $table->dropColumn('farm_rotation_plan_id');
        });
    }
};