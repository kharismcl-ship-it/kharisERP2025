<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'current_company_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('current_company_id')->nullable()->after('remember_token');
            $table->foreign('current_company_id', 'users_current_company_id_fk')
                ->references('id')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignIfExists('users_current_company_id_fk');
            $table->dropColumnIfExists('current_company_id');
        });
    }
};
