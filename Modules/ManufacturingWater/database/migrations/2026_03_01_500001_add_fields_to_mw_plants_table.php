<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mw_plants', function (Blueprint $table) {
            if (! Schema::hasColumn('mw_plants', 'location')) {
                $table->string('location')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('mw_plants', 'type')) {
                $table->string('type', 30)->default('treatment')->after('location'); // treatment, bottling, distribution
            }
            if (! Schema::hasColumn('mw_plants', 'source_type')) {
                $table->string('source_type', 50)->nullable()->after('type'); // borehole, river, reservoir, municipal
            }
            if (! Schema::hasColumn('mw_plants', 'capacity_liters_per_day')) {
                $table->decimal('capacity_liters_per_day', 15, 2)->nullable()->after('source_type');
            }
            if (! Schema::hasColumn('mw_plants', 'status')) {
                $table->string('status', 30)->default('active')->after('capacity_liters_per_day');
            }
            if (! Schema::hasColumn('mw_plants', 'description')) {
                $table->text('description')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mw_plants', function (Blueprint $table) {
            $table->dropColumn(['location', 'type', 'source_type', 'capacity_liters_per_day', 'status', 'description']);
        });
    }
};
