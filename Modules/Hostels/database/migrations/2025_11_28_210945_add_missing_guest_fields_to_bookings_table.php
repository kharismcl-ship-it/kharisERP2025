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
            if (! Schema::hasColumn('bookings', 'guest_first_name')) {
                $table->string('guest_first_name')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_last_name')) {
                $table->string('guest_last_name')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_other_names')) {
                $table->string('guest_other_names')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_full_name')) {
                $table->string('guest_full_name')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_gender')) {
                $table->string('guest_gender')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_dob')) {
                $table->date('guest_dob')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_phone')) {
                $table->string('guest_phone')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_alt_phone')) {
                $table->string('guest_alt_phone')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_email')) {
                $table->string('guest_email')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_national_id_number')) {
                $table->string('guest_national_id_number')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_student_id')) {
                $table->string('guest_student_id')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_institution')) {
                $table->string('guest_institution')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_guardian_name')) {
                $table->string('guest_guardian_name')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_guardian_phone')) {
                $table->string('guest_guardian_phone')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_guardian_email')) {
                $table->string('guest_guardian_email')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_address')) {
                $table->text('guest_address')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_emergency_contact_name')) {
                $table->string('guest_emergency_contact_name')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'guest_emergency_contact_phone')) {
                $table->string('guest_emergency_contact_phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $drop = [];
            foreach ([
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
            ] as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $drop[] = $col;
                }
            }
            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
