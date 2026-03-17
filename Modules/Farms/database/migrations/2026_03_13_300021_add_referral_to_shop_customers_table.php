<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_customers', function (Blueprint $table) {
            $table->string('referral_code', 10)->unique()->nullable()->after('default_landmark');
        });

        Schema::create('farm_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('referrer_id')->constrained('shop_customers')->cascadeOnDelete();
            $table->foreignId('referred_id')->constrained('shop_customers')->cascadeOnDelete();
            $table->timestamp('credited_at')->nullable(); // when referrer was awarded points
            $table->timestamps();

            $table->unique(['referrer_id', 'referred_id'], 'referral_pair_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_referrals');
        Schema::table('shop_customers', function (Blueprint $table) {
            $table->dropColumn('referral_code');
        });
    }
};
