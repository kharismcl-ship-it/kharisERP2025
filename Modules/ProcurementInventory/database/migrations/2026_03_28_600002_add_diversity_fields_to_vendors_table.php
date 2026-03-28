<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('is_local')->default(false)->after('notes');
            $table->enum('diversity_class', [
                'none',
                'women_owned',
                'minority_owned',
                'sme',
                'veteran_owned',
                'youth_owned',
            ])->default('none')->after('is_local');
            $table->decimal('local_content_score', 5, 2)->nullable()->after('diversity_class');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['is_local', 'diversity_class', 'local_content_score']);
        });
    }
};