<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_service_visitor_profiles', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('organization');
            $table->text('check_in_signature')->nullable()->after('photo_path');
            $table->boolean('communication_opt_in')->default(false)->after('check_in_signature');
        });
    }

    public function down(): void
    {
        Schema::table('client_service_visitor_profiles', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'check_in_signature', 'communication_opt_in']);
        });
    }
};
