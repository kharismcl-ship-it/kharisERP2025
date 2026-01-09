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
        Schema::table('hostels', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('region');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('country')->after('longitude');
            $table->string('contact_phone')->nullable()->after('country');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->string('contact_name')->nullable()->after('contact_email');
            $table->string('photo')->nullable()->after('contact_name');
            $table->text('description')->nullable()->after('photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'country', 'contact_phone', 'contact_email', 'contact_name', 'photo', 'description']);
        });
    }
};
