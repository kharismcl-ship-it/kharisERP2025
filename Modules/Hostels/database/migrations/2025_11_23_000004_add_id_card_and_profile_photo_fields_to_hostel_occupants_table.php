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
        Schema::table('hostel_occupants', function (Blueprint $table) {
            $table->string('id_card_front_photo')->nullable();
            $table->string('id_card_back_photo')->nullable();
            $table->string('profile_photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostel_occupants', function (Blueprint $table) {
            $table->dropColumn(['id_card_front_photo', 'id_card_back_photo', 'profile_photo']);
        });
    }
};
