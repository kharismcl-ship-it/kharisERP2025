<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->string('client_email')->nullable()->after('client_contact');
            $table->string('client_phone')->nullable()->after('client_email');
            $table->string('payment_status', 30)->default('unpaid')->after('total_spent'); // unpaid, partial, paid
            $table->decimal('amount_paid', 18, 2)->default(0)->after('payment_status');
            $table->unsignedBigInteger('invoice_id')->nullable()->after('amount_paid');

            if (Schema::hasTable('invoices')) {
                $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            if (Schema::hasTable('invoices')) {
                $table->dropForeign(['invoice_id']);
            }
            $table->dropColumn(['client_email', 'client_phone', 'payment_status', 'amount_paid', 'invoice_id']);
        });
    }
};