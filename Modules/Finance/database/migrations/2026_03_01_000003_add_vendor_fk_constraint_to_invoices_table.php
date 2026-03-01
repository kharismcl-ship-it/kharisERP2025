<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullify orphaned vendor_id values before adding the FK constraint
        DB::table('invoices')
            ->whereNotNull('vendor_id')
            ->whereNotIn('vendor_id', DB::table('vendors')->pluck('id'))
            ->update(['vendor_id' => null]);

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
        });
    }
};
