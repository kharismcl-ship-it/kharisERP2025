<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('location')->nullable()->after('description');
            $table->decimal('total_area', 14, 4)->nullable()->after('location'); // in acres or hectares
            $table->string('area_unit')->default('acres')->after('total_area'); // acres, hectares
            $table->string('type')->default('mixed')->after('area_unit'); // crop, livestock, mixed, aquaculture
            $table->string('owner_name')->nullable()->after('type');
            $table->string('owner_phone')->nullable()->after('owner_name');
            $table->string('status')->default('active')->after('owner_phone'); // active, inactive, fallow
            $table->text('notes')->nullable()->after('status');
            $table->foreignId('company_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'location', 'total_area', 'area_unit', 'type',
                'owner_name', 'owner_phone', 'status', 'notes',
            ]);
        });
    }
};
