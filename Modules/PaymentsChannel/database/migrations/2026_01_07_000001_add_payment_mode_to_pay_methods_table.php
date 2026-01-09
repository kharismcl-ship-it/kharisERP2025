<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->enum('payment_mode', ['online', 'offline'])
                ->after('channel')
                ->comment('Online: Processed through payment gateway. Offline: Manual/cash/bank transfer.');
            $table->text('offline_payment_instruction')
                ->after('payment_mode')
                ->nullable()
                ->comment('Instructions for offline payments.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->dropColumn('payment_mode');
        });
    }
};
