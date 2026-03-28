<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_cycle_counts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('count_number')->unique();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->enum('count_type', ['full', 'partial', 'abc_a', 'abc_b', 'abc_c'])->default('full');
            $table->date('scheduled_date');
            $table->date('counted_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('counted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('variance_threshold_pct', 5, 2)->default(5.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_cycle_count_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('count_id')->constrained('procurement_cycle_counts')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->decimal('system_quantity', 15, 4);
            $table->decimal('counted_quantity', 15, 4)->nullable();
            $table->decimal('variance', 15, 4)->nullable();
            $table->decimal('variance_pct', 5, 2)->nullable();
            $table->decimal('variance_value', 15, 2)->nullable();
            $table->enum('status', ['pending', 'counted', 'approved', 'adjusted'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_cycle_count_lines');
        Schema::dropIfExists('procurement_cycle_counts');
    }
};