<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_cooperative_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_cooperative_id')->constrained('farm_cooperatives')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->string('member_number')->nullable();
            $table->date('membership_date')->nullable();
            $table->decimal('land_area_ha', 8, 4)->nullable();
            $table->unsignedInteger('share_count')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('farm_cooperative_id');
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_cooperative_members');
    }
};