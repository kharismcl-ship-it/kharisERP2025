<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_opportunities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('title');
            $table->unsignedBigInteger('contact_id')->nullable()->index();
            $table->unsignedBigInteger('organization_id')->nullable()->index();
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->unsignedTinyInteger('probability_pct')->default(50);
            $table->string('stage')->default('prospecting'); // prospecting, qualification, proposal, negotiation, closed_won, closed_lost
            $table->date('expected_close_date')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_opportunities');
    }
};