<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');           // e.g. "January 2026", "Q1 2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'closing', 'closed'])->default('open');
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'start_date', 'end_date']);
            $table->index(['company_id', 'status']);
        });

        // Add period_id to journal_entries so we can lock entries per period
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreignId('period_id')
                ->nullable()
                ->after('description')
                ->constrained('accounting_periods')
                ->nullOnDelete();
            $table->boolean('is_locked')->default(false)->after('period_id');
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('period_id');
            $table->dropColumn('is_locked');
        });

        Schema::dropIfExists('accounting_periods');
    }
};
