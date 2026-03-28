<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_tasks', function (Blueprint $table) {
            $table->string('kanban_status')->default('pending')->after('completed_at');
            $table->integer('sort_order')->default(0)->after('kanban_status');
        });
    }

    public function down(): void
    {
        Schema::table('farm_tasks', function (Blueprint $table) {
            $table->dropColumn(['kanban_status', 'sort_order']);
        });
    }
};