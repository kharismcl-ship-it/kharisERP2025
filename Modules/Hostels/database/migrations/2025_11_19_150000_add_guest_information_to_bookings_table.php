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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('guest_first_name')->nullable()->after('status');
            $table->string('guest_last_name')->nullable()->after('guest_first_name');
            $table->string('guest_other_names')->nullable()->after('guest_last_name');
            $table->string('guest_full_name')->nullable()->after('guest_other_names');
            $table->string('guest_gender')->nullable()->after('guest_full_name');
            $table->date('guest_dob')->nullable()->after('guest_gender');
            $table->string('guest_phone')->nullable()->after('guest_dob');
            $table->string('guest_alt_phone')->nullable()->after('guest_phone');
            $table->string('guest_email')->nullable()->after('guest_alt_phone');
            $table->string('guest_national_id_number')->nullable()->after('guest_email');
            $table->string('guest_student_id')->nullable()->after('guest_national_id_number');
            $table->string('guest_institution')->nullable()->after('guest_student_id');
            $table->string('guest_guardian_name')->nullable()->after('guest_institution');
            $table->string('guest_guardian_phone')->nullable()->after('guest_guardian_name');
            $table->string('guest_guardian_email')->nullable()->after('guest_guardian_phone');
            $table->text('guest_address')->nullable()->after('guest_guardian_email');
            $table->string('guest_emergency_contact_name')->nullable()->after('guest_address');
            $table->string('guest_emergency_contact_phone')->nullable()->after('guest_emergency_contact_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'guest_first_name',
                'guest_last_name',
                'guest_other_names',
                'guest_full_name',
                'guest_gender',
                'guest_dob',
                'guest_phone',
                'guest_alt_phone',
                'guest_email',
                'guest_national_id_number',
                'guest_student_id',
                'guest_institution',
                'guest_guardian_name',
                'guest_guardian_phone',
                'guest_guardian_email',
                'guest_address',
                'guest_emergency_contact_name',
                'guest_emergency_contact_phone',
            ]);
        });
    }
};
