<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_material_usages', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('company_id');

            if (Schema::hasTable('items')) {
                $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('construction_material_usages', function (Blueprint $table) {
            if (Schema::hasTable('items')) {
                $table->dropForeign(['item_id']);
            }
            $table->dropColumn('item_id');
        });
    }
};
