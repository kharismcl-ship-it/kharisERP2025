<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_restaurants', function (Blueprint $table) {
            $table->foreignId('hostel_id')->nullable()->after('company_id')->constrained('hostels')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales_restaurants', function (Blueprint $table) {
            $table->dropForeignIdFor(\Modules\Hostels\Models\Hostel::class);
            $table->dropColumn('hostel_id');
        });
    }
};
