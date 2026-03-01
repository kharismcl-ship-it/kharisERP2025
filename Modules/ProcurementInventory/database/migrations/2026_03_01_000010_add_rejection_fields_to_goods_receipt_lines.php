<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goods_receipt_lines', function (Blueprint $table) {
            $table->decimal('quantity_rejected', 15, 4)->default(0)->after('quantity_received');
            $table->text('rejection_reason')->nullable()->after('quantity_rejected');
        });
    }

    public function down(): void
    {
        Schema::table('goods_receipt_lines', function (Blueprint $table) {
            $table->dropColumn(['quantity_rejected', 'rejection_reason']);
        });
    }
};
