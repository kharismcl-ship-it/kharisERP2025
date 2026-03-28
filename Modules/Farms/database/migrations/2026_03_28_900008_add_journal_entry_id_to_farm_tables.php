<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('fin_journal_entry_id')->nullable()->after('notes');
        });

        Schema::table('farm_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('fin_journal_entry_id')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('farm_sales', function (Blueprint $table) {
            $table->dropColumn('fin_journal_entry_id');
        });

        Schema::table('farm_expenses', function (Blueprint $table) {
            $table->dropColumn('fin_journal_entry_id');
        });
    }
};