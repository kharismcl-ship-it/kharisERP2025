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
        Schema::table('hr_job_positions', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('hr_departments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_job_positions', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
    }
};
