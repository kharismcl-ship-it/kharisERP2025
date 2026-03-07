<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->json('geometry')->nullable()->after('longitude');
        });

        Schema::table('farm_plots', function (Blueprint $table) {
            $table->json('geometry')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn('geometry');
        });

        Schema::table('farm_plots', function (Blueprint $table) {
            $table->dropColumn('geometry');
        });
    }
};