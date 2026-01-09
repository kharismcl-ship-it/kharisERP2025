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
        Schema::create('inventory_maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->unsignedBigInteger('room_assignment_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable(); // staff member assigned to maintenance

            // Maintenance details
            $table->string('maintenance_type'); // preventive, corrective, emergency, routine
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled

            // Maintenance scheduling
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Maintenance details
            $table->text('description')->nullable();
            $table->text('issue_details')->nullable();
            $table->text('work_performed')->nullable();
            $table->text('parts_used')->nullable(); // JSON array of parts with quantities
            $table->decimal('labor_cost', 12, 2)->default(0);
            $table->decimal('parts_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);

            // Maintenance outcomes
            $table->string('outcome')->nullable(); // resolved, partially_resolved, not_resolved
            $table->text('notes')->nullable();
            $table->text('follow_up_required')->nullable();
            $table->timestamp('next_maintenance_date')->nullable();

            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            // Foreign key constraints - inventory_item_id and assigned_to constraints will be added later
            $table->foreign('room_assignment_id')->references('id')->on('room_inventory_assignments')->onDelete('cascade');

            // Indexes
            $table->index(['status', 'priority']);
            $table->index('maintenance_type');
            $table->index('scheduled_date');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_maintenance_records');
    }
};
