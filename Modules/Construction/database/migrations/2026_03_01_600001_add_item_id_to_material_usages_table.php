<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('construction_material_usages')) {
            return;
        }

        Schema::table('construction_material_usages', function (Blueprint $table) {
            if (! Schema::hasColumn('construction_material_usages', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('company_id');
            }

            if (Schema::hasTable('items')) {
                $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('construction_material_usages')) {
            return;
        }

        Schema::table('construction_material_usages', function (Blueprint $table) {
            if (Schema::hasTable('items')) {
                $table->dropForeign(['item_id']);
            }
            if (Schema::hasColumn('construction_material_usages', 'item_id')) {
                $table->dropColumn('item_id');
            }
        });
    }
};
