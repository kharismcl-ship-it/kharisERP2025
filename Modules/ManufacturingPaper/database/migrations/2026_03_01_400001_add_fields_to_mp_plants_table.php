<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mp_plants', function (Blueprint $table) {
            if (! Schema::hasColumn('mp_plants', 'location')) {
                $table->string('location')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('mp_plants', 'type')) {
                $table->string('type')->default('integrated')->after('location'); // integrated, pulp_only, paper_only, recycled
            }
            if (! Schema::hasColumn('mp_plants', 'capacity')) {
                $table->decimal('capacity', 12, 2)->nullable()->after('type');
            }
            if (! Schema::hasColumn('mp_plants', 'capacity_unit')) {
                $table->string('capacity_unit', 20)->default('tonnes/day')->after('capacity');
            }
            if (! Schema::hasColumn('mp_plants', 'status')) {
                $table->string('status', 30)->default('active')->after('capacity_unit');
            }
            if (! Schema::hasColumn('mp_plants', 'description')) {
                $table->text('description')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mp_plants', function (Blueprint $table) {
            $table->dropColumn(['location', 'type', 'capacity', 'capacity_unit', 'status', 'description']);
        });
    }
};