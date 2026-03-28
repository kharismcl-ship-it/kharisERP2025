<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_breeding_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('livestock_batch_id');
            $table->enum('event_type', ['mating', 'pregnancy_check', 'parturition', 'weaning', 'abortion', 'stillbirth']);
            $table->date('event_date');
            $table->string('sire_description')->nullable();
            $table->string('dam_description')->nullable();
            $table->enum('method', ['natural', 'artificial_insemination', 'embryo_transfer'])->nullable();
            $table->date('expected_parturition_date')->nullable();
            $table->date('actual_parturition_date')->nullable();
            $table->unsignedSmallInteger('offspring_count')->nullable();
            $table->unsignedSmallInteger('offspring_alive')->nullable();
            $table->decimal('conception_rate_pct', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('livestock_batch_id')->references('id')->on('livestock_batches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_breeding_events');
    }
};