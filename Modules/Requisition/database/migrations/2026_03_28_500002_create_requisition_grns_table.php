<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_grns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('grn_number')->unique();
            $table->foreignId('requisition_id')->nullable()->constrained('requisitions')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('received_by_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->date('received_date');
            $table->string('supplier_delivery_ref')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'accepted', 'partially_accepted', 'rejected'])->default('draft');
            $table->timestamps();
        });

        Schema::create('requisition_grn_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained('requisition_grns')->cascadeOnDelete();
            $table->foreignId('requisition_item_id')->nullable()->constrained('requisition_items')->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity_ordered', 10, 3);
            $table->decimal('quantity_received', 10, 3);
            $table->decimal('quantity_accepted', 10, 3);
            $table->decimal('quantity_rejected', 10, 3)->default(0);
            $table->string('rejection_reason')->nullable();
            $table->string('unit')->default('pcs');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_grn_lines');
        Schema::dropIfExists('requisition_grns');
    }
};