<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livestock_batches', function (Blueprint $table) {
            $table->enum('species', ['cattle', 'sheep', 'goat', 'pig', 'poultry', 'rabbit', 'other'])->nullable()->after('animal_type');
            $table->unsignedSmallInteger('gestation_days')->nullable()->after('species');
            $table->date('last_mating_date')->nullable()->after('gestation_days');
            $table->date('next_parturition_date')->nullable()->after('last_mating_date');
        });
    }

    public function down(): void
    {
        Schema::table('livestock_batches', function (Blueprint $table) {
            $table->dropColumn(['species', 'gestation_days', 'last_mating_date', 'next_parturition_date']);
        });
    }
};