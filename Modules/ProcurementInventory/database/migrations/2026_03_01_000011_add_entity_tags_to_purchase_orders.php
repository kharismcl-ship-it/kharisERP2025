<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('hostel_id')->nullable()->after('finance_invoice_id');
            $table->unsignedBigInteger('project_id')->nullable()->after('hostel_id');   // construction project
            $table->unsignedBigInteger('farm_id')->nullable()->after('project_id');
            $table->string('module_tag')->nullable()->after('farm_id'); // e.g. 'hostels', 'construction', 'farms'

            $table->index('hostel_id');
            $table->index('project_id');
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['hostel_id']);
            $table->dropIndex(['project_id']);
            $table->dropIndex(['farm_id']);
            $table->dropColumn(['hostel_id', 'project_id', 'farm_id', 'module_tag']);
        });
    }
};
