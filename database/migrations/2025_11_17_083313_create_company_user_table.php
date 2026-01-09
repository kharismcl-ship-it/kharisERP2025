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
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);

            // Explicitly named foreign keys to avoid auto-naming collisions
            $table->foreign('company_id', 'company_user_company_id_fk')
                ->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('user_id', 'company_user_user_id_fk')
                ->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }
};
