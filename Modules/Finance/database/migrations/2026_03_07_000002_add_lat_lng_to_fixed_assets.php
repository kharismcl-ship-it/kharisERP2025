<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Single-point pin — read/written automatically by MapPicker
            $table->decimal('latitude', 10, 7)->nullable()->after('location');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            // GeoJSON FeatureCollection for user-drawn polygons, polylines,
            // circles and rectangles (Geoman shapes).
            // Stored as JSON; displayed via MapEntry geoJsonData() /
            // HasGeoJsonFile trait; written back on save via a Hidden field
            // + custom Alpine Geoman event hook.
            // Example value:
            //   {"type":"FeatureCollection","features":[{"type":"Feature",
            //    "geometry":{"type":"Polygon","coordinates":[[...]]},"properties":{}}]}
            $table->json('geometry')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'geometry']);
        });
    }
};
