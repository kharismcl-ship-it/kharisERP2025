<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cast any existing string values to integers so the type change is safe
        DB::statement("UPDATE companies SET parent_company_id = NULL WHERE parent_company_id = '' OR parent_company_id = '0'");

        Schema::table('companies', function (Blueprint $table) {
            // Drop the old string column
            $table->dropColumn('parent_company_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            // Re-add as a proper unsigned bigint FK
            $table->unsignedBigInteger('parent_company_id')->nullable()->after('type');
            $table->foreign('parent_company_id', 'companies_parent_company_id_fk')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign('companies_parent_company_id_fk');
            $table->dropColumn('parent_company_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('parent_company_id')->nullable()->after('type');
        });
    }
};