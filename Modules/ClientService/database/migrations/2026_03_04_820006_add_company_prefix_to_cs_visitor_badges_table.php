<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cs_visitor_badges', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')
                  ->constrained('companies')->nullOnDelete();
            $table->string('prefix', 10)->default('VB')->after('company_id');
        });
    }

    public function down(): void
    {
        Schema::table('cs_visitor_badges', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'prefix']);
        });
    }
};
