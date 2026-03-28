<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soil_test_records', function (Blueprint $table) {
            $table->decimal('lime_recommendation_kg_ha', 8, 2)->nullable()->after('recommendations');
            $table->decimal('nitrogen_recommendation_kg_ha', 8, 2)->nullable()->after('lime_recommendation_kg_ha');
            $table->decimal('phosphorus_recommendation_kg_ha', 8, 2)->nullable()->after('nitrogen_recommendation_kg_ha');
            $table->decimal('potassium_recommendation_kg_ha', 8, 2)->nullable()->after('phosphorus_recommendation_kg_ha');
            $table->text('recommendation_notes')->nullable()->after('potassium_recommendation_kg_ha');
            $table->text('interpretation')->nullable()->after('recommendation_notes');
        });
    }

    public function down(): void
    {
        Schema::table('soil_test_records', function (Blueprint $table) {
            $table->dropColumn([
                'lime_recommendation_kg_ha',
                'nitrogen_recommendation_kg_ha',
                'phosphorus_recommendation_kg_ha',
                'potassium_recommendation_kg_ha',
                'recommendation_notes',
                'interpretation',
            ]);
        });
    }
};