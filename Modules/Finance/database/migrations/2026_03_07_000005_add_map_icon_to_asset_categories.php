<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            // Relative path inside the public disk — uploaded via Filament FileUpload
            // Displayed as the marker icon on the Fixed Assets map
            $table->string('map_icon')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn('map_icon');
        });
    }
};
