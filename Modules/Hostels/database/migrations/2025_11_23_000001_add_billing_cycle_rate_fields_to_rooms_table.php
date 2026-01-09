<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('per_night_rate', 10, 2)->nullable()->after('base_rate');
            $table->decimal('per_semester_rate', 10, 2)->nullable()->after('per_night_rate');
            $table->decimal('per_year_rate', 10, 2)->nullable()->after('per_semester_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['per_night_rate', 'per_semester_rate', 'per_year_rate']);
        });
    }
};
