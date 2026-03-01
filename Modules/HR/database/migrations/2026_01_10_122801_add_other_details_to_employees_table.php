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
        Schema::table('hr_employees', function (Blueprint $table) {
            $table->text('employee_photo')->nullable()->after('employee_code');
            $table->string('whatsapp_no')->nullable()->after('alt_phone');
            $table->string('residential_gps')->nullable()->after('address');
            $table->json('next_of_kin')->nullable()->after('marital_status');
            $table->string('bank_account_holder_name')->nullable()->after('next_of_kin');
            $table->string('bank_name')->nullable()->after('bank_account_holder_name');
            $table->string('bank_account_no')->nullable()->after('bank_name');
            $table->string('bank_branch')->nullable()->after('bank_account_no');
            $table->string('bank_sort_code')->nullable()->after('bank_branch');
            $table->string('national_id_type')->nullable()->after('email');
            $table->json('national_id_photos')->nullable()->after('national_id_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_employees', function (Blueprint $table) {
            $table->dropColumn([
                'employee_photo',
                'whatsapp_no',
                'residential_gps',
                'next_of_kin',
                'bank_account_holder_name',
                'bank_name',
                'bank_account_no',
                'bank_branch',
                'bank_sort_code',
                'national_id_type',
                'national_id_photos',
            ]);
        });
    }
};
