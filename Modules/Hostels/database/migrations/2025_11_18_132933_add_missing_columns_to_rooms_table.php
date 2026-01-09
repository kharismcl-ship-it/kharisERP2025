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
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('gender_policy', ['male', 'female', 'mixed', 'inherit_hostel'])->nullable()->after('type');
            $table->unsignedBigInteger('block_id')->nullable()->after('hostel_id');
            $table->unsignedBigInteger('floor_id')->nullable()->after('block_id');
            $table->enum('billing_cycle', ['per_night', 'per_semester', 'per_year'])->nullable()->after('base_rate');
            $table->integer('max_occupancy')->nullable()->after('billing_cycle');
            $table->integer('current_occupancy')->default(0)->after('max_occupancy');
            $table->text('notes')->nullable()->after('status');

            $table->foreign('block_id')->references('id')->on('hostel_blocks')->onDelete('set null');
            $table->foreign('floor_id')->references('id')->on('hostel_floors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
            $table->dropForeign(['floor_id']);

            $table->dropColumn([
                'gender_policy',
                'block_id',
                'floor_id',
                'billing_cycle',
                'max_occupancy',
                'current_occupancy',
                'notes',
            ]);
        });
    }
};
