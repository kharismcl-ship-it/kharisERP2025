<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cost_centres', function (Blueprint $table) {
            $table->decimal('budget_amount', 15, 2)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('cost_centres', function (Blueprint $table) {
            $table->dropColumn('budget_amount');
        });
    }
};