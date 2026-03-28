<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_equipment', function (Blueprint $table) {
            $table->decimal('total_hours_logged', 10, 2)->default(0)->after('notes');
            $table->date('last_service_date')->nullable()->change();
            $table->date('next_service_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('farm_equipment', function (Blueprint $table) {
            $table->dropColumn('total_hours_logged');
        });
    }
};