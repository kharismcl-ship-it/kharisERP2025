<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('type'); // call, email, meeting, demo, task, note
            $table->string('subject');
            $table->text('body')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('outcome')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();

            // Polymorphic relation — can be attached to Lead, Contact, Opportunity, etc.
            $table->nullableMorphs('related');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_activities');
    }
};