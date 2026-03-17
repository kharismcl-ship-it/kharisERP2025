<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_customers', function (Blueprint $table) {
            $table->boolean('is_b2b')->default(false)->after('referral_code');
            $table->unsignedBigInteger('b2b_account_id')->nullable()->after('is_b2b');
        });
    }

    public function down(): void
    {
        Schema::table('shop_customers', function (Blueprint $table) {
            $table->dropColumn(['is_b2b', 'b2b_account_id']);
        });
    }
};
