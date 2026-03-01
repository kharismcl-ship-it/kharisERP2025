<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_centres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('cost_centres')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        // Add cost_centre_id to journal_lines for departmental allocation
        Schema::table('journal_lines', function (Blueprint $table) {
            $table->foreignId('cost_centre_id')
                ->nullable()
                ->after('credit')
                ->constrained('cost_centres')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('journal_lines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cost_centre_id');
        });

        Schema::dropIfExists('cost_centres');
    }
};
