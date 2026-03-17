<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_shop_settings', function (Blueprint $table) {
            $table->boolean('popup_active')->default(false)->after('announcement_bar_active');
            $table->string('popup_title')->nullable()->after('popup_active');
            $table->text('popup_body')->nullable()->after('popup_title');
            $table->string('popup_cta_text', 100)->nullable()->after('popup_body');
            $table->string('popup_cta_url', 500)->nullable()->after('popup_cta_text');
            $table->timestamp('popup_starts_at')->nullable()->after('popup_cta_url');
            $table->timestamp('popup_ends_at')->nullable()->after('popup_starts_at');
            $table->timestamp('announcement_bar_starts_at')->nullable()->after('popup_ends_at');
            $table->timestamp('announcement_bar_ends_at')->nullable()->after('announcement_bar_starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('farm_shop_settings', function (Blueprint $table) {
            $table->dropColumn([
                'popup_active', 'popup_title', 'popup_body', 'popup_cta_text', 'popup_cta_url',
                'popup_starts_at', 'popup_ends_at',
                'announcement_bar_starts_at', 'announcement_bar_ends_at',
            ]);
        });
    }
};
