<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('type')->default('product')->after('description'); // product, service, raw_material, asset
            $table->string('unit_of_measure')->nullable()->after('type');     // pcs, kg, litre, box, etc.
            $table->decimal('unit_price', 15, 4)->nullable()->after('unit_of_measure');
            $table->decimal('reorder_level', 15, 4)->nullable()->after('unit_price');
            $table->decimal('reorder_quantity', 15, 4)->nullable()->after('reorder_level');
            $table->boolean('is_active')->default(true)->after('reorder_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'type',
                'unit_of_measure',
                'unit_price',
                'reorder_level',
                'reorder_quantity',
                'is_active',
            ]);
        });
    }
};