<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_workers', function (Blueprint $table) {
            $table->enum('worker_type', ['permanent', 'daily', 'contract'])->default('permanent')->after('role');
            $table->date('contract_start')->nullable()->after('worker_type');
            $table->date('contract_end')->nullable()->after('contract_start');
        });
    }

    public function down(): void
    {
        Schema::table('farm_workers', function (Blueprint $table) {
            $table->dropColumn(['worker_type', 'contract_start', 'contract_end']);
        });
    }
};
