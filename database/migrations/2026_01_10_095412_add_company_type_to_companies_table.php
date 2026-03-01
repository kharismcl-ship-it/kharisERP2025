<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
            $table->string('company_logo')->after('name')->nullable();
            $table->string('company_service_type')->after('name')->default('hostel');
            $table->text('company_service_description')->after('company_service_type')->nullable();
            $table->string('company_address')->after('company_service_description')->nullable();
            $table->string('company_country')->after('company_address')->nullable();
            $table->string('company_city')->after('company_address')->nullable();
            $table->text('company_location')->after('company_city')->nullable();
            $table->double('company_latitude')->after('company_address')->nullable();
            $table->double('company_longitude')->after('company_latitude')->nullable();
            $table->string('company_ghanapostgps')->after('company_longitude')->nullable();
            $table->string('company_phone')->after('company_address')->nullable();
            $table->string('company_email')->after('company_phone')->nullable();
            $table->string('company_website')->after('company_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
            $table->dropColumn('company_logo');
            $table->dropColumn('company_service_type');
            $table->dropColumn('company_service_description');
            $table->dropColumn('company_address');
            $table->dropColumn('company_country');
            $table->dropColumn('company_city');
            $table->dropColumn('company_location');
            $table->dropColumn('company_latitude');
            $table->dropColumn('company_longitude');
            $table->dropColumn('company_ghanapostgps');
            $table->dropColumn('company_phone');
            $table->dropColumn('company_email');
            $table->dropColumn('company_website');
        });
    }
};
