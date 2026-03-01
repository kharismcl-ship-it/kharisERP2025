<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('make')->nullable()->after('description');          // Toyota, Ford…
            $table->string('model')->nullable()->after('make');
            $table->unsignedSmallInteger('year')->nullable()->after('model');
            $table->string('type')->default('car')->after('year');             // car, truck, van, bus, motorcycle
            $table->string('color')->nullable()->after('type');
            $table->string('chassis_number')->nullable()->after('color');
            $table->string('engine_number')->nullable()->after('chassis_number');
            $table->string('fuel_type')->default('petrol')->after('engine_number'); // petrol, diesel, electric, hybrid
            $table->decimal('capacity', 8, 2)->nullable()->after('fuel_type'); // seats or tonnes
            $table->decimal('current_mileage', 12, 2)->default(0)->after('capacity');
            $table->string('status')->default('active')->after('current_mileage'); // active, inactive, under_maintenance, retired
            $table->date('purchase_date')->nullable()->after('status');
            $table->decimal('purchase_price', 15, 2)->nullable()->after('purchase_date');
            $table->foreignId('company_id')->nullable()->change(); // make sure FK is set; original column is unsignedBigInteger
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'make', 'model', 'year', 'type', 'color',
                'chassis_number', 'engine_number', 'fuel_type', 'capacity',
                'current_mileage', 'status', 'purchase_date', 'purchase_price',
            ]);
        });
    }
};
