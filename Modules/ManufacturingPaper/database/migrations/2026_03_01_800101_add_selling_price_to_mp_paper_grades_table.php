<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mp_paper_grades') && ! Schema::hasColumn('mp_paper_grades', 'unit_selling_price')) {
            Schema::table('mp_paper_grades', function (Blueprint $table) {
                $table->decimal('unit_selling_price', 15, 4)->nullable()->after('basis_weight');
                $table->decimal('min_order_quantity', 10, 3)->nullable()->after('unit_selling_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mp_paper_grades')) {
            Schema::table('mp_paper_grades', function (Blueprint $table) {
                $table->dropColumn(['unit_selling_price', 'min_order_quantity']);
            });
        }
    }
};