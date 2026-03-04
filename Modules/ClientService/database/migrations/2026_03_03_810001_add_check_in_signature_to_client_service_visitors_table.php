<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_service_visitors', function (Blueprint $table) {
            $table->longText('check_in_signature')->nullable()->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('client_service_visitors', function (Blueprint $table) {
            $table->dropColumn('check_in_signature');
        });
    }
};
